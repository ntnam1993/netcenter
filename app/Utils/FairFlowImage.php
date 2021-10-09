<?php

namespace App\Utils;

use App\Console\Commands\BuildEML;

class FairFlowImage
{
    const PHOTO_GALLERY = 20;

    //build 1 in 3 template in top
    public static function buildCommentTop1($shop, $zexy): bool
    {
        return (!$shop['is_a1_not_needed'] && (self::isOkKeisaiStartDate($zexy, $shop) || $zexy->checkStatusCodeAutoLink()));
    }

    public static function buildCommentTop2($shop)
    {
        return (!$shop['is_a2_not_needed'] && self::checkIssueMonthWithFairFistFlowLastCommentedDateIsDifference($shop));
    }

    public static function buildCommentTop3($shop): bool
    {
        return (!$shop['is_a3_not_needed'] && self::checkIssueMonthWithFairFistFlowLastCommentedDateIsSame($shop));
    }

    private static function isOkKeisaiStartDate($zexy, $shop): bool
    {
        $keisai_start_date = $zexy->getKeisaiStartDateFromArrival();
        $a1Threshold = $shop['a1_threshold'];
        $dateA1Threshold = $zexy->modifyDateFromNow("-$a1Threshold");
        return (!$keisai_start_date || ($keisai_start_date < $dateA1Threshold));
    }

    private static function checkIssueMonthWithFairFistFlowLastCommentedDateIsDifference($shop): bool
    {
        $issueMonth = BuildEML::postgreSql()->getDateReportRunBatch();
        if (empty($issueMonth)) {
            return false;
        }

        if ($shop['fair_list_flow_last_commented_date'] == null || $shop['fair_list_flow_last_commented_date'] != $issueMonth->issue_month) {
            BuildEML::postgreSql()->updateFairListFlowLastCommentDate($shop['c_code'], $issueMonth->issue_month);
            return true;
        }
        return false;
    }

    private static function checkIssueMonthWithFairFistFlowLastCommentedDateIsSame($shop): bool
    {
        $issueMonth = BuildEML::postgreSql()->getDateReportRunBatch();
        if (empty($issueMonth)) {
            return false;
        }
        return ($shop['fair_list_flow_last_commented_date'] == $issueMonth->issue_month);
    }

    //build template 4
    public static function buildCommentPhotoGallery($shop, $zexy): bool
    {
        return (!$shop['is_a4_not_needed'] && $zexy->getQuantityTotalPhotoGallery() < self::PHOTO_GALLERY);
    }

    //build template 5
    public static function buildCommentPlan1($shop, $zexy): bool
    {
        return (!$shop['is_a5_not_needed'] && $zexy->checkDateA5ThresHold($shop['a5_threshold']));
    }

    //build template 6
    public static function buildCommentPlan2($shop, $zexy): bool
    {
        return (!$shop['is_a6_not_needed'] && $zexy->checkDateA6ThresHold() == true);
    }

    //build template 7
    public static function buildCommentPlan3($shop, $zexy): bool
    {
        return (!$shop['is_a7_not_needed'] && $zexy->checkDateA7ThresHold($shop['a7_threshold']) == true);
    }

    //build template 8
    public static function buildCommentPlan4($shop, $zexy): bool
    {
        return (!$shop['is_a8_not_needed'] && $zexy->checkDateA8ThresHold() == true);
    }
}
