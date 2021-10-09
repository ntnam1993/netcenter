<?php

namespace App\Utils;

use App\Console\Commands\BuildEML;

class Helper
{
    public static function varDump($message)
    {
        var_dump($message);
        return;
    }

    public static function turnOnFlag()
    {
        return true;
    }

    public static function turnOffFlag()
    {
        return false;
    }

    public static function devision($numerator, $denominator)
    {
        return $denominator == 0 ? 0.0 : ($numerator/$denominator);
    }

    public static function devisionAndPercent100($numerator, $denominator)
    {
        return $denominator == 0 ? 0.0 : ($numerator/$denominator) * 100;
    }

    public static function getDate($format = 'Ymd')
    {
        return date($format);
    }

    /**
     * This will suppress all the PMD warnings in
     * this class.
     *
     * @SuppressWarnings(PHPMD)
     */
    public static function calculatorCValue($quantityOrderEachDays, $shop, $fairAndContentTypeEachDays, $keyCheck = 'page_view')
    {
        if (!count((array)$fairAndContentTypeEachDays)) {
            return false;
        }
        $rows = [];
        $cValues = [];
        foreach ($quantityOrderEachDays as $key => $quantityOrderEachDay) {
            if ($quantityOrderEachDay['client_cd'] == $shop['c_code']) {
                array_push($rows, $quantityOrderEachDay);
                $cValues[$key] = $quantityOrderEachDay['c'];
            }
        }
        $count = count($rows);
        if (!$count) {
            return false;
        }
        array_multisort($cValues, SORT_DESC, $rows);

        if ($count >= Constant::LIMIT_COUNT_OF_DATA_IN_FAIR_DETAIL_10) {
            foreach ($rows as $key => $row) {
                $date = date('Y M D d', strtotime($row['hold_date']));
                if (!(strpos($date, 'Sat') || strpos($date, 'Sun') || self::checkDayIsHoliday($row['hold_date']))) {
                    unset($rows[$key]);
                }
            }
            $rows = array_slice($rows, 0, Constant::LIMIT_COUNT_OF_DATA_IN_FAIR_DETAIL_10);
        }

        $dataComment = [];
        if (env('APP_ENV') == Constant::LOCAL_ENV) {
            $fairAndContentTypeEachDays = self::getFairAndContentTypeEachDayWithCCode($shop['c_code']);
        }
        //buoc 5
        $rowAs = array();
        foreach ($fairAndContentTypeEachDays as $fairAndContentTypeEachDay) {
            if ($shop['c_code'] == $fairAndContentTypeEachDay->client_cd) {
                array_push($rowAs, (array)$fairAndContentTypeEachDay);
            }
        }

        //buoc 6
        $rowBs = [];
        foreach ($rowAs as $rowA) {
            foreach ($rows as $row) {
                if (date('Y-m-d', strtotime($rowA['hold_date'])) == date('Y-m-d', strtotime($row['hold_date']))) {
                    array_push($rowBs, $rowA);
                }
            }
        }

        if (!count($rowBs)) {
            return false;
        }

        $rowCs = [];
        foreach ($rowAs as $rowA) {
            if (date('Y-m-d', strtotime($rowA['hold_date'])) < date('Y-m-d')) {
                array_push($rowCs, $rowA);
            }
        }
        
        if (!count($rowCs)) {
            return false;
        }

        $rowDs = [];
        $fairNms = [];
        $rowBCs = [];
        foreach ($rowBs as $key => $rowB) {
            $rowBCs[date('Ymd', strtotime($rowB['hold_date']))] = [];
            foreach ($rowCs as $rowC) {
                //check trong $rowDs có fair_nm ko chưa $rowB['fair_nm']
                if ($rowB['tkch_004'] == $rowC['tkch_004'] && $rowB['tkch_005'] == $rowC['tkch_005'] && $rowB['tkch_006'] == $rowC['tkch_006'] && $rowB['tkch_007'] == $rowC['tkch_007'] && $rowB['tkch_008'] == $rowC['tkch_008'] && $rowB['tkch_009'] == $rowC['tkch_009'] && $rowB['tkch_010'] == $rowC['tkch_010'] && $rowB['tkch_011'] == $rowC['tkch_011'] && $rowB['tkch_012'] == $rowC['tkch_012'] && $rowB['tkch_013'] == $rowC['tkch_013']) {
                    array_push($rowBCs[date('Ymd', strtotime($rowB['hold_date']))], $rowC);
                }
            }
        }
        foreach ($rowBCs as $keyHoldDate => $rowCs) {
            $max = 0;
            $keyCMax = 0;
            foreach ($rowCs as $key => $rowC) {
                if (($max == 0 || $rowC[$keyCheck] > $max) && !in_array($rowC['fair_nm'], $fairNms)) {
                    $max = $rowC[$keyCheck];
                    $keyCMax = $key;
                }
            }

            array_push($fairNms, $rowCs[$keyCMax]['fair_nm']);
            $rowDs[$keyHoldDate] = $rowCs[$keyCMax];
        }
        ksort($rowDs);
        $index = 0;

        foreach ($rowDs as $key => $rowD) {
            $dataComment[$index] = [
                'date' =>  $key,
                'fair_nm' => $rowD['fair_nm'],
                'page_view' => $rowD['page_view']
            ];
            $index++;
        }

        return count($dataComment) ? $dataComment : false;
    }

    public static function dateFormat($dateString, $format = 'Y-m-d')
    {
        return date($format, strtotime($dateString));
    }

    private static function checkDayIsHoliday($holdDate)
    {
        return count(BuildEML::postgreSql()->searchDayIsHoliday($holdDate));
    }

    private static function getFairAndContentTypeEachDayWithCCode($cCode)
    {
        return BuildEML::nabi()->mySql->getFairAndContentTypeEachDayWithCCode($cCode);
    }

    public static function getCompetitorCCode($c_code)
    {
        return BuildEML::postgreSql()->getCompetitorCCodes($c_code);
    }

    public static function checkHoldDate($zexy): bool
    {
        $clientFairs = $zexy->searchClientFairWithDaysAfterSomeday();
        if (!count((array)$clientFairs) || !$clientFairs) {
            return false;
        }
        $holdDates = array();
        foreach ($clientFairs as $clientFair) {
            if (!empty($clientFair->fair_image_url) && count($clientFair->fair_image_url)) {
                $fair_image_urls = (array)$clientFair->fair_image_url;
                $holdDates[$fair_image_urls[0]][] = $zexy->formatDayForObject($clientFair->hold_date);
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
}
