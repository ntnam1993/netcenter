<?php


namespace App\Utils;

use App\Utils\HTML;
use App\Utils\CSV;
use App\Utils\Image;
use App\Utils\Helper;
use App\Exceptions\CustomException;

class CVImage
{
    const SUN_NUMBER_EQUAL = 0;
    const SUN_NUMBER_BIGGER = 1.0;
    const CLOUD_NUMBER_BIGGER = 0.8;
    const UMBRELLA_NUMBER_LESS = 0.8;

    public static function insertIcon($search, $ratio, $actual, $goal, $content)
    {
        switch (true) {
            case $goal == self::SUN_NUMBER_EQUAL && $actual >= self::SUN_NUMBER_EQUAL:
                $content = str_replace($search, public_path(HTML::SUN), $content); //phpcs:ignore
                break;
            case $goal > self::SUN_NUMBER_EQUAL && $actual == self::SUN_NUMBER_EQUAL:
                $content = str_replace($search, public_path(HTML::UMBRELLA), $content); //phpcs:ignore
                break;
            case $goal > self::SUN_NUMBER_EQUAL && $actual > self::SUN_NUMBER_EQUAL:
                if ($goal <= $actual) {
                    $content = str_replace($search, public_path(HTML::SUN), $content); //phpcs:ignore
                } elseif ($ratio >= self::CLOUD_NUMBER_BIGGER) {
                    $content = str_replace($search, public_path(HTML::CLOUD), $content); //phpcs:ignore
                } elseif ($ratio < self::CLOUD_NUMBER_BIGGER) {
                    $content = str_replace($search, public_path(HTML::UMBRELLA), $content); //phpcs:ignore
                }
                break;
        }
        return $content;
    }

    public static function insertAchievement($ratio, $content)
    {
        return str_replace("ACHIEVEMENT", $ratio >= 1 ? '達成' : '未達成', $content);
    }

    public static function insertMessage($goal_type, $content)
    {
        switch ($goal_type) {
            case HTML::GOAL_TYPE_1:
                $content = str_replace("GOAL_DEFINITION", "直近3ヶ月のエリア平均比（自社の数値／エリア全体の平均数値）を記載させていただいております", $content);
                break;
            case HTML::GOAL_TYPE_2:
                $content = str_replace("GOAL_DEFINITION", "直近1年内のTOP3ヶ月のエリア平均比（自社の数値／エリア全体の平均数値）を記載させていただいております", $content);
                break;
            case HTML::GOAL_TYPE_3:
                $content = str_replace("GOAL_DEFINITION", "前年同月のエリア平均比（自社の数値／エリア全体の数値）を記載させていただいております", $content);
                break;
        }
        return $content;
    }

    public static function goal($data, $goal_type)
    {
        try {
            for ($i = count($data) - 1; $i >= 0; $i--) {
                if (!empty($data[$i])) {
                    if ($data[$i][CSV::WEEK_NUMBER] != "F") {
                        unset($data[$i]);
                    }
                }
            }
            if (!count($data)) {
                throw new CustomException('No data in CSV Which has week_number # F');
            }
            $goal = 0.0;
            switch ($goal_type) {
                case HTML::GOAL_TYPE_1:
                    $goal = self::calGoalType1($data);
                    break;
                case HTML::GOAL_TYPE_2:
                    $goal = self::calGoalType2($data);
                    break;
                case HTML::GOAL_TYPE_3:
                    $goal = self::calGoalType3($data);
                    break;
            }
            return Image::numberFormat($goal);
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return false;
        }
    }

    public static function calGoal($data)
    {
        $total_reservations = 0.0;
        $visit_reservations_cv_area_avg = 0.0;
        for ($i = 0; $i < count($data); $i++) {
            $total_reservations += (float)$data[$i][CSV::TOTAL_RESERVATIONS];
            $visit_reservations_cv_area_avg += (float)$data[$i][CSV::VISIT_RESERVATIONS_CV_AREA_AVG];
        }
        $goal = Helper::devisionAndPercent100($total_reservations, $visit_reservations_cv_area_avg);
        return $goal;
    }

    public static function sort($data, $column)
    {
        $n = count($data);
        for ($i = 0; $i < $n - 1; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                if ($data[$i][$column] > $data[$j][$column]) {
                    $temp = $data[$i];
                    $data[$i] = $data[$j];
                    $data[$j] = $temp;
                }
            }
        }
        return $data;
    }

    public static function getTwelveMonths($data)
    {
        $data = array_slice($data, -12, 12, true);
        $new_start_index = 0;

        $data = array_combine(range($new_start_index, count($data) + ($new_start_index-1)), array_values($data));
        for ($i = 0; $i < count($data); $i++) {
            $ratio = Helper::devision((float)$data[$i][CSV::TOTAL_RESERVATIONS], (float)$data[$i][CSV::VISIT_RESERVATIONS_CV_AREA_AVG]);
            array_push($data[$i], $ratio);
        }

        $data = self::sort($data, 36);

        return $data;
    }

    public static function calGoalType1($data)
    {
        $data = Image::getRecord($data);
        return ($data) ? self::calGoal($data) : false;
    }

    public static function calGoalType2($data)
    {
        $data = self::getTwelveMonths($data);
        $goal = self::calGoalType1($data);
        return ($goal) ? $goal : false;
    }

    public static function calGoalType3($data)
    {
        $data = Image::getRecordType3($data);
        $goal = self::calGoalType1($data);
        return $goal;
    }

    public static function actual($data)
    {
        try {
            for ($i = count($data) - 1; $i >= 0; $i--) {
                if (!empty($data[$i])) {
                    if ($data[$i][CSV::WEEK_NUMBER] == "F") {
                        unset($data[$i]);
                    }
                }
            }
            if (!count($data)) {
                throw new CustomException('No data in CSV have week_number # F');
            }
            $new_start_index = 0;
            $data = array_combine(range($new_start_index, count($data) + ($new_start_index-1)), array_values($data));
            $max_issue_month = Image::max($data, CSV::ISSUE_MONTH);
            $max_week_number = Image::max($data, CSV::WEEK_NUMBER);
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i][0] == $max_issue_month && $data[$i][1] == $max_week_number) {
                    $actual = Helper::devisionAndPercent100((float)$data[$i][CSV::TOTAL_RESERVATIONS], (float)$data[$i][CSV::VISIT_RESERVATIONS_CV_AREA_AVG]);
                }
            }
            return Image::numberFormat($actual);
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return false;
        }
    }
}
