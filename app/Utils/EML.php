<?php

namespace App\Utils;

use App\Exceptions\CustomException;
use App\Services\Nabi;
use App\Utils\Constant;

class EML
{
    public static function buildHeader($subject = "", $string = "")
    {
        $header = file_get_contents(resource_path(Constant::HEADER_EML_PATH));
        $header = str_replace("TEMPLATE_CC", config('app.ccAddress'), $header);
        $header = str_replace("TEMPLATE_SUBJECT", $subject, $header);
        $header = str_replace("TEMPLATE_BODY", $string, $header);
        return $header;
    }

    public static function buildHeaderCID($content_id = array())
    {
        $content = "";

        for ($j = 0; $j < count($content_id); $j++) {
            $header_cid = file_get_contents(resource_path(Constant::HEADER_CID_EML_PATH));
            $header_cid = str_replace("TEMPLATE_CID", $content_id[$j], $header_cid);
            $content .= $header_cid;
        }
        $content .= '</html>';

        return $content;
    }

    public static function buildImage($content_id = array(), $path_image = "")
    {
        $content = "";
        $images = array_diff(scandir($path_image), array('.', '..'));

        if (!empty($images)) {
            for ($i = 0; $i < count($images); $i++) {
                $attach = file_get_contents(resource_path(Constant::CONTENT_IMAGE_EML_PATH));
                $attach = str_replace("TEMPLATE_ATTACH_TYPE", 'image/png', $attach);
                $attach = str_replace("TEMPLATE_ATTACH_DISPOSITION", 'inline', $attach);
                $attach = str_replace("TEMPLATE_ATTACH_FILENAME", $images[$i+2], $attach);
                $key_id = array_keys($content_id);
                $key_img = array_keys($images);
                if ($key_img[$i] == $key_id[$i] + 2) {
                    $attach = str_replace("TEMPLATE_ATTACH_ID", $content_id[$i], $attach);
                }
                $attach_file = file_get_contents($path_image.'/' .$images[$i+2]);
                $attach = str_replace("TEMPLATE_ATTACH_CONTENT", base64_encode($attach_file), $attach);

                $content .= $attach;
            }
        }

        return $content;
    }

    public static function addAttachment()
    {
        $content = "";
        $path_file = resource_path(Constant::ATTACHMENT_PATH);

        $files = array_diff(scandir($path_file), array('.', '..'));
        if (!empty($files)) {
            foreach ($files as $file) {
                $arr= explode(".", $file);
                $attach = file_get_contents(resource_path(Constant::CONTENT_ATTACHMENT_EML_PATH));
                if ($arr[1] == 'pdf') {
                    $attach = str_replace("TEMPLATE_ATTACH_TYPE", 'application/pdf', $attach);
                } elseif ($arr[1] == 'xlsx') {
                    $attach = str_replace("TEMPLATE_ATTACH_TYPE", 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $attach);
                }
                $attach = str_replace("TEMPLATE_ATTACH_DISPOSITION", 'attachment', $attach);
                $attach = str_replace("TEMPLATE_ATTACH_FILENAME", $file, $attach);
                $attach_file = file_get_contents($path_file.$file);
                $attach = str_replace("TEMPLATE_ATTACH_CONTENT", base64_encode($attach_file), $attach);

                $content .= $attach;
                File::deleteFile($path_file.$file);
            }
            $close_file = file_get_contents(resource_path(Constant::CLOSE_ATTACHMENT_EML_PATH));
            $content .= $close_file;
        }

        if (empty($files)) {
            $attach = file_get_contents(resource_path(Constant::NO_ATTACHMENT_EML_PATH));
            $content .= $attach;
        }

        return $content;
    }

    public static function buildContent($content, $shop = false, $path_image = false, $shops = false)
    {
        if ($shop) {
            $content_id = self::contentID($shop);
            if (!$content_id) {
                return false;
            }
            $header_cid = self::buildHeaderCID($content_id);
            $content .= $header_cid;

            $images = self::buildImage($content_id, $path_image);
            $content .= $images;
        }

        if ($shops) {
            foreach ($shops as $shop) {
                $content_id = self::contentID($shop);
                if (!$content_id) {
                    continue;
                }

                $nabi = new Nabi();
                $shopName = $nabi->sqlServer->getShopName($shop['c_code']);
                $shopName = $shopName ? $shopName : '';
                $c_codeShop = $shop['c_code'];

                if (count($shops) > 1) {
                    $header_multi = file_get_contents(resource_path(Constant::HEADER_MULTI_SHOP_EML_PATH));
                    $text = "<br> --------- <br> 【".$shopName."】$c_codeShop";
                    $header_multi = str_replace("TEMPLATE_MULTI", $text, $header_multi);
                    $content .= $header_multi;
                }

                $header_cid = EML::buildHeaderCID($content_id);
                $content .= $header_cid;
            }

            foreach ($shops as $shop) {
                $dir = resource_path(Constant::IMAGE_PATH.$shop['c_code']);
                $content_id = self::contentID($shop);
                if (!$content_id) {
                    continue;
                }
                $image = EML::buildImage($content_id, $dir);
                $content .= $image;
            }
        }
        return $content;
    }

    public static function exportEML($filename, $subject, $string, $shop = false, $path_image = false, $shops = false)
    {
        try {
            $handle = fopen(resource_path(Constant::EML_PATH.$filename), 'w');

            $content = file_get_contents(resource_path(Constant::EML_PATH.$filename));

            $header = self::buildHeader($subject, $string);
            $content .= $header;

            $content = self::buildContent($content, $shop, $path_image, $shops);
            if (!$content) {
                throw new CustomException("Don't build content of EML !");
            }

            $close_image = file_get_contents(resource_path(Constant::CLOSE_IMAGE_EML_PATH));
            $content .= $close_image;

            $files = self::addAttachment();
            $content .= $files;

            fwrite($handle, $content);

            fclose($handle);

            $pathFile = resource_path(Constant::EML_PATH.$filename);

            return $pathFile;
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return false;
        }
    }

    public static function contentID($shop)
    {
        try {
            $dir = resource_path(Constant::IMAGE_PATH.$shop['c_code']);
            if (!is_dir($dir)) {
                throw new CustomException("Dir shop ". $shop['c_code'] . " not found");
            }
            $images = array_diff(scandir($dir), array('.', '..'));
            $count = count($images);
            $contentIDs = array();
            for ($i = 1; $i <= $count; $i++) {
                array_push($contentIDs, $i.$shop['c_code']);
            }
            return $contentIDs;
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return false;
        }
    }
}
