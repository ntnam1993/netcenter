<?php

namespace App\Utils;

use App\Console\Commands\BuildEML;
use App\Utils\CSV;
use App\Utils\Helper;

class Image
{
    public static function getRecord($data)
    {
        $count = count($data);
        switch ($count) {
            case $count >= 3:
                $count = 3;
                $data = array_slice($data, -3, 3, true);
                break;
            case $count == 2:
                $data = array_slice($data, -2, 2, true);
                break;
            case $count == 1:
                $data = array_slice($data, -1, 1, true);
                break;
            default:
                return false;
        }

        $new_start_index = 0;
        return array_combine(range($new_start_index, $count + ($new_start_index-1)), array_values($data));
    }

    public static function max($data, $column)
    {
        $max = 0;
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i][$column] > $max) {
                $max = $data[$i][$column];
            }
        }
        return $max;
    }

    public static function getRecordType3($data)
    {
        $issue_month = BuildEML::postgreSql()->getIssueMonth();
        if (empty($issue_month)) {
            return $data;
        }
        $str = str_split($issue_month, 4);
        $formatDate = implode('-', $str);

        $oldDate = date("Ym", strtotime("-12 months", strtotime($formatDate)));
        if (!$oldDate) {
            return $data;
        }
        for ($i = 0; $i < count($data); $i++) {
            if ($oldDate == $data[$i][CSV::ISSUE_MONTH]) {
                $newData[] = $data[$i];
            }
        }
        return (empty($newData)) ? $data : $newData;
    }

    public static function calFair($total_fair_avg, $total_avg, $total_fair_actual, $total_actual)
    {
        $actual = Helper::devision($total_fair_actual, $total_actual);
        $avg = Helper::devision($total_fair_avg, $total_avg);
        $fair_flow = Helper::devisionAndPercent100($actual, $avg);
        $fair_flow = self::numberFormat($fair_flow);
        return $fair_flow;
    }

    public static function calDetail($total_detail_avg, $total_fair_avg, $total_detail_actual, $total_fair_actual)
    {
        $actual = Helper::devision($total_detail_actual, $total_fair_actual);
        $avg = Helper::devision($total_detail_avg, $total_fair_avg);
        $detail_flow = Helper::devisionAndPercent100($actual, $avg);
        $detail_flow = self::numberFormat($detail_flow);
        return $detail_flow;
    }

    public static function calReservation($total_reservation_avg, $total_detail_avg, $total_reservation_actual, $total_detail_actual)
    {
        $actual = Helper::devision($total_reservation_actual, $total_detail_actual);
        $avg = Helper::devision($total_reservation_avg, $total_detail_avg);
        $reservation_flow = Helper::devisionAndPercent100($actual, $avg);
        $reservation_flow = self::numberFormat($reservation_flow);
        return $reservation_flow;
    }

    public static function getTwelveMonths($data)
    {
        $data = count($data) >= 12 ? array_slice($data, -12, 12, true) : array_slice($data, -count($data), count($data), true);
        $new_start_index = 0;
        $data = array_combine(range($new_start_index, count($data) + ($new_start_index-1)), array_values($data));

        return $data;
    }

    public static function getTop3($data, $top, $plan = false, $photo = false)
    {
        $data = self::getTwelveMonths($data);

        $array = array();
        for ($i = 0; $i < count($data); $i++) {
            $planNumber = $plan ? (float)$data[$i][$plan] : 0;
            $photoNumber = $photo ? (float)$data[$i][$photo] : 0;
            $value = (float)$data[$i][$top] + $photoNumber + $planNumber;
            array_push($array, $value);
        }

        rsort($array);
        $total = $array[0] + $array[1] + $array[2];
        return $total;
    }

    public static function numberFormat($number)
    {
        return number_format((float)$number, 1, '.', '');
    }
}
