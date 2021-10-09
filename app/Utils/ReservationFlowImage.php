<?php

namespace App\Utils;

use App\Utils\Helper;

class ReservationFlowImage
{
    public static function buildCommentCReservation1($shop, $zexy)
    {
        return $shop['is_c1_not_needed'] ? false : self::checkNaiyoText($zexy);
    }

    public static function buildCommentCReservation2($shop, $zexy)
    {
        return $shop['is_c2_not_needed'] ? false : self::checkFairNameReservation2($zexy);
    }

    public static function buildCommentCReservation3($shop, $zexy)
    {
        return (!$shop['is_c3_not_needed'] && self::checkTourCountReservation3($zexy));
    }

    public static function buildCommentCReservation4($shop, $zexy)
    {
        return $shop['is_c4_not_needed'] ? false : self::checkReportFlagFairReservation4($zexy);
    }

    public static function buildCommentCReservation5($shop, $zexy)
    {
        return $shop['is_c5_not_needed'] ? false : self::checkReportPlusFlagFairReservation5($zexy);
    }

    public static function buildCommentCReservation6($shop, $zexy)
    {
        return $shop['is_c6_not_needed'] ? false : self::checkFairImageCaptionReservation6($zexy);
    }

    public static function buildCommentCReservation7($shop, $zexy)
    {
        return $shop['is_c7_not_needed'] ? false : self::checkRecommendCatchReservation7($zexy);
    }

    public static function buildCommentCReservation8($shop, $zexy)
    {
        return $shop['is_c8_not_needed'] ? false : self::checkExplainReservation8($zexy);
    }

    public static function buildCommentCReservation9($shop, $zexy)
    {
        return $shop['is_c9_not_needed'] ? false : self::checkTantoReservation9($zexy);
    }

    public static function buildCommentCReservation10($shop, $zexy)
    {
        return $shop['is_c10_not_needed'] ? false : self::checkTantoReservation10($zexy);
    }

    public static function buildCommentCReservation12($shop, $zexy)
    {
        return $shop['is_c12_not_needed'] ? false : self::checkPeriodReservation12($zexy);
    }

    public static function buildCommentCReservation13($shop, $zexy)
    {
        return $shop['is_c13_not_needed'] ? false : self::checkRemarkReservation13($zexy);
    }

    public static function buildCommentCReservation14($shop, $zexy)
    {
        return $shop['is_c14_not_needed'] ? false : self::checkRequiredMinuteReservation14($zexy, $shop);
    }

    public static function buildCommentCReservation15($shop, $zexy)
    {
        return $shop['is_c15_not_needed'] ? false : self::checkNaiyoTextWithRegexReservation15($shop, $zexy);
    }

    public static function buildCommentCReservation16($shop, $quantityOrderEachDays, $fairAndContentTypeEachDays)
    {
        return $shop['is_c16_not_needed'] ? false : Helper::calculatorCValue($quantityOrderEachDays, $shop, $fairAndContentTypeEachDays, 'cv');
    }

    private static function checkNaiyoText($zexy)
    {
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeDayWithOnlyStaturdaySunDayHoliday();
        if (!count((array)$clientFairs)) {
            return false;
        }

        $holdDates = [];
        foreach ($clientFairs as $clientFair) {
            $clientFairWithProductCd = $zexy->getClientFairWithProductCd($clientFair->product_cd);
            if (empty($clientFairWithProductCd->fair_perk)) {
                array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
                continue;
            }
            if (empty($clientFairWithProductCd->fair_perk->naiyo) || !$clientFairWithProductCd->fair_perk->naiyo) {
                array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
            }
        }
        asort($holdDates);
        return count($holdDates) ? $holdDates : false;
    }

    private static function checkFairNameReservation2($zexy)
    {
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeDayWithOnlyStaturdaySunDayHoliday();
        if (!count((array)$clientFairs)) {
            return false;
        }

        $holdDates = array();
        foreach ($clientFairs as $clientFair) {
            $content_cd = self::checkRegexFairNameReservation2($clientFair->fair_nm);
            if (!$content_cd) {
                continue;
            }
            if (empty($clientFair->fair_tkch_list) || empty($clientFair->fair_tkch_list->fair_tkch)) {
                continue;
            }
            foreach ($clientFair->fair_tkch_list->fair_tkch as $fair_tkch) {
                if ($content_cd == $fair_tkch->fair_tkch_cd) {
                    return false;
                }
            }
            array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
        }
        asort($holdDates);
        return count($holdDates) ? $holdDates : false;
    }

    private static function checkRegexFairNameReservation2($e): string
    {
        if (preg_match('/相談会|プランナー相談/u', $e)) {
            return "004";
        } elseif (preg_match('/模擬挙式|模擬セレモニー/u', $e)) {
            return "005";
        } elseif (preg_match('/模擬披露宴|模擬パーティ/u', $e)) {
            return "006";
        } elseif (preg_match('/試食/u', $e)) {
            return "007";
        } elseif (preg_match('/試着/u', $e)) {
            return "008";
        } elseif (preg_match('/ファッションショー/u', $e)) {
            return "009";
        } elseif (preg_match('/会場コーデ/u', $e)) {
            return "010";
        } elseif (preg_match('/料理展示|引出物展示/u', $e)) {
            return "011";
        }
        return '';
    }

    private static function checkTourCountReservation3($zexy): bool
    {
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeDayWithOnlyStaturdaySunDayHoliday();
        if (!count((array)$clientFairs)) {
            return false;
        }

        foreach ($clientFairs as $clientFair) {
            if (empty($clientFair->tour_count) || $clientFair->tour_count < Constant::KEY_CHECK_NUMBER_OF_TOUR_COUNT) {
                return true;
            }
        }
        return false;
    }

    private static function checkReportFlagFairReservation4($zexy)
    {
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeDayWithOnlyStaturdaySunDayHoliday();
        if (!count((array)$clientFairs)) {
            return false;
        }

        $holdDates = [];
        foreach ($clientFairs as $clientFair) {
            $urls = [];
            $flag = false;
            $clientFairWithProductCd = $zexy->getClientFairWithProductCd($clientFair->product_cd);
            if (!empty($clientFairWithProductCd->report_plus_fair_flg) && $clientFairWithProductCd->report_plus_fair_flg == 'true') {
                if (!empty($clientFairWithProductCd->fair_event_list) && !empty($clientFairWithProductCd->fair_event_list->fair_event)) {
                    foreach ($clientFairWithProductCd->fair_event_list->fair_event as $fairEvent) {
                        if (empty($fairEvent->report_plus_image_list) || empty($fairEvent->report_plus_image_list->report_plus_image)) {
                            continue;
                        }
                        foreach ($fairEvent->report_plus_image_list->report_plus_image as $reportPlusImage) {
                            if (empty($reportPlusImage->report_plus_image_url) || empty((string)$reportPlusImage->report_plus_image_url[0])) {
                                continue;
                            }
                            $reportPlusImageURL = (string)$reportPlusImage->report_plus_image_url[0];
                            if (in_array($reportPlusImageURL, $urls)) {
                                $flag = true;
                            }
                            array_push($urls, $reportPlusImageURL);
                        }
                    }
                }
            }
            if ($flag) {
                array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
            }
        }
        asort($holdDates);
        return count($holdDates) ? $holdDates : false;
    }

    private static function checkReportPlusFlagFairReservation5($zexy)
    {
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeDayWithOnlyStaturdaySunDayHoliday();
        if (!count((array)$clientFairs)) {
            return false;
        }

        $holdDates = [];
        foreach ($clientFairs as $clientFair) {
            $flag = false;
            $clientFairWithProductCd = $zexy->getClientFairWithProductCd($clientFair->product_cd);
            if (!empty($clientFairWithProductCd->report_plus_fair_flg) && $clientFairWithProductCd->report_plus_fair_flg == 'true') {
                if (!empty($clientFairWithProductCd->fair_event_list) && !empty($clientFairWithProductCd->fair_event_list->fair_event)) {
                    foreach ($clientFairWithProductCd->fair_event_list->fair_event as $fairEvent) {
                        if (empty($fairEvent->report_plus_image_list) || empty($fairEvent->report_plus_image_list->report_plus_image)) {
                            continue;
                        }
                        if (count($fairEvent->report_plus_image_list->report_plus_image) < Constant::CHECK_COUNT_REPORT_PLUS_IMAGE) {
                            $flag = true;
                        }
                    }
                }
            }
            if ($flag) {
                array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
            }
        }
        asort($holdDates);
        return count($holdDates) ? $holdDates : false;
    }

    private static function checkFairImageCaptionReservation6($zexy)
    {
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeDayWithOnlyStaturdaySunDayHoliday();
        if (!count((array)$clientFairs)) {
            return false;
        }

        $holdDates = [];
        foreach ($clientFairs as $clientFair) {
            $clientFairWithProductCd = $zexy->getClientFairWithProductCd($clientFair->product_cd);
            if (empty($clientFairWithProductCd->fair_image_caption) || !$clientFairWithProductCd->fair_image_caption) {
                array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
            }
        }
        asort($holdDates);
        return count($holdDates) ? $holdDates : false;
    }

    private static function checkRecommendCatchReservation7($zexy)
    {
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeDayWithOnlyStaturdaySunDayHoliday();
        if (!count((array)$clientFairs)) {
            return false;
        }

        $holdDates = [];
        foreach ($clientFairs as $clientFair) {
            $clientFairWithProductCd = $zexy->getClientFairWithProductCd($clientFair->product_cd);
            if (!empty($clientFairWithProductCd->report_plus_fair_flg) && $clientFairWithProductCd->report_plus_fair_flg == 'true') {
                if (empty($clientFairWithProductCd->recommend_point)) {
                    array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
                    continue;
                }
                if (empty($clientFairWithProductCd->recommend_point->recommend_catch) || !$clientFairWithProductCd->recommend_point->recommend_catch) {
                    array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
                }
            }
        }
        asort($holdDates);
        return count($holdDates) ? $holdDates : false;
    }

    private static function checkExplainReservation8($zexy)
    {
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeDayWithOnlyStaturdaySunDayHoliday();
        if (!count((array)$clientFairs)) {
            return false;
        }

        $holdDates = [];
        foreach ($clientFairs as $clientFair) {
            $clientFairWithProductCd = $zexy->getClientFairWithProductCd($clientFair->product_cd);
            if (!empty($clientFairWithProductCd->report_plus_fair_flg) && $clientFairWithProductCd->report_plus_fair_flg == 'true') {
                if (empty($clientFairWithProductCd->recommend_point)) {
                    array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
                    continue;
                }
                if (empty($clientFairWithProductCd->recommend_point->explain) || !$clientFairWithProductCd->recommend_point->explain) {
                    array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
                }
            }
        }
        asort($holdDates);
        return count($holdDates) ? $holdDates : false;
    }

    private static function checkTantoReservation9($zexy)
    {
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeDayWithOnlyStaturdaySunDayHoliday();
        if (!count((array)$clientFairs)) {
            return false;
        }
        $holdDates = [];
        foreach ($clientFairs as $clientFair) {
            $clientFairWithProductCd = $zexy->getClientFairWithProductCd($clientFair->product_cd);
            if (!empty($clientFairWithProductCd->report_plus_fair_flg) && $clientFairWithProductCd->report_plus_fair_flg == 'true') {
                if (empty($clientFairWithProductCd->recommend_point)) {
                    array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
                    continue;
                }
                if (empty($clientFairWithProductCd->recommend_point->tanto_nm) || !$clientFairWithProductCd->recommend_point->tanto_nm
                    || empty($clientFairWithProductCd->recommend_point->tanto_duty) || !$clientFairWithProductCd->recommend_point->tanto_duty) {
                    array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
                }
            }
        }
        asort($holdDates);
        return count($holdDates) ? $holdDates : false;
    }

    private static function checkTantoReservation10($zexy)
    {
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeDayWithOnlyStaturdaySunDayHoliday();
        if (!count((array)$clientFairs)) {
            return false;
        }
        $holdDates = [];
        foreach ($clientFairs as $clientFair) {
            $clientFairWithProductCd = $zexy->getClientFairWithProductCd($clientFair->product_cd);
            if (!empty($clientFairWithProductCd->report_plus_fair_flg) && $clientFairWithProductCd->report_plus_fair_flg == 'true') {
                if (empty($clientFairWithProductCd->recommend_point)) {
                    array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
                    continue;
                }
                if (empty($clientFairWithProductCd->recommend_point->tanto_image_url) || !$clientFairWithProductCd->recommend_point->tanto_image_url) {
                    array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
                }
            }
        }
        asort($holdDates);
        return count($holdDates) ? $holdDates : false;
    }

    private static function checkPeriodReservation12($zexy)
    {
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeDayWithOnlyStaturdaySunDayHoliday();
        if (!count((array)$clientFairs)) {
            return false;
        }

        $holdDates = [];
        foreach ($clientFairs as $clientFair) {
            $clientFairWithProductCd = $zexy->getClientFairWithProductCd($clientFair->product_cd);
            if (empty($clientFairWithProductCd->fair_perk)) {
                array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
                continue;
            }
            if (empty($clientFairWithProductCd->fair_perk->period) || !$clientFairWithProductCd->fair_perk->period) {
                array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
            }
        }
        asort($holdDates);
        return count($holdDates) ? $holdDates : false;
    }

    private static function checkRemarkReservation13($zexy)
    {
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeDayWithOnlyStaturdaySunDayHoliday();
        if (!count((array)$clientFairs)) {
            return false;
        }

        $holdDates = [];
        foreach ($clientFairs as $clientFair) {
            $clientFairWithProductCd = $zexy->getClientFairWithProductCd($clientFair->product_cd);
            if (empty($clientFairWithProductCd->fair_perk)) {
                array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
                continue;
            }
            if (empty($clientFairWithProductCd->fair_perk->remarks) || !$clientFairWithProductCd->fair_perk->remarks) {
                array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
            }
        }
        asort($holdDates);
        return count($holdDates) ? $holdDates : false;
    }

    private static function checkRequiredMinuteReservation14($zexy, $shop)
    {
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeday();
        if (!count((array)$clientFairs)) {
            return false;
        }

        $holdDates = array();
        foreach ($clientFairs as $clientFair) {
            if ($clientFair->fair_list) {
                $flag = false;
                foreach ($clientFair->fair_list->fair as $fair) {
                    if (empty($fair->required_minute)) {
                        continue;
                    }
                    if ($fair->required_minute <= $shop['c14_threshold']) {
                        return false;
                    }
                    $flag = true;
                }
                if ($flag) {
                    array_push($holdDates, $zexy->formatDayForObject($clientFair->hold_date));
                }
            }
        }
        asort($holdDates);
        return count($holdDates) ? $holdDates : false;
    }

    private static function getMoneies($c_code, $zexy)
    {
        $moneyWithCCode = array();
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeDayWithOnlyStaturdaySunDayHolidayCompetitorCCode($c_code);
        if (!count((array)$clientFairs)) {
            return false;
        }
        foreach ($clientFairs as $clientFair) {
            $clientFairWithProductCd = $zexy->getClientFairWithProductCdCompetitorCCode($clientFair->product_cd, $c_code);
            if (!empty($clientFairWithProductCd->fair_perk) && !empty($clientFairWithProductCd->fair_perk->naiyo) && self::checkRegexReservation15($clientFairWithProductCd->fair_perk->naiyo)) {
                $moneyWithCCode[$zexy->formatDayForObject($clientFair->hold_date)] = self::checkRegexToGetMoneyReservation15($clientFairWithProductCd->fair_perk->naiyo);
            }
        }
        return $moneyWithCCode;
    }

    private static function getMoneiesCompetitorCCode($shop, $zexy)
    {
        $competitorCCodes = Helper::getCompetitorCCode($shop['c_code']);
        if (!count((array)$competitorCCodes)) {
            return false;
        }
        $moneyWithCCodes = array();
        for ($i = 0; $i < count($competitorCCodes); $i++) {
            $money = self::getMoneies($competitorCCodes[$i], $zexy);
            $moneyWithCCodes[$i] = $money;
        }
        return $moneyWithCCodes;
    }

    private static function checkNaiyoTextWithRegexReservation15($shop, $zexy)
    {
        $moneyWithCCode = self::getMoneies($shop['c_code'], $zexy);
        if (empty($moneyWithCCode)) {
            return false;
        }
        $moneyWithCompeitorCCode = self::getMoneiesCompetitorCCode($shop, $zexy);

        $dates = array();
        foreach ($moneyWithCCode as $key => $value) {
            for ($i = 0; $i < count((array)$moneyWithCompeitorCCode); $i++) {
                if (!empty($moneyWithCompeitorCCode[$i]) && !empty($moneyWithCompeitorCCode[$i][$key]) && $value < $moneyWithCompeitorCCode[$i][$key]) {
                    array_push($dates, $key);
                }
            }
        }
        $dates = array_unique($dates);
        $dates = array_values($dates);
        asort($dates);
        return count($dates) ? $dates : false;
    }

    private static function checkRegexReservation15($naiyo)
    {
        return preg_match('/チケット|券|カード/u', $naiyo);
    }

    private static function checkRegexToGetMoneyReservation15($naiyo)
    {
        $naiyo = preg_replace('/￥|¥/u', '\\', preg_replace('/,/', '', array_reduce([['/〇/u', '0'], ['/一/u', '1'], ['/二/u', '2'], ['/三/u', '3'], ['/四/u', '4'], ['/五/u', '5'], ['/六/u', '6'], ['/七/u', '7'], ['/八/u', '8'], ['/九/u', '9']], function ($a, $p) {
            return preg_replace($p[0], $p[1], $a);
        }, mb_convert_kana($naiyo, "KVa", "utf-8"))));
        if (preg_match('/(円|\\\)([\d十百千万]+)/u', $naiyo, $a)) {
            $naiyo = $a[2];
        } elseif (preg_match('/([\d十百千万]+)(円|\\\)/u', $naiyo, $a)) {
            $naiyo = $a[1];
        }
        return ($f = function ($q) use (&$f) {
            if (preg_match('/(.*)万(.*)/u', $q, $a)) {
                return $f($a[1]) * 10000 + $f($a[2]);
            } elseif (preg_match('/(.*)千(.*)/u', $q, $a)) {
                return (empty($a[1]) ? 1 : $a[1]) * 1000 + $f($a[2]);
            } elseif (preg_match('/(.*)百(.*)/u', $q, $a)) {
                return (empty($a[1]) ? 1 : $a[1]) * 100 + $f($a[2]);
            } elseif (preg_match('/(.*)十(.*)/u', $q, $a)) {
                return (empty($a[1]) ? 1 : $a[1]) * 10 + $f($a[2]);
            }
            return empty($q) ? 0 : $q;
        })($naiyo);
    }
}
