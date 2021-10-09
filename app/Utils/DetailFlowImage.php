<?php

namespace App\Utils;

use App\Utils\Helper;
use App\Console\Commands\BuildEML;

class DetailFlowImage
{
    const KEY_CHECK_REMINDER_CD = "03";

    public static function buildCommentBFairDetail1($shop, $zexy): bool
    {
        return !$shop['is_b1_not_needed'] && Helper::checkHoldDate($zexy);
    }

    public static function buildCommentBFairDetail2($shop, $zexy): bool
    {
        return !$shop['is_b2_not_needed'] && self::checkFairNameWithHoldDate($zexy);
    }

    public static function buildCommentBFairDetail3($shop, $zexy)
    {
        return $shop['is_b3_not_needed'] ? false : self::checkRemainderCd($zexy);
    }

    public static function buildCommentBFairDetail4($shop, $dataCSVIchioshiIcon): bool
    {
        return !$shop['is_b4_not_needed'] && !empty($dataCSVIchioshiIcon) && (($dataCSVIchioshiIcon['available_number'] - $dataCSVIchioshiIcon['used_number']) > 0);
    }

    public static function buildCommentBFairDetail5($shop, $zexy)
    {
        return $shop['is_b5_not_needed'] ? false : self::checkContentCd($zexy);
    }

    public static function buildCommentBFairDetail6($shop, $zexy)
    {
        return $shop['is_b6_not_needed'] ? false : self::isFairNameWIthRegexB6($zexy);
    }

    public static function buildCommentBFairDetail7($shop, $zexy)
    {
        return $shop['is_b7_not_needed'] ? false : self::isFairNameWIthRegexB7($zexy);
    }

    public static function buildCommentBFairDetail8($shop, $zexy)
    {
        return $shop['is_b8_not_needed']? false : self::isFairNameWIthRegexB8($zexy);
    }

    public static function buildCommentBFairDetail9($shop, $zexy)
    {
        return $shop['is_b9_not_needed'] ? false : self::checkTours($shop['c_code'], $zexy);
    }

    public static function buildCommentBFairDetail10($shop, $quantityOrderEachDays, $fairAndContentTypeEachDays)
    {
        return $shop['is_b10_not_needed'] ? false : Helper::calculatorCValue($quantityOrderEachDays, $shop, $fairAndContentTypeEachDays);
    }

    private static function checkRemainderCd($zexy)
    {
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeday();
        if (!count((array)$clientFairs)) {
            return false;
        }
        $holdDates = array();
        foreach ($clientFairs as $clientFair) {
            if (!empty($clientFair->remainder_cd) && $clientFair->remainder_cd == self::KEY_CHECK_REMINDER_CD) {
                array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
            }
        }
        asort($holdDates);
        return count($holdDates) ? $holdDates : false;
    }

    private static function checkFairNameWithHoldDate($zexy): bool
    {
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeDayWithOnlyStaturdaySunDayHoliday();
        if (!count((array)$clientFairs) || !$clientFairs) {
            return false;
        }
        $holdDates = array();
        foreach ($clientFairs as $clientFair) {
            if (!empty($clientFair->fair_nm) && count($clientFair->fair_nm)) {
                $fair_nm = (array)$clientFair->fair_nm;
                $holdDates[$fair_nm[0]][] = $zexy->formatDayForObject($clientFair->hold_date);
            }
        }
        if (!count($holdDates)) {
            return false;
        }
        foreach ($holdDates as $key => $values) {
            asort($holdDates[$key]);
            for ($i = 0; $i < (count($values) - 1); $i++) {
                for ($j = $i + 1; $j < count($values); $j++) {
                    if (abs(strtotime($values[$i]) - strtotime($values[$j])) == Constant::SECOND_IN_ONE_DAY) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    private static function checkContentCd($zexy)
    {
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeDayWithOnlyStaturdaySunDayHoliday();
        if (!count((array)$clientFairs)) {
            return false;
        }
        $holdDates = array();
        foreach ($clientFairs as $clientFair) {
            $fairNM = self::regexCheckFairNameGetContentCd($clientFair->fair_nm);
            if ($fairNM) {
                if (empty($clientFair->fair_tkch_list) || empty($clientFair->fair_tkch_list->fair_tkch)) {
                    array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
                } elseif (!empty($clientFair->fair_tkch_list) || !empty($clientFair->fair_tkch_list->fair_tkch)) {
                    $fairTkchCdLists = $clientFair->fair_tkch_list->fair_tkch;
                    $fair_tkch_cds = [];
                    foreach ($fairTkchCdLists as $fairTkchCdList) {
                        array_push($fair_tkch_cds, $fairTkchCdList->fair_tkch_cd);
                    }
                    if (!in_array($fairNM, $fair_tkch_cds)) {
                        array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
                    }
                }
            }
        }

        if (!count($holdDates)) {
            return false;
        }

        asort($holdDates);
        return count($holdDates) ? $holdDates : false;
    }

    private static function isFairNameWIthRegexB6($zexy)
    {
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeDayWithOnlyStaturdaySunDayHoliday();
        if (!count((array)$clientFairs)) {
            return false;
        }
        $holdDates = array();
        foreach ($clientFairs as $clientFair) {
            if (self::regexCheckFairNameB6($clientFair->fair_nm)) {
                array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
            }
        }
        asort($holdDates);
        return count($holdDates) ? $holdDates : false;
    }

    private static function isFairNameWIthRegexB7($zexy)
    {
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeday();
        if (!count((array)$clientFairs)) {
            return false;
        }

        $holdDates = array();
        foreach ($clientFairs as $clientFair) {
            if (self::regexCheckFairNameB7($clientFair->fair_nm)) {
                array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
            }
        }
        asort($holdDates);
        return count($holdDates) ? $holdDates : false;
    }

    private static function isFairNameWIthRegexB8($zexy)
    {
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeday();
        if (!count((array)$clientFairs)) {
            return false;
        }

        $holdDates = array();
        foreach ($clientFairs as $clientFair) {
            if (self::regexCheckFairNameB8($clientFair->fair_nm)) {
                array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
            }
        }
        asort($holdDates);
        return count($holdDates) ? $holdDates : false;
    }

    private static function getTours($c_code, $zexy)
    {
        $tours = array();
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeDayWithOnlyStaturdaySunDayHolidayCompetitorCCode($c_code);
        if (!count((array)$clientFairs)) {
            return false;
        }
        foreach ($clientFairs as $clientFair) {
            $clientFairWithProductCd = $zexy->getClientFairWithProductCdCompetitorCCode($clientFair->product_cd, $c_code);
            if (empty($clientFairWithProductCd) || empty($clientFairWithProductCd->tour_flg) || $clientFairWithProductCd->tour_flg == false) {
                $tours[$zexy->formatDayForObject($clientFair->hold_date)] = 1;
            } elseif ($clientFairWithProductCd->tour_flg == true) {
                $countTour = 0;
                foreach ($clientFairWithProductCd->tour_list as $tourList) {
                    $countTour += count($tourList) ? count($tourList) : 0;
                }
                $countTour = ($countTour == 0) ? 1 : $countTour;
                $tours[$zexy->formatDayForObject($clientFair->hold_date)] = $countTour;
            }
        }
        return $tours;
    }

    private static function getToursCompetitorCCode($c_code, $zexy)
    {
        $competitorCCodes = BuildEML::postgreSql()->getCompetitorCCodes($c_code);
        if (!count((array)$competitorCCodes)) {
            return false;
        }
        $reportPlusImagesCompetitorCCode = array();
        for ($i = 0; $i < count($competitorCCodes); $i++) {
            $reportPlusImages = self::getTours($competitorCCodes[$i], $zexy);
            $reportPlusImagesCompetitorCCode[$i] = $reportPlusImages;
        }
        return $reportPlusImagesCompetitorCCode;
    }

    private static function checkTours($c_code, $zexy)
    {
        $toursCCode = self::getTours($c_code, $zexy);
        if (empty($toursCCode)) {
            return false;
        }
        $toursCompetitorCCode = self::getToursCompetitorCCode($c_code, $zexy);
        $dates = array();
        foreach ($toursCCode as $key => $value) {
            for ($i = 0; $i < count((array)$toursCompetitorCCode); $i++) {
                if (!empty($toursCompetitorCCode[$i]) && !empty($toursCompetitorCCode[$i][$key]) && $value < $toursCompetitorCCode[$i][$key]) {
                    array_push($dates, $key);
                }
            }
        }
        $dates = array_unique($dates);
        $dates = array_values($dates);
        asort($dates);
        return count($dates) ? $dates : false;
    }

    private static function regexCheckFairNameGetContentCd($fairNM): string
    {
        if (preg_match('/相談会|プランナー相談/u', $fairNM)) {
            return "004";
        } elseif (preg_match('/模擬挙式|模擬セレモニー/u', $fairNM)) {
            return "005";
        } elseif (preg_match('/模擬披露宴|模擬パーティ/u', $fairNM)) {
            return "006";
        } elseif (preg_match('/試食/u', $fairNM)) {
            return "007";
        } elseif (preg_match('/試着/u', $fairNM)) {
            return "008";
        } elseif (preg_match('/ファッションショー/u', $fairNM)) {
            return "009";
        } elseif (preg_match('/会場コーデ/u', $fairNM)) {
            return "010";
        } elseif (preg_match('/料理展示|引出物展示/u', $fairNM)) {
            return "011";
        }
        return '';
    }

    private static function regexCheckFairNameB6($fairNM): bool
    {
        return preg_match('/少人数|40名以下|30名以下|20名以下|10名以下|４０名以下|３０名以下|２０名以下|１０名以下|親族のみ|マタニティ|パパママ|ママパパ|Wハッピー|Ｗハッピー|ダブルハッピー|おめでた|妊婦|直近|3カ月以内|2カ月以内|1カ月以内|3ヶ月以内|2ヶ月以内|1ヶ月以内|３カ月以内|２カ月以内|１カ月以内|３ヶ月以内|２ヶ月以内|１ヶ月以内|三カ月以内|二カ月以内|一カ月以内|三ヶ月以内|二ヶ月以内|一ヶ月以内/u', $fairNM);
    }

    private static function regexCheckFairNameB7($fairNM): bool
    {
        return !preg_match('/☓|✕|×|&|＆|\+|＋/u', $fairNM);
    }

    private static function regexCheckFairNameB8($fairNM): bool
    {
        return !preg_match('/特典|プレゼント|ギフト券|OFF|ＯＦＦ|割引|優待|チケット/u', $fairNM);
    }
}
