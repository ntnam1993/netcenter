<?php
/**
 * Created by PhpStorm.
 * User: nl
 * Date: 23/06/2020
 * Time: 09:09
 */
namespace App\Utils;

use DateTime;
use App\Utils\Helper;

class FurtherFlowImage
{
    public static function buildCommentDFurther1($shop, $zexy): bool
    {
        return !$shop['is_d1_not_needed'] && self::checkDateFurther1($shop, $zexy);
    }

    public static function buildCommentDFurther2($shop, $zexy): bool
    {
        if ($shop['is_d2_not_needed']) {
            return false;
        }
        return Helper::checkHoldDate($zexy);
    }

    public static function buildCommentDFurther3($shop, $zexy)
    {
        if ($shop['is_d3_not_needed']) {
            return false;
        }
        return self::checkNaiyoText($zexy);
    }

    public static function buildCommentDFurther4($shop, $dataCSVIchioshiIcon): bool
    {
        return !$shop['is_d4_not_needed'] && !empty($dataCSVIchioshiIcon) && (($dataCSVIchioshiIcon['available_number'] - $dataCSVIchioshiIcon['used_number']) > 0);
    }

    private static function checkDateFurther1($shop, $zexy)
    {
        $keisaiDate = $zexy->getKeisaiStartDateFromArrival();

        if (!$keisaiDate || !self::isFormatDate($keisaiDate)) {
            return true;
        }

        if (strtotime($keisaiDate) < strtotime(!$zexy->modifyDateFromNow($shop['d1_threshold']))) {
            return true;
        }

        return $zexy->checkStatusCodeAutoLink();
    }

    private static function checkNaiyoText($zexy)
    {
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeDayWithOnlyStaturdaySunDayHoliday();

        if (!count((array)$clientFairs) || !$clientFairs) {
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

    private static function isFormatDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}
