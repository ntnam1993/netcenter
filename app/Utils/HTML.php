<?php

namespace App\Utils;

use App\Console\Commands\BuildEML;
use App\Utils\Helper;
use App\Utils\PVImage;
use App\Utils\CVImage;
use App\Utils\FairFlowImage;
use App\Utils\DetailFlowImage;
use App\Utils\FurtherFlowImage;
use App\Utils\CommentFairListHTML;
use App\Utils\CommentFairDetailHTML;
use App\Utils\CommentReservationHTML;
use App\Utils\ReservationFlowImage;
use App\Utils\CommentFurtherHTML;
use App\Services\Zexy;
use App\Exceptions\CustomException;

class HTML
{
    const SIZE_IMAGE = [
        Constant::SIZE_IMAGE_CV => '850,225',
        Constant::SIZE_IMAGE_PV => '850,630',
        Constant::SIZE_IMAGE_PV_ONE_LINE => '850,590',

        Constant::SIZE_IMAGE_COMMENT_A_TOP_1 => '850,205',
        Constant::SIZE_IMAGE_COMMENT_A_TOP_2 => '850,280',
        Constant::SIZE_IMAGE_COMMENT_A_TOP_3 => '850,170',
        Constant::SIZE_IMAGE_COMMENT_A_PHOTO_GALLERY => '850,240',
        Constant::SIZE_IMAGE_COMMENT_A_PLAN_1 => '850,240',
        Constant::SIZE_IMAGE_COMMENT_A_PLAN_2 => '850,230',
        Constant::SIZE_IMAGE_COMMENT_A_PLAN_3 => '850,240',
        Constant::SIZE_IMAGE_COMMENT_A_PLAN_4 => '850,220',

        Constant::SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_1 => '850,250',
        Constant::SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_2 => '850,240',
        Constant::SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_3 => '850,250',
        Constant::SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_4 => '850,250',
        Constant::SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_5 => '850,250',
        Constant::SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_6 => '850,250',
        Constant::SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_7 => '850,280',
        Constant::SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_8 => '850,250',
        Constant::SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_9 => '850,280',
        Constant::SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_10 => '850,480',

        Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_1 => '850,250',
        Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_2 => '850,250',
        Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_3 => '850,250',
        Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_4 => '850,250',
        Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_5 => '850,250',
        Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_6 => '850,250',
        Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_7 => '850,250',
        Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_8 => '850,250',
        Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_9 => '850,250',
        Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_10 => '850,250',
        Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_12 => '850,250',
        Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_13 => '850,250',
        Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_14 => '850,250',
        Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_15 => '850,250',
        Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_16 => '850,250',
        Constant::SIZE_IMAGE_COMMENT_D_FURTHER_1 => '850,210',
        Constant::SIZE_IMAGE_COMMENT_D_FURTHER_2 => '850,260',
        Constant::SIZE_IMAGE_COMMENT_D_FURTHER_3 => '850,260',
        Constant::SIZE_IMAGE_COMMENT_D_FURTHER_4 => '850,230',

    ];

    const GOAL_TYPE_1 = 1;
    const GOAL_TYPE_2 = 2;
    const GOAL_TYPE_3 = 3;

    const SUMMARY_TYPE_1 = 1;
    const SUMMARY_TYPE_2 = 2;
    const SUMMARY_TYPE_3 = 3;

    const UMBRELLA = 'images/umbrella.png';
    const CLOUD = 'images/cloudy.png';
    const SUN = 'images/sun.png';

    public static function exportHTML($types, $shop, $dataCSVClrepo, $dateReport, $dataCSVIchioshiIcon, $quantityOrderEachDays, $fairAndContentTypeEachDays, $log)
    {
        try {
            list($goal_type, $pathHtmls) = self::getPathHtmls($types, $dataCSVClrepo, $shop, $dateReport);

            if (!$pathHtmls) {
                throw new CustomException("Don't have any path html match to build image !");
            }

            if (!count($pathHtmls) && empty($pathHtmls[Constant::SIZE_IMAGE_CV]) && empty($pathHtmls[Constant::SIZE_IMAGE_PV]) && empty($pathHtmls[Constant::SIZE_IMAGE_PV_ONE_LINE])) {
                throw new CustomException("Don't have any path html match to build image !");
            }

            $dir = resource_path(Constant::IMAGE_PATH . $shop['c_code']);

            if (!is_dir($dir)) {
                mkdir($dir);
            }

            $images = array_diff(scandir($dir), array('.', '..'));
            if (!is_array($images) || count($images)) {
                foreach ($images as $file) {
                    unlink($dir . '/' . $file);
                }
            }

            foreach ($pathHtmls as $key => $path) {
                $imageName = self::buildNameImageWithPath($path, $shop['c_code']);
                self::buildImage($key, $path, $imageName, $shop, $log, $goal_type);
                self::moveImage($imageName, $dir);
            }
            $zexy = new Zexy($shop['c_code']);
            $issueMonthBefore1Week = $dateReport->issue_month;

            self::buildCommentImagesA($dataCSVClrepo, $shop, $zexy, $log);

            self::buildCommentImagesB($dataCSVClrepo, $shop, $zexy, $dataCSVIchioshiIcon, $issueMonthBefore1Week, $quantityOrderEachDays, $fairAndContentTypeEachDays, $log);

            self::buildCommentImagesC($dataCSVClrepo, $shop, $zexy, $issueMonthBefore1Week, $quantityOrderEachDays, $fairAndContentTypeEachDays, $log);

            self::buildCommentImagesD($dataCSVClrepo, $shop, $zexy, $dataCSVIchioshiIcon, $issueMonthBefore1Week, $log);

            return resource_path(Constant::IMAGE_PATH . $shop['c_code']);
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return false;
        }
    }

    public static function checkIconSunCV($dataCSVClrepo, $goal_type)
    {
        $goal = CVImage::goal($dataCSVClrepo, $goal_type);
        if (!$goal) {
            return false;
        }
        $actual = CVImage::actual($dataCSVClrepo);
        if (!$actual) {
            return false;
        }
        $ratio = Helper::devision($actual, $goal);
        return ($ratio == CVImage::SUN_NUMBER_EQUAL || $ratio >= CVImage::SUN_NUMBER_BIGGER) ? true : false;
    }

    public static function checkIconUmbrella($dataCSVClrepo, $summary_type, $indexIcon)
    {
        $icons = PVImage::printIcon($dataCSVClrepo, $summary_type);
        $iconUmbrella = $icons[$indexIcon];
        return ($iconUmbrella == self::UMBRELLA);
    }

    //check condition icon in CV and fair_flow_result in PV
    public static function checkConditionBuildCommentsA($dataCSVClrepo, $goal_type, $summary_type)
    {
        return (!self::checkIconSunCV($dataCSVClrepo, $goal_type) && self::checkIconUmbrella($dataCSVClrepo, $summary_type, PVImage::INDEX_ICON20));
    }

    public static function checkConditionBuildCommentsB($dataCSVClrepo, $goal_type, $summary_type)
    {
        return (!self::checkIconSunCV($dataCSVClrepo, $goal_type) && self::checkIconUmbrella($dataCSVClrepo, $summary_type, PVImage::INDEX_ICON21));
    }

    public static function checkConditionBuildCommentsC($dataCSVClrepo, $goal_type, $summary_type)
    {
        return (!self::checkIconSunCV($dataCSVClrepo, $goal_type) && self::checkIconUmbrella($dataCSVClrepo, $summary_type, PVImage::INDEX_ICON22));
    }

    public static function buildCommentImagesA($dataCSVClrepo, $shop, $zexy, $log)
    {
        $dir = resource_path(Constant::IMAGE_PATH.$shop['c_code']);
        if (self::checkConditionBuildCommentsA($dataCSVClrepo, $shop['goal_type'], $shop['summary_type'])) {
            switch (true) {
                case FairFlowImage::buildCommentTop1($shop, $zexy):
                    $path = CommentFairListHTML::buildHtmlCommentATop1($shop);
                    self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_A_TOP_1, $path, $shop, $dir, $log);
                    break;
                case FairFlowImage::buildCommentTop2($shop):
                    $path = CommentFairListHTML::buildHtmlCommentATop2();
                    self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_A_TOP_2, $path, $shop, $dir, $log);
                    break;
                case FairFlowImage::buildCommentTop3($shop):
                    $path = CommentFairListHTML::buildHtmlCommentATop3();
                    self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_A_TOP_3, $path, $shop, $dir, $log);
                    break;
            }
            if (FairFlowImage::buildCommentPhotoGallery($shop, $zexy)) {
                $path = CommentFairListHTML::buildHtmlCommentAPhotoGallery();
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_A_PHOTO_GALLERY, $path, $shop, $dir, $log);
            }
            if (FairFlowImage::buildCommentPlan1($shop, $zexy)) {
                $path = CommentFairListHTML::buildHtmlCommentAPlan1($shop);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_A_PLAN_1, $path, $shop, $dir, $log);
            }
            if (FairFlowImage::buildCommentPlan2($shop, $zexy)) {
                $path = CommentFairListHTML::buildHtmlCommentAPlan2();
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_A_PLAN_2, $path, $shop, $dir, $log);
            }
            if (FairFlowImage::buildCommentPlan3($shop, $zexy)) {
                $path = CommentFairListHTML::buildHtmlCommentAPlan3($shop);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_A_PLAN_3, $path, $shop, $dir, $log);
            }
            if (FairFlowImage::buildCommentPlan4($shop, $zexy)) {
                $path = CommentFairListHTML::buildHtmlCommentAPlan4();
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_A_PLAN_4, $path, $shop, $dir, $log);
            }
        }
    }

    public static function buildCommentImagesB($dataCSVClrepo, $shop, $zexy, $dataCSVIchioshiIcon, $issueMonthBefore1Week, $quantityOrderEachDays, $fairAndContentTypeEachDays, $log)
    {
        $dir = resource_path(Constant::IMAGE_PATH.$shop['c_code']);
        if (self::checkConditionBuildCommentsB($dataCSVClrepo, $shop['goal_type'], $shop['summary_type'])) {
            $index = false;
            if (DetailFlowImage::buildCommentBFairDetail1($shop, $zexy)) {
                $path = CommentFairDetailHTML::buildHtmlCommentBFairDetail1();
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_1, $path, $shop, $dir, $log);
                self::updateFairDetailFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if (DetailFlowImage::buildCommentBFairDetail2($shop, $zexy)) {
                $path = CommentFairDetailHTML::buildHtmlCommentBFairDetail2();
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_2, $path, $shop, $dir, $log);
                self::updateFairDetailFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if ($dates = DetailFlowImage::buildCommentBFairDetail3($shop, $zexy)) {
                $path = CommentFairDetailHTML::buildHtmlCommentBFairDetail3($dates);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_3, $path, $shop, $dir, $log);
                self::updateFairDetailFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if (DetailFlowImage::buildCommentBFairDetail4($shop, $dataCSVIchioshiIcon)) {
                $path = CommentFairDetailHTML::buildHtmlCommentBFairDetail4();
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_4, $path, $shop, $dir, $log);
                self::updateFairDetailFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if ($dates = DetailFlowImage::buildCommentBFairDetail5($shop, $zexy)) {
                $path = CommentFairDetailHTML::buildHtmlCommentBFairDetail5($dates);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_5, $path, $shop, $dir, $log);
                self::updateFairDetailFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if ($dates = DetailFlowImage::buildCommentBFairDetail6($shop, $zexy)) {
                $path = CommentFairDetailHTML::buildHtmlCommentBFairDetail6($dates);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_6, $path, $shop, $dir, $log);
                self::updateFairDetailFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if ($dates = DetailFlowImage::buildCommentBFairDetail7($shop, $zexy)) {
                $path = CommentFairDetailHTML::buildHtmlCommentBFairDetail7($dates);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_7, $path, $shop, $dir, $log);
                self::updateFairDetailFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if ($dates = DetailFlowImage::buildCommentBFairDetail8($shop, $zexy)) {
                $path = CommentFairDetailHTML::buildHtmlCommentBFairDetail8($dates);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_8, $path, $shop, $dir, $log);
                self::updateFairDetailFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if ($dates = DetailFlowImage::buildCommentBFairDetail9($shop, $zexy)) {
                $path = CommentFairDetailHTML::buildHtmlCommentBFairDetail9($dates);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_9, $path, $shop, $dir, $log);
                self::updateFairDetailFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if (!$index && ($issueMonthBefore1Week == $shop['fair_detail_flow_last_commented_date'])) {
                $dataCommentB10 = DetailFlowImage::buildCommentBFairDetail10($shop, $quantityOrderEachDays, $fairAndContentTypeEachDays);
                if ($dataCommentB10) {
                    $path = CommentFairDetailHTML::buildHtmlCommentBFairDetail10($dataCommentB10);
                    self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_10, $path, $shop, $dir, $log);
                    if (!file_exists(resource_path(Constant::ATTACHMENT_PATH . Constant::PDF_TIPS_FILE_NAME))) {
                        copy(resource_path(Constant::ATTACHMENT_PDF_TIPS_PATH), resource_path(Constant::ATTACHMENT_PATH . Constant::PDF_TIPS_FILE_NAME));
                    }
                }
            }
        }
    }

    public static function buildCommentImagesC($dataCSVClrepo, $shop, $zexy, $issueMonthBefore1Week, $quantityOrderEachDays, $fairAndContentTypeEachDays, $log)
    {
        $dir = resource_path(Constant::IMAGE_PATH.$shop['c_code']);
        if (self::checkConditionBuildCommentsC($dataCSVClrepo, $shop['goal_type'], $shop['summary_type'])) {
            $index = false;
            if ($dates = ReservationFlowImage::buildCommentCReservation1($shop, $zexy)) {
                $path = CommentReservationHTML::buildHtmlCommentCReservation1($dates);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_1, $path, $shop, $dir, $log);
                self::updateReservationFlowFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if ($dates = ReservationFlowImage::buildCommentCReservation2($shop, $zexy)) {
                $path = CommentReservationHTML::buildHtmlCommentCReservation2($dates);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_2, $path, $shop, $dir, $log);
                self::updateReservationFlowFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if (ReservationFlowImage::buildCommentCReservation3($shop, $zexy)) {
                $path = CommentReservationHTML::buildHtmlCommentCReservation3();
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_3, $path, $shop, $dir, $log);
                self::updateReservationFlowFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if ($dates = ReservationFlowImage::buildCommentCReservation4($shop, $zexy)) {
                $path = CommentReservationHTML::buildHtmlCommentCReservation4($dates);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_4, $path, $shop, $dir, $log);
                self::updateReservationFlowFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if ($dates = ReservationFlowImage::buildCommentCReservation5($shop, $zexy)) {
                $path = CommentReservationHTML::buildHtmlCommentCReservation5($dates);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_5, $path, $shop, $dir, $log);
                self::updateReservationFlowFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if ($dates = ReservationFlowImage::buildCommentCReservation6($shop, $zexy)) {
                $path = CommentReservationHTML::buildHtmlCommentCReservation6($dates);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_6, $path, $shop, $dir, $log);
                self::updateReservationFlowFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if ($dates = ReservationFlowImage::buildCommentCReservation7($shop, $zexy)) {
                $path = CommentReservationHTML::buildHtmlCommentCReservation7($dates);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_7, $path, $shop, $dir, $log);
                self::updateReservationFlowFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if ($dates = ReservationFlowImage::buildCommentCReservation8($shop, $zexy)) {
                $path = CommentReservationHTML::buildHtmlCommentCReservation8($dates);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_8, $path, $shop, $dir, $log);
                self::updateReservationFlowFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if ($dates = ReservationFlowImage::buildCommentCReservation9($shop, $zexy)) {
                $path = CommentReservationHTML::buildHtmlCommentCReservation9($dates);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_9, $path, $shop, $dir, $log);
                self::updateReservationFlowFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if ($dates = ReservationFlowImage::buildCommentCReservation10($shop, $zexy)) {
                $path = CommentReservationHTML::buildHtmlCommentCReservation10($dates);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_10, $path, $shop, $dir, $log);
                self::updateReservationFlowFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if ($dates = ReservationFlowImage::buildCommentCReservation12($shop, $zexy)) {
                $path = CommentReservationHTML::buildHtmlCommentCReservation12($dates);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_12, $path, $shop, $dir, $log);
                self::updateReservationFlowFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if ($dates = ReservationFlowImage::buildCommentCReservation13($shop, $zexy)) {
                $path = CommentReservationHTML::buildHtmlCommentCReservation13($dates);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_13, $path, $shop, $dir, $log);
                self::updateReservationFlowFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if ($dates = ReservationFlowImage::buildCommentCReservation14($shop, $zexy)) {
                $path = CommentReservationHTML::buildHtmlCommentCReservation14($dates);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_14, $path, $shop, $dir, $log);
                self::updateReservationFlowFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if ($dates = ReservationFlowImage::buildCommentCReservation15($shop, $zexy)) {
                $path = CommentReservationHTML::buildHtmlCommentCReservation15($dates);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_C_RESERVATION_15, $path, $shop, $dir, $log);
                self::updateReservationFlowFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
                $index = true;
            }
            if (!$index && ($issueMonthBefore1Week == $shop['fair_detail_flow_last_commented_date']) && $dataCommentC16 = ReservationFlowImage::buildCommentCReservation16($shop, $quantityOrderEachDays, $fairAndContentTypeEachDays)) {
                $path = CommentReservationHTML::buildHtmlCommentCReservation16($dataCommentC16);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_10, $path, $shop, $dir, $log);
                if (!file_exists(resource_path(Constant::ATTACHMENT_PATH.Constant::PDF_TIPS_FILE_NAME))) {
                    copy(resource_path(Constant::ATTACHMENT_PDF_TIPS_PATH), resource_path(Constant::ATTACHMENT_PATH.Constant::PDF_TIPS_FILE_NAME));
                }
            }
        }
    }

    public static function buildCommentImagesD($dataCSVClrepo, $shop, $zexy, $dataCSVIchioshiIcon, $issueMonthBefore1Week, $log)
    {
        $dir = resource_path(Constant::IMAGE_PATH.$shop['c_code']);
        if (self::checkIconSunCV($dataCSVClrepo, $shop['goal_type'])) {
            if (FurtherFlowImage::buildCommentDFurther1($shop, $zexy)) {
                $path = CommentFurtherHTML::buildHtmlCommentDFurther1($shop);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_D_FURTHER_1, $path, $shop, $dir, $log);
            }
            if (FurtherFlowImage::buildCommentDFurther2($shop, $zexy)) {
                $path = CommentFurtherHTML::buildHtmlCommentDFurther2();
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_D_FURTHER_2, $path, $shop, $dir, $log);
            }
            if ($dates = FurtherFlowImage::buildCommentDFurther3($shop, $zexy)) {
                $path = CommentFurtherHTML::buildHtmlCommentDFurther3($dates);
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_D_FURTHER_3, $path, $shop, $dir, $log);
            }
            if (FurtherFlowImage::buildCommentDFurther4($shop, $dataCSVIchioshiIcon)) {
                $path = CommentFurtherHTML::buildHtmlCommentDFurther4();
                self::buildImageFromHtml(Constant::SIZE_IMAGE_COMMENT_D_FURTHER_4, $path, $shop, $dir, $log);
                self::updateFairDetailFlowLastCommentDate($shop['c_code'], $issueMonthBefore1Week);
            }
        }
    }

    public static function buildImageFromHtml($key, $path, $shop, $dir, $log)
    {
        $imageName = self::buildNameImageWithPath($path, $shop['c_code']);
        self::buildImage($key, $path, $imageName, $shop, $log);
        self::moveImage($imageName, $dir);
    }

    //@SuppressWarnings(PHPMD)
    public static function buildNameImageWithPath($path, $c_code)
    {
        $path_name =  explode('.', basename($path))[0];
        switch (true) {
            case $path_name == Constant::IMAGE_CV_NAME:
                return self::fileNameImage($c_code, Constant::IMAGE_CV_NAME);
            case $path_name == Constant::IMAGE_PV_NAME:
                return self::fileNameImage($c_code, Constant::IMAGE_PV_NAME);
            case $path_name == Constant::IMAGE_COMMENT_A_TOP_1_NAME:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_A_TOP_1_NAME);
            case $path_name == Constant::IMAGE_COMMENT_A_TOP_2_NAME:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_A_TOP_2_NAME);
            case $path_name == Constant::IMAGE_COMMENT_A_TOP_3_NAME:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_A_TOP_3_NAME);
            case $path_name == Constant::IMAGE_COMMENT_A_PHOTO_GALLERY_NAME:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_A_PHOTO_GALLERY_NAME);
            case $path_name == Constant::IMAGE_COMMENT_A_PLAN_1_NAME:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_A_PLAN_1_NAME);
            case $path_name == Constant::IMAGE_COMMENT_A_PLAN_2_NAME:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_A_PLAN_2_NAME);
            case $path_name == Constant::IMAGE_COMMENT_A_PLAN_3_NAME:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_A_PLAN_3_NAME);
            case $path_name == Constant::IMAGE_COMMENT_A_PLAN_4_NAME:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_A_PLAN_4_NAME);
            case $path_name == Constant::IMAGE_COMMENT_B_FAIR_DETAIL_1:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_B_FAIR_DETAIL_1);
            case $path_name == Constant::IMAGE_COMMENT_B_FAIR_DETAIL_2:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_B_FAIR_DETAIL_2);
            case $path_name == Constant::IMAGE_COMMENT_B_FAIR_DETAIL_3:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_B_FAIR_DETAIL_3);
            case $path_name == Constant::IMAGE_COMMENT_B_FAIR_DETAIL_4:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_B_FAIR_DETAIL_4);
            case $path_name == Constant::IMAGE_COMMENT_B_FAIR_DETAIL_5:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_B_FAIR_DETAIL_5);
            case $path_name == Constant::IMAGE_COMMENT_B_FAIR_DETAIL_6:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_B_FAIR_DETAIL_6);
            case $path_name == Constant::IMAGE_COMMENT_B_FAIR_DETAIL_7:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_B_FAIR_DETAIL_7);
            case $path_name == Constant::IMAGE_COMMENT_B_FAIR_DETAIL_8:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_B_FAIR_DETAIL_8);
            case $path_name == Constant::IMAGE_COMMENT_B_FAIR_DETAIL_9:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_B_FAIR_DETAIL_9);
            case $path_name == Constant::IMAGE_COMMENT_B_FAIR_DETAIL_10:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_B_FAIR_DETAIL_10);
            case $path_name == Constant::IMAGE_COMMENT_C_RESERVATION_1:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_C_RESERVATION_1);
            case $path_name == Constant::IMAGE_COMMENT_C_RESERVATION_2:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_C_RESERVATION_2);
            case $path_name == Constant::IMAGE_COMMENT_C_RESERVATION_3:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_C_RESERVATION_3);
            case $path_name == Constant::IMAGE_COMMENT_C_RESERVATION_4:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_C_RESERVATION_4);
            case $path_name == Constant::IMAGE_COMMENT_C_RESERVATION_5:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_C_RESERVATION_5);
            case $path_name == Constant::IMAGE_COMMENT_C_RESERVATION_6:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_C_RESERVATION_6);
            case $path_name == Constant::IMAGE_COMMENT_C_RESERVATION_7:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_C_RESERVATION_7);
            case $path_name == Constant::IMAGE_COMMENT_C_RESERVATION_8:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_C_RESERVATION_8);
            case $path_name == Constant::IMAGE_COMMENT_C_RESERVATION_9:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_C_RESERVATION_9);
            case $path_name == Constant::IMAGE_COMMENT_C_RESERVATION_10:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_C_RESERVATION_10);
            case $path_name == Constant::IMAGE_COMMENT_C_RESERVATION_12:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_C_RESERVATION_12);
            case $path_name == Constant::IMAGE_COMMENT_C_RESERVATION_13:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_C_RESERVATION_13);
            case $path_name == Constant::IMAGE_COMMENT_C_RESERVATION_14:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_C_RESERVATION_14);
            case $path_name == Constant::IMAGE_COMMENT_C_RESERVATION_15:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_C_RESERVATION_15);
            case $path_name == Constant::IMAGE_COMMENT_C_RESERVATION_16:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_C_RESERVATION_16);
            case $path_name == Constant::IMAGE_COMMENT_D_FURTHER_1:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_D_FURTHER_1);
            case $path_name == Constant::IMAGE_COMMENT_D_FURTHER_2:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_D_FURTHER_2);
            case $path_name == Constant::IMAGE_COMMENT_D_FURTHER_3:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_D_FURTHER_3);
            case $path_name == Constant::IMAGE_COMMENT_D_FURTHER_4:
                return self::fileNameImage($c_code, Constant::IMAGE_COMMENT_D_FURTHER_4);
        }
        return '';
    }

    public static function fileNameImage($c_code, $imageType)
    {
        return $c_code . $imageType . '.png';
    }

    public static function buildHtmlCV($dataCSVClrepo, $shop, $dateReport)
    {
        try {
            $start_date = $dateReport->start_date;
            $end_date = $dateReport->end_date;
            $goal_type = $shop['goal_type'];

            $handle = fopen(resource_path(Constant::CV_HTML_PATH), 'w');

            $content = file_get_contents(resource_path(Constant::CV_LAYOUT_PATH));

            $content = str_replace("START_DATE", date("n/j", strtotime($start_date)), $content);
            $content = str_replace("END_DATE", date("n/j", strtotime($end_date)), $content);

            $goal = CVImage::goal($dataCSVClrepo, $goal_type);
            if ($goal === false) {
                throw new CustomException('No goal build CV html for shop '.$shop['c_code']);
            }

            $actual = CVImage::actual($dataCSVClrepo);
            if ($actual === false) {
                throw new CustomException('No actual build CV html for shop '.$shop['c_code']);
            }

            $ratio = Helper::devision($actual, $goal);
            $content = CVImage::insertIcon("ICON", $ratio, $actual, $goal, $content);
            $content = CVImage::insertAchievement($ratio, $content);

            $content = CVImage::insertMessage($goal_type, $content);
            $content = str_replace("GOAL", $goal, $content);
            $content = str_replace("ACTUAL", $actual, $content);

            if (!$content) {
                return false;
            }

            fwrite($handle, $content);

            fclose($handle);

            return resource_path(Constant::CV_HTML_PATH);
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return false;
        }
    }

    public static function buildHtmlPV($dataCSVClrepo, $shop)
    {
        try {
            $goal_type = $shop['goal_type'];
            $summary_type = $shop['summary_type'];

            $results = PVImage::results($dataCSVClrepo);
            if (!count($results)) {
                throw new CustomException('No result build PV html for shop '.$shop['c_code']);
            }

            $avgs = PVImage::avgs($dataCSVClrepo);
            if (!count($avgs)) {
                throw new CustomException('No avg build PV html for shop '.$shop['c_code']);
            }

            $actuals = PVImage::actuals($dataCSVClrepo);
            if (!count($actuals)) {
                throw new CustomException('No actual build PV html for shop '.$shop['c_code']);
            }

            $stds = PVImage::stds($dataCSVClrepo, $summary_type);
            if (!count($stds)) {
                throw new CustomException('No std build PV html for shop '.$shop['c_code']);
            }

            $curs = PVImage::curs($dataCSVClrepo);
            if (!count($curs)) {
                throw new CustomException('No cur build PV html for shop '.$shop['c_code']);
            }

            $flowAvgs = PVImage::flowAvgs($dataCSVClrepo);
            if (!count($flowAvgs)) {
                throw new CustomException('No flowAvg build PV html for shop '.$shop['c_code']);
            }

            $flowActuals = PVImage::flowActuals($dataCSVClrepo);
            if (!count($flowActuals)) {
                throw new CustomException('No flowActual build PV html for shop '.$shop['c_code']);
            }

            $handle = fopen(resource_path(Constant::PV_HTML_PATH), 'w');

            $content = file_get_contents(resource_path(Constant::PV_LAYOUT_PATH));

            $content = PVImage::insertCriteria($goal_type, $content);

            for ($i = 0; $i < count($avgs); $i++) {
                $content = str_replace("VALUE1".$i, $avgs[$i] + 0, $content);
                $content = str_replace("VALUE2".$i, $actuals[$i] + 0, $content);
                $content = PVImage::printResult($results[$i], "RESULT".$i, $content);
            }

            $content = PVImage::printFlowResult($dataCSVClrepo, $summary_type, $content);

            for ($i = 0; $i < count($stds); $i++) {
                $content = str_replace("STD".$i, $stds[$i] + 0, $content);
                $content = str_replace("CUR".$i, $curs[$i] + 0, $content);
                $content = str_replace("AVG".$i, $flowAvgs[$i] + 0, $content);
                $content = str_replace("ACTUAL".$i, $flowActuals[$i] + 0, $content);
            }
            list($sizePV, $content) = PVImage::insertConclusion($dataCSVClrepo, $goal_type, $content);
            if (!$content) {
                return [
                    $goal_type,
                    $sizePV,
                    resource_path(Constant::PV_HTML_PATH)
                ];
            }

            fwrite($handle, $content);

            fclose($handle);

            return [
                $goal_type,
                $sizePV,
                resource_path(Constant::PV_HTML_PATH)
            ];
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return [
                false, false, false
            ];
        }
    }

    public static function buildImage($key, $path, $imageName, $shop, $log, $goal_type = null)
    {
        $sizeImage = self::SIZE_IMAGE[$key];
        if ($goal_type && ($key == Constant::SIZE_IMAGE_PV || $key == Constant::SIZE_IMAGE_PV_ONE_LINE) && ($goal_type == self::GOAL_TYPE_1 || $goal_type == self::GOAL_TYPE_2)) {
            list($width, $height) = explode(',', HTML::SIZE_IMAGE[$key]);
            $height += Constant::HEIGHT_ADDED_WHEN_GOAL_TYPE_IS2_AND_IS3;
            $sizeImage = "$width,$height";
        }
        $cmd = 'google-chrome --headless --disable-gpu --no-sandbox --screenshot='.$imageName.' --window-size='.$sizeImage.' '.$path;
        shell_exec($cmd);
        $log->logInfo($shop['c_code']." Written to file ".$imageName);
    }

    public static function moveImage($imageName, $dir)
    {
        $pathFile = $dir.'/'.$imageName;
        $cmd = 'mv '.$imageName.' '.$pathFile;
        shell_exec($cmd);
    }

    public static function getPathHtmls($types, $dataCSVClrepo, $shop, $dateReport)
    {
        try {
            $pathHtml = [];
            foreach ($types as $type) {
                if ($type == Constant::SIZE_IMAGE_CV) {
                    $pathHtml[Constant::SIZE_IMAGE_CV] = self::buildHtmlCV($dataCSVClrepo, $shop, $dateReport);
                    if (!$pathHtml[Constant::SIZE_IMAGE_CV]) {
                        throw new CustomException("No build CV html for shop ".$shop['c_code']);
                    }
                }
                if ($type == Constant::SIZE_IMAGE_PV) {
                    list($goal_type, $size, $pathHtmlPV) = self::buildHtmlPV($dataCSVClrepo, $shop);
                    if (!$pathHtmlPV) {
                        throw new CustomException("No build PV html for shop ".$shop['c_code']);
                    }
                    $pathHtml[$size] = $pathHtmlPV;
                }
            }

            return [
                $goal_type,
                $pathHtml
                ];
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return [
                false, false
            ];
        }
    }

    private static function updateFairDetailFlowLastCommentDate($c_code, $issueMonthBefore1Week)
    {
        BuildEML::postgreSql()->updateFairDetailFlowLastCommentDate($c_code, $issueMonthBefore1Week);
    }

    private static function updateReservationFlowFlowLastCommentDate($c_code, $issueMonthBefore1Week)
    {
        BuildEML::postgreSql()->updateReservationFlowFlowLastCommentDate($c_code, $issueMonthBefore1Week);
    }
}
