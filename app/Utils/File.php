<?php

namespace App\Utils;

use App\Console\Commands\BuildEML;
use App\Exceptions\CustomException;
use App\Utils\Helper;
use Carbon\Carbon;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;

class File
{
    public static function storeFile($pathS3, $pathLocal, $diskBucket = null)
    {
        try {
            $diskBucket = $diskBucket ?? 's3';
            $s3 = \Storage::disk($diskBucket);
            $s3->put($pathS3, fopen($pathLocal, 'r+'), 'public');
            $url = $s3->url($pathS3);
            unlink($pathLocal);
            return $url;
        } catch (CustomException $e) {
            $e::dumpAndLog($e->getMessage());
        }
    }

    public static function createPathS3($pathFile)
    {
        $explode = explode('/', $pathFile);
        return Carbon::now()->format('YmdHis').end($explode);
    }

    public static function dirNameEML($shop, $multipleShop = false)
    {
        $shop = !$multipleShop ? $shop : $shop[0];
        $staffNames = BuildEML::postgreSql()->getStaffName([$shop['email1'], $shop['email2'], $shop['email3']]);
        $dirname = (count($staffNames) && implode('_', array_filter($staffNames)) == '') ? 'test' : implode('_', array_filter($staffNames));
        return $dirname;
    }

    public static function createS3EML($shop, $s_code, $shopName, $multipleShop)
    {
        $dirName = self::dirNameEML($shop, $multipleShop);
        $fileName = self::getFileName($shopName, $s_code).'.eml';
        $path = date('Ymd').'/'.$dirName.'/'.$fileName;
        return $path;
    }

    public static function putEMLS3($pathFileLocal, $shop, $s_code, $shopName, $log, $multipleShop = false)
    {
        $pathFileS3 = self::createS3EML($shop, $s_code, $shopName, $multipleShop);
        $storeFile = self::storeFile($pathFileS3, $pathFileLocal, config('app.s3DiskWithBuckettEml'));
        $shop = !$multipleShop ? $shop : $shop[0];
        $log->logInfo($shop['c_code'].' '.$storeFile);
        return $pathFileS3;
    }

    public static function pushZipS3($pathFile)
    {
        return self::storeFile(date('Ymd').'/'.basename($pathFile), $pathFile, config('app.s3DiskWithBucketZip'));
    }

    public static function deleteDir($dirPath)
    {
        self::deleteFiles($dirPath);
        rmdir($dirPath);
    }

    public static function deleteFiles($dirPath)
    {
        try {
            if (! is_dir($dirPath)) {
                throw new CustomException("$dirPath must be a directory");
            }
            if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
                $dirPath .= '/';
            }
            $files = glob($dirPath . '*', GLOB_MARK);
            foreach ($files as $file) {
                is_dir($file) ? self::deleteDir($file) : unlink($file);
            }
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
        }
    }

    public static function deleteFile($pathFile)
    {
        unlink($pathFile);
    }

    public static function deleteFileFromS3($pathFile, $diskBucket)
    {
        $diskBucket = $diskBucket ?? 's3';
        \Storage::disk($diskBucket)->delete($pathFile);
    }

    public static function connectS3()
    {
        $s3 = new S3Client([
            'region' => env('AWS_DEFAULT_REGION'),
            'version' => 'latest'
        ]);

        $s3->registerStreamWrapper();
        return $s3;
    }

    public static function downloadFile($bucket, $url)
    {
        self::connectS3();
        $url = 's3://'.$bucket.$url;
        Helper::varDump('Downloading '.$url.'...');

        if (!file_exists($url)) {
            throw new CustomException('No file in '.$url);
        }

        $file_name = basename($url);

        $file = file_put_contents($file_name, file_get_contents($url));
        if (!$file) {
            return false;
        }
        return $file;
    }

    public static function moveFile($file_name, $path)
    {
        if (!rename($file_name, $path)) {
            throw new CustomException('Rename path:'. $file_name.' to path:'.$path.' fail !');
        }
        return $path;
    }

    public static function downloadCSV($url)
    {
        try {
            self::downloadFile(config('app.bucketData'), $url);
            $file_name = basename($url);
            return self::moveFile($file_name, resource_path(Constant::CSV_PATH.$file_name));
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return false;
        }
    }

    public static function downloadXLSX($s_code, $shopName)
    {
        $url = Constant::getURLXLSX($s_code);
        try {
            self::downloadFile(config('app.bucketData'), $url);
            $pathXLXS = self::moveFile(basename($url), resource_path(Constant::ATTACHMENT_PATH.$s_code.'_'.$shopName.'_'.date('YmdHis').'.xlsx'));
            self::deleteFileFromS3($url, config('app.s3DiskWithBucketData'));
            return $pathXLXS;
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return false;
        }
    }

    public static function downloadPDF($s_code, $shopName)
    {
        $url = Constant::getURLPDF($s_code);
        try {
            self::downloadFile(config('app.bucketData'), $url);
            $pathPDF = self::moveFile(basename($url), resource_path(Constant::ATTACHMENT_PATH.'競合情報シート_'.$s_code.'_'.$shopName.'_'.date('YmdHis').'.pdf'));
            self::deleteFileFromS3($url, config('app.s3DiskWithBucketData'));
            return $pathPDF;
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return false;
        }
    }

    public static function downloadPDFTips()
    {
        $url = Constant::getURLPDFTips();
        try {
            self::downloadFile(config('app.bucketData'), $url);
            $file_name = basename($url);
            $pathPDFTips = self::moveFile($file_name, resource_path(Constant::ATTACHMENT_PDF_TIPS_PATH));
            return $pathPDFTips;
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return false;
        }
    }

    public static function zipFromS3($file_names, $staffNames, $groupingKey)
    {
        $date = Carbon::now()->format('YmdHis');
        $fileName = $groupingKey ? implode("_", $staffNames).'_'.$groupingKey : implode("_", $staffNames);
        $zip_name = $fileName.'_'.$date.'.zip';
        $zip = new Filesystem(new ZipArchiveAdapter(resource_path($zip_name)));

        if (is_array($file_names)) {
            foreach ($file_names as $file_name) {
                if (!Storage::disk(config('app.s3DiskWithBuckettEml'))->exists($file_name)) {
                    Helper::varDump('Not found EML file, path = '.$fileName);
                    continue;
                }
                $file_content = Storage::disk(config('app.s3DiskWithBuckettEml'))->get($file_name);
                $zip->put($file_name, $file_content);
            }
        }

        $zip->getAdapter()->getArchive()->close();

        $pathZip = resource_path($zip_name);

        return $pathZip;
    }

    public static function getFileName($shopName, $s_code)
    {
        if (is_array($s_code)) {
            if (count($s_code) != 1) {
                $shopInfo = "(".implode(',', $s_code).")";
            }
            if (count($s_code) == 1) {
                $shopInfo = "【".$shopName[0]."】(".implode(',', $s_code).")";
            }
        }
        if (!is_array($s_code)) {
            $shopInfo = "【".$shopName."】($s_code)";
        }

        return mb_convert_encoding("週次XYNET改善のご提案" . $shopInfo, "utf-8", "auto");
    }
}
