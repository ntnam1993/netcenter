<?php

namespace App\Console\Commands;

use App\Services\Nabi;
use App\Services\PostGreSQL;
use App\Utils\Constant;
use App\Utils\CSV;
use App\Utils\EML;
use App\Utils\File;
use App\Utils\Helper;
use App\Utils\HTML;
use App\Utils\MailNet;
use Illuminate\Console\Command;
use App\Utils\Log;

class BuildEML extends Command
{
    const TYPE_BUILD_IMAGE = [
        self::TYPE_CV,
        self::TYPE_PV
    ];
    const TYPE_CV = 1;
    const TYPE_PV = 2;

    protected $signature = "build:eml";

    protected $description = "Build EML file";

    public function handle()
    {
        if (!is_dir(resource_path(Constant::IMAGE_PATH))) {
            mkdir(resource_path(Constant::IMAGE_PATH));
        }
        $log = new Log();
        $log->logInfo("RUN BATCH STARTED !");
        $checkDate = $this->postgreSql()->checkDateRunBatch();
        if (!$checkDate) {
            return;
        }

        $dataCSVClrepo = CSV::readCSVClrepo();
        if (!$dataCSVClrepo) {
            $log->logError("cannot read clrepo.csv");
            return;
        }
        $log->logInfo('Read done and delete clrepo.csv');

        $dataCSVIchioshiIcon = CSV::readCSVIchioshiIcon();
        if (!$dataCSVIchioshiIcon) {
            $log->logError("cannot read ichioshi_icon.csv");
            return;
        }
        $log->logInfo('Read done and delete ichioshi_icon.csv');

        $pathPDFTips = File::downloadPDFTips();
        if (!$pathPDFTips) {
            $log->logError("Cannot get tips PDF file .");
            return;
        }

        $quantityOrderEachDays = $this->nabi()->mySql->getQuantityOrderEachDay($log);
        if (!$quantityOrderEachDays) {
            $log->logError("Cannot execute query to get number of daily reservations for each c_code .");
            return;
        }

        $fairAndContentTypeEachDays = $this->nabi()->mySql->getFairAndContentTypeEachDay($log);
        if (!$fairAndContentTypeEachDays) {
            $log->logError("Cannot execute query to get daily fairs and their content types for each c_code .");
            return;
        }

        $shopGroups = $this->postgreSql()->getListShop();
        if (!$shopGroups) {
            return;
        }

        $dateReport = self::postgreSql()->getDateReportRunBatch();
        if (empty($dateReport)) {
            return;
        }

        Helper::varDump("Start Loop Shop : ...");
        foreach ($shopGroups as $shops) {
            $flagHasShopBuild = false;
            $groupingKey = $shops['grouping_key'];
            unset($shops['grouping_key']);
            $shopEmails = $shops['shop_emails'];
            unset($shops['shop_emails']);
            $staffNames = $shops['staff_names'];
            unset($shops['staff_names']);

            $index = 0;
            $shopsName = [];
            $shopName = '';
            $s_codes = [];
            $pathFileEMLs = [];

            $flagNotGetAttachment = false;
            foreach ($shops as $shop) {
                $log->logInfo($shop['c_code']. ' Processing started .');
                $index++;
                $flagNotBuildEmlSuccess = false;
                $s_code = $this->nabi()->sqlServer->getSCode($shop['c_code']);
                if (!$s_code) {
                    continue;
                }
                if (empty($dataCSVClrepo[$s_code])) {
                    $log->logInfo($s_code.' No have s_code in csv');
                    continue;
                }

                if (!empty($dataCSVClrepo[$s_code])) {
                    $log->infoProcess($shop, $s_code);
                }

                if (empty($dataCSVIchioshiIcon[$s_code])) {
                    $dataCSVIchioshiIcon[$s_code] = '';
                    $log->logInfo($s_code. ' No have s_code in CSVIchioshiIcon');
                }

                if (!empty($dataCSVIchioshiIcon[$s_code])) {
                    $log->infoProcess($shop, $s_code);
                }

                $pathFile = HTML::exportHTML(self::TYPE_BUILD_IMAGE, $shop, $dataCSVClrepo[$s_code], $dateReport, $dataCSVIchioshiIcon[$s_code], $quantityOrderEachDays, $fairAndContentTypeEachDays, $log);
                if (!$pathFile) {
                    continue;
                }

                $shopName = $this->nabi()->sqlServer->getShopName($shop['c_code']);
                if ($shopName) {
                    array_push($shopsName, $shopName);
                }
                if ($shop['is_client_report_needed'] == true) {
                    $pathExcel = File::downloadXLSX($s_code, $shopName);
                    if (!$pathExcel) {
                        $log->logError("cannot get client report corresponding to ".$shop['c_code']);
                        $flagNotGetAttachment = Helper::turnOnFlag();
                    }
                }

                if ($shop['is_competitor_sheet_needed'] == true) {
                    $pathPDF = File::downloadPDF($s_code, $shopName);
                    if (!$pathPDF) {
                        $log->logError("cannot get competitor sheet corresponding to ".$shop['c_code']);
                        $flagNotGetAttachment = Helper::turnOnFlag();
                    }
                }

                array_push($s_codes, $s_code);

                if (is_null($groupingKey)) {
                    $pathFileEMLS3 = '';
                    $fileName = File::getFileName($shopName, $s_code);
                    $pathFileEML = EML::exportEML($fileName.".eml", $fileName, '', $shop, $pathFile, false);
                    if ($pathFileEML) {
                        $pathFileEMLS3 = File::putEMLS3($pathFileEML, $shop, $s_code, $shopName, $log, false);
                    } else {
                        $flagNotBuildEmlSuccess = Helper::turnOnFlag();
                        continue;
                    }
                    if ($pathFileEMLS3) {
                        array_push($pathFileEMLs, $pathFileEMLS3);
                    }
                }
                $flagHasShopBuild = Helper::turnOnFlag();
                $log->logInfo($shop['c_code']. ' Processing completed .');
            }

            if (!is_null($groupingKey) && $flagHasShopBuild) {
                $pathFileEMLS3 = '';
                $fileName = File::getFileName($shopsName, $s_codes);
                $pathFileEML = EML::exportEML($fileName . ".eml", $fileName, '', false, false, $shops);
                if ($pathFileEML) {
                    $pathFileEMLS3 = File::putEMLS3($pathFileEML, $shops, $s_codes, $shopsName, $log, true);
                } else {
                    $flagNotBuildEmlSuccess = Helper::turnOnFlag();
                    continue;
                }
                if ($pathFileEMLS3) {
                    array_push($pathFileEMLs, $pathFileEMLS3);
                }
            }

            if ($flagNotGetAttachment === true || !count($pathFileEMLs)) {
                continue;
            }
            $pathZip = File::zipFromS3($pathFileEMLs, $staffNames, $groupingKey);

            if (isset($pathZip) && file_exists($pathZip)) {
                $log->logInfo('Push Zip to S3');
                $pathZipS3 = File::pushZipS3($pathZip);
                if (!$pathZipS3) {
                    $log->logWarnning('Path Zip S3 not found, path = '.$pathZipS3);
                    continue;
                }

                $log->logInfo('Send Mail to Shop');
                MailNet::sendMail($pathZipS3, $shopsName, $shopEmails);
                $log->logInfo('Send Mail to Shop'. implode(',', $shopEmails).' done !');
            }
        }
        File::deleteDir(resource_path(Constant::IMAGE_PATH));
        $log->logInfo('RUN BATCH DONE !');
    }

    public static function nabi()
    {
        return new Nabi();
    }

    public static function postgreSql()
    {
        return new PostGreSQL();
    }
}
