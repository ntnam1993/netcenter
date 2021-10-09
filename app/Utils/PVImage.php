<?php


namespace App\Utils;

use App\Exceptions\CustomException;
use App\Utils\CVImage;
use App\Utils\HTML;
use App\Utils\CSV;
use App\Utils\Image;
use App\Utils\Helper;

class PVImage
{
    protected $sizePV;
    const VALUE_ICON = 100;
    const INDEX_ICON20 = "ICON20";
    const INDEX_ICON21 = "ICON21";
    const INDEX_ICON22 = "ICON22";

    public static function insertCriteria($goal_type, $content)
    {
        switch ($goal_type) {
            case HTML::GOAL_TYPE_1:
                $string = '直近3ヶ月のエリア平均比（自社の数値／エリア全体の平均数値）を記載させていただいております';
                $content = str_replace("CRITERIA", $string, $content);
                break;
            case HTML::GOAL_TYPE_2:
                $string = '直近1年内のTOP3ヶ月のエリア平均比（自社の数値／エリア全体の平均数値）を記載させていただいております';
                $content = str_replace("CRITERIA", $string, $content);
                break;
            case HTML::GOAL_TYPE_3:
                $string = '前年同月のエリア平均比（自社の数値／エリア全体の数値）を記載させていただいております';
                $content = str_replace("CRITERIA", $string, $content);
                break;
        }
        return $content;
    }

    public static function color($color, $character)
    {
        $string = "<td class=\"val\" style=\"background-color: #$color\">$character</td>";
        return $string;
    }

    public static function printResult($result, $search, $content)
    {
        switch (true) {
            case $result < 30:
                $string = self::color('ddd9c5', 'C');
                $content = str_replace($search, $string, $content);
                break;
            case $result >= 30 && $result < 60:
                $string = self::color('c4be9b', 'C+');
                $content = str_replace($search, $string, $content);
                break;
            case $result >= 60 && $result < 80:
                $string = self::color('efdedc', 'B');
                $content = str_replace($search, $string, $content);
                break;
            case $result >= 80 && $result < 100:
                $string = self::color('dfbab9', 'B+');
                $content = str_replace($search, $string, $content);
                break;
            case $result >= 100 && $result < 120:
                $string = self::color('c8d9ef', 'A');
                $content = str_replace($search, $string, $content);
                break;
            case $result >= 120 && $result < 160:
                $string = self::color('94b4df', 'A+');
                $content = str_replace($search, $string, $content);
                break;
            case $result >= 160 && $result < 200:
                $string = self::color('c6d4a0', 'S');
                $content = str_replace($search, $string, $content);
                break;
            case $result >= 200:
                $string = self::color('a1b866', 'S+');
                $content = str_replace($search, $string, $content);
                break;
        }
        return $content;
    }

    public static function rowMax($data)
    {
        $max_issue_month = Image::max($data, CSV::ISSUE_MONTH);
        $max_week_number = Image::max($data, CSV::WEEK_NUMBER);
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i][CSV::ISSUE_MONTH] == $max_issue_month && $data[$i][CSV::WEEK_NUMBER] == $max_week_number) {
                return $i;
            }
        }
        return $i;
    }

    public static function getRow($dataCSVClrepo)
    {
        try {
            for ($i = count($dataCSVClrepo) - 1; $i >= 0; $i--) {
                if (!empty($dataCSVClrepo[$i])) {
                    if ($dataCSVClrepo[$i][CSV::WEEK_NUMBER] == "F") {
                        unset($dataCSVClrepo[$i]);
                    }
                }
            }
            if (!count($dataCSVClrepo)) {
                throw new CustomException('No data in CSV have week_number # F');
            }
            $new_start_index = 0;
            $dataCSVClrepo = array_combine(range($new_start_index, count($dataCSVClrepo) + ($new_start_index - 1)), array_values($dataCSVClrepo));
            return $dataCSVClrepo;
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return [];
        }
    }

    public static function avgs($dataCSVClrepo)
    {
        try {
            $dataCSVClrepo = self::getRow($dataCSVClrepo);
            if (!$dataCSVClrepo) {
                throw new CustomException('No data in csv clrepo (build avgs)');
            }
            $row = self::rowMax($dataCSVClrepo);

            $top_avg = Image::numberFormat($dataCSVClrepo[$row][CSV::TOP_PAGE_PV_AREA_AVG]);
            $plan_avg = Image::numberFormat($dataCSVClrepo[$row][CSV::AREA_AVG_PLAN_PAGE_PV]);
            $photo_avg = Image::numberFormat($dataCSVClrepo[$row][CSV::PHOTO_GALLERY_PAGE_PV_AREA_AVG]);
            $fair_avg = Image::numberFormat($dataCSVClrepo[$row][CSV::FAIRS_PAGE_PV_AREA_AVG]);
            $detail_avg = Image::numberFormat($dataCSVClrepo[$row][CSV::FAIR_DETAILS_PAGE_PV_AREA_AVG]);
            $reservation_avg = Image::numberFormat($dataCSVClrepo[$row][CSV::VISIT_RESERVATIONS_CV_AREA_AVG]);

            $avg = array($top_avg, $plan_avg, $photo_avg, $fair_avg, $detail_avg, $reservation_avg);

            return $avg;
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return [];
        }
    }

    public static function actuals($dataCSVClrepo)
    {
        try {
            $dataCSVClrepo = self::getRow($dataCSVClrepo);
            if (!$dataCSVClrepo) {
                throw new CustomException('No data in csv clrepo (build actual)');
            }
            $row = self::rowMax($dataCSVClrepo);

            $top_actual = Image::numberFormat($dataCSVClrepo[$row][CSV::TOP_PAGE_PV]);
            $plan_actual = Image::numberFormat($dataCSVClrepo[$row][CSV::PLAN_PAGE_PV]);
            $photo_actual = Image::numberFormat($dataCSVClrepo[$row][CSV::PHOTO_GALLERY_PAGE_PV]);
            $fair_actual = Image::numberFormat($dataCSVClrepo[$row][CSV::BRIDAL_FAIRS_PAGE_PV]);
            $detail_actual = Image::numberFormat($dataCSVClrepo[$row][CSV::BRIDAL_FAIR_DETAILS_PAGE_PV]);
            $reservation_actual = Image::numberFormat($dataCSVClrepo[$row][CSV::TOTAL_RESERVATIONS]);

            $actual = array($top_actual, $plan_actual, $photo_actual, $fair_actual, $detail_actual, $reservation_actual);
            return $actual;
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return [];
        }
    }

    public static function results($dataCSVClrepo)
    {
        try {
            $avg = self::avgs($dataCSVClrepo);
            if (!$avg) {
                throw new CustomException('No data in csv clrepo (build result)');
            }

            $actual = self::actuals($dataCSVClrepo);
            if (!$actual) {
                throw new CustomException('No data in csv clrepo (build actual)');
            }

            $top_result = Helper::devisionAndPercent100($actual[0], $avg[0]);
            $plan_result = Helper::devisionAndPercent100($actual[1], $avg[1]);
            $photo_result = Helper::devisionAndPercent100($actual[2], $avg[2]);
            $fair_result = Helper::devisionAndPercent100($actual[3], $avg[3]);
            $detail_result = Helper::devisionAndPercent100($actual[4], $avg[4]);
            $reservation_result = Helper::devisionAndPercent100($actual[5], $avg[5]);

            $result = array($top_result, $plan_result, $photo_result, $fair_result, $detail_result, $reservation_result);
            return $result;
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return [];
        }
    }

    public static function stds($data, $summary_type)
    {
        try {
            for ($i = count($data) - 1; $i >= 0; $i--) {
                if (!empty($data[$i])) {
                    if ($data[$i][1] != "F") {
                        unset($data[$i]);
                    }
                }
            }
            if (!count($data)) {
                throw new CustomException('No data in CSV have week_number # F');
            }
            $std = array();
            switch ($summary_type) {
                case HTML::SUMMARY_TYPE_1:
                    $std = self::calStd1s($data);
                    break;
                case HTML::SUMMARY_TYPE_2:
                    $std = self::calStd2s($data);
                    break;
                case HTML::SUMMARY_TYPE_3:
                    $std = self::calStd3s($data);
                    break;
            }
            return $std;
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return [];
        }
    }

    public static function cal($total_actual, $total_fair_actual, $total_avg, $total_fair_avg, $total_detail_actual, $total_detail_avg, $total_reservation_actual, $total_reservation_avg)
    {
        $fair_flow = Image::calFair($total_fair_avg, $total_avg, $total_fair_actual, $total_actual);
        $detail_flow = Image::calDetail($total_detail_avg, $total_fair_avg, $total_detail_actual, $total_fair_actual);
        $reservation_flow = Image::calReservation($total_reservation_avg, $total_detail_avg, $total_reservation_actual, $total_detail_actual);

        $std = array($fair_flow, $detail_flow, $reservation_flow);
        return $std;
    }

    public static function calStd1s($data)
    {
        $total_actual = 0.0;
        $total_fair_actual = 0.0;
        $total_avg = 0.0;
        $total_fair_avg = 0.0;
        $total_detail_actual = 0.0;
        $total_detail_avg = 0.0;
        $total_reservation_actual = 0.0;
        $total_reservation_avg = 0.0;

        $data = Image::getRecord($data);

        for ($i = 0; $i < count($data); $i++) {
            $total_actual += (float)$data[$i][CSV::TOP_PAGE_PV] + (float)$data[$i][CSV::PLAN_PAGE_PV] + (float)$data[$i][CSV::PHOTO_GALLERY_PAGE_PV];
            $total_fair_actual += (float)$data[$i][CSV::BRIDAL_FAIRS_PAGE_PV];
            $total_avg += (float)$data[$i][CSV::TOP_PAGE_PV_AREA_AVG] + (float)$data[$i][CSV::AREA_AVG_PLAN_PAGE_PV] + (float)$data[$i][CSV::PHOTO_GALLERY_PAGE_PV_AREA_AVG];
            $total_fair_avg += (float)$data[$i][CSV::FAIRS_PAGE_PV_AREA_AVG];
            $total_detail_actual += (float)$data[$i][CSV::BRIDAL_FAIR_DETAILS_PAGE_PV];
            $total_detail_avg += (float)$data[$i][CSV::FAIR_DETAILS_PAGE_PV_AREA_AVG];
            $total_reservation_actual += (float)$data[$i][CSV::TOTAL_RESERVATIONS];
            $total_reservation_avg += (float)$data[$i][CSV::VISIT_RESERVATIONS_CV_AREA_AVG];
        }

        $std = self::cal($total_actual, $total_fair_actual, $total_avg, $total_fair_avg, $total_detail_actual, $total_detail_avg, $total_reservation_actual, $total_reservation_avg);
        return $std;
    }

    public static function getTop3Actual($data)
    {
        $total_actual = Image::getTop3($data, CSV::TOP_PAGE_PV, CSV::PLAN_PAGE_PV, CSV::PHOTO_GALLERY_PAGE_PV);
        return $total_actual;
    }

    public static function getTop3Avg($data)
    {
        $total_avg = Image::getTop3($data, CSV::TOP_PAGE_PV_AREA_AVG, CSV::AREA_AVG_PLAN_PAGE_PV, CSV::PHOTO_GALLERY_PAGE_PV_AREA_AVG);
        return $total_avg;
    }

    public static function getTop3FairActual($data)
    {
        $total_fair_actual = Image::getTop3($data, CSV::BRIDAL_FAIRS_PAGE_PV);
        return $total_fair_actual;
    }

    public static function getTop3FairAvg($data)
    {
        $total_fair_avg = Image::getTop3($data, CSV::FAIRS_PAGE_PV_AREA_AVG);
        return $total_fair_avg;
    }

    public static function getTop3DetailActual($data)
    {
        $total_detail_actual = Image::getTop3($data, CSV::BRIDAL_FAIR_DETAILS_PAGE_PV);
        return $total_detail_actual;
    }

    public static function getTop3DetailAvg($data)
    {
        $total_detail_avg = Image::getTop3($data, CSV::FAIR_DETAILS_PAGE_PV_AREA_AVG);
        return $total_detail_avg;
    }

    public static function getTop3ResActual($data)
    {
        $total_reservation_actual = Image::getTop3($data, CSV::TOTAL_RESERVATIONS);
        return $total_reservation_actual;
    }

    public static function getTop3ResAvg($data)
    {
        $total_reservation_avg = Image::getTop3($data, CSV::VISIT_RESERVATIONS_CV_AREA_AVG);
        return $total_reservation_avg;
    }

    public static function calStd2s($data)
    {
        $total_actual = self::getTop3Actual($data);
        $total_fair_actual = self::getTop3FairActual($data);
        $total_avg = self::getTop3Avg($data);
        $total_fair_avg = self::getTop3FairAvg($data);
        $total_detail_actual = self::getTop3DetailActual($data);
        $total_detail_avg = self::getTop3DetailAvg($data);
        $total_reservation_actual = self::getTop3ResActual($data);
        $total_reservation_avg = self::getTop3ResAvg($data);

        $std = self::cal($total_actual, $total_fair_actual, $total_avg, $total_fair_avg, $total_detail_actual, $total_detail_avg, $total_reservation_actual, $total_reservation_avg);
        return $std;
    }

    public static function calStd3s($data)
    {
        $data = Image::getRecordType3($data);
        $std = self::calStd1s($data);
        return $std;
    }

    public static function curs($dataCSVClrepo)
    {
        try {
            $dataCSVClrepo = self::getRow($dataCSVClrepo);
            if (!$dataCSVClrepo) {
                throw new CustomException('No data in csv clrepo (build curs) .');
            }
            $row = self::rowMax($dataCSVClrepo);

            $total_actual = (float)$dataCSVClrepo[$row][CSV::TOP_PAGE_PV] + (float)$dataCSVClrepo[$row][CSV::PLAN_PAGE_PV] + (float)$dataCSVClrepo[$row][CSV::PHOTO_GALLERY_PAGE_PV];
            $total_fair_actual = (float)$dataCSVClrepo[$row][CSV::BRIDAL_FAIRS_PAGE_PV];
            $total_avg = (float)$dataCSVClrepo[$row][CSV::TOP_PAGE_PV_AREA_AVG] + (float)$dataCSVClrepo[$row][CSV::AREA_AVG_PLAN_PAGE_PV] + (float)$dataCSVClrepo[$row][CSV::PHOTO_GALLERY_PAGE_PV_AREA_AVG];
            $total_fair_avg = (float)$dataCSVClrepo[$row][CSV::FAIRS_PAGE_PV_AREA_AVG];
            $total_detail_actual = (float)$dataCSVClrepo[$row][CSV::BRIDAL_FAIR_DETAILS_PAGE_PV];
            $total_detail_avg = (float)$dataCSVClrepo[$row][CSV::FAIR_DETAILS_PAGE_PV_AREA_AVG];
            $total_reservation_actual = (float)$dataCSVClrepo[$row][CSV::TOTAL_RESERVATIONS];
            $total_reservation_avg = (float)$dataCSVClrepo[$row][CSV::VISIT_RESERVATIONS_CV_AREA_AVG];

            $cur = self::cal($total_actual, $total_fair_actual, $total_avg, $total_fair_avg, $total_detail_actual, $total_detail_avg, $total_reservation_actual, $total_reservation_avg);
            return $cur;
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return [];
        }
    }

    public static function flowAvgs($dataCSVClrepo)
    {
        try {
            $dataCSVClrepo = self::getRow($dataCSVClrepo);
            if (!$dataCSVClrepo) {
                throw new CustomException('No data in csv clrepo (build avgs) .');
            }
            $row = self::rowMax($dataCSVClrepo);

            $total_avg = (float)$dataCSVClrepo[$row][CSV::TOP_PAGE_PV_AREA_AVG] + (float)$dataCSVClrepo[$row][CSV::AREA_AVG_PLAN_PAGE_PV] + (float)$dataCSVClrepo[$row][CSV::PHOTO_GALLERY_PAGE_PV_AREA_AVG];
            $total_fair_avg = (float)$dataCSVClrepo[$row][CSV::FAIRS_PAGE_PV_AREA_AVG];
            $total_detail_avg = (float)$dataCSVClrepo[$row][CSV::FAIR_DETAILS_PAGE_PV_AREA_AVG];
            $total_reservation_avg = (float)$dataCSVClrepo[$row][CSV::VISIT_RESERVATIONS_CV_AREA_AVG];

            $fair_flow_avg = Helper::devisionAndPercent100($total_fair_avg, $total_avg);
            $fair_flow_avg = Image::numberFormat($fair_flow_avg);
            $detail_flow_avg = Helper::devisionAndPercent100($total_detail_avg, $total_fair_avg);
            $detail_flow_avg = Image::numberFormat($detail_flow_avg);
            $reservation_flow_avg = Helper::devisionAndPercent100($total_reservation_avg, $total_detail_avg);
            $reservation_flow_avg = Image::numberFormat($reservation_flow_avg);

            $avg = array($fair_flow_avg, $detail_flow_avg, $reservation_flow_avg);
            return $avg;
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return [];
        }
    }

    public static function flowActuals($dataCSVClrepo)
    {
        try {
            $dataCSVClrepo = self::getRow($dataCSVClrepo);
            if (!$dataCSVClrepo) {
                throw new CustomException('No data in csv clrepo (build actual) .');
            }
            $row = self::rowMax($dataCSVClrepo);

            $total_actual = (float)$dataCSVClrepo[$row][CSV::TOP_PAGE_PV] + (float)$dataCSVClrepo[$row][CSV::PLAN_PAGE_PV] + (float)$dataCSVClrepo[$row][CSV::PHOTO_GALLERY_PAGE_PV];
            $total_fair_actual = (float)$dataCSVClrepo[$row][CSV::BRIDAL_FAIRS_PAGE_PV];
            $total_detail_actual = (float)$dataCSVClrepo[$row][CSV::BRIDAL_FAIR_DETAILS_PAGE_PV];
            $total_reservation_actual = (float)$dataCSVClrepo[$row][CSV::TOTAL_RESERVATIONS];

            $fair_flow_actual = Helper::devisionAndPercent100($total_fair_actual, $total_actual);
            $fair_flow_actual = Image::numberFormat($fair_flow_actual);
            $detail_flow_actual = Helper::devisionAndPercent100($total_detail_actual, $total_fair_actual);
            $detail_flow_actual = Image::numberFormat($detail_flow_actual);
            $reservation_flow_actual = Helper::devisionAndPercent100($total_reservation_actual, $total_detail_actual);
            $reservation_flow_actual = Image::numberFormat($reservation_flow_actual);

            $actual = array($fair_flow_actual, $detail_flow_actual, $reservation_flow_actual);
            return $actual;
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return [];
        }
    }

    public static function flowResults($dataCSVClrepo, $summary_type)
    {
        $std = self::stds($dataCSVClrepo, $summary_type);
        if (!$std) {
            return false;
        }
        $cur = self::curs($dataCSVClrepo);
        if (!$cur) {
            return false;
        }
        $fair_flow_result = Helper::devisionAndPercent100($cur[0], $std[0]);
        $detail_flow_result = Helper::devisionAndPercent100($cur[1], $std[1]);
        $reservation_flow_result = Helper::devisionAndPercent100($cur[2], $std[2]);

        $result = array($fair_flow_result, $detail_flow_result, $reservation_flow_result);
        return $result;
    }

    public static function printIcon($dataCSVClrepo, $summary_type)
    {
        $icons = array(
            self::INDEX_ICON20    => HTML::SUN,
            self::INDEX_ICON21    => HTML::SUN,
            self::INDEX_ICON22    => HTML::SUN,
        );

        $flowResult = self::flowResults($dataCSVClrepo, $summary_type);
        if (!count($flowResult)) {
            return [];
        }

        $icons[self::INDEX_ICON20] = $flowResult[0] >= self::VALUE_ICON ? HTML::SUN : HTML::UMBRELLA;
        $icons[self::INDEX_ICON21] = $flowResult[1] >= self::VALUE_ICON ? HTML::SUN : HTML::UMBRELLA;
        $icons[self::INDEX_ICON22] = $flowResult[2] >= self::VALUE_ICON ? HTML::SUN : HTML::UMBRELLA;

        if ($flowResult[0] < 100 && $flowResult[1] < 100 && $flowResult[2] <100) {
            $icons = self::getIcons($icons, $flowResult);
        }

        return $icons;
    }

    public static function getIcons($icons, $flowResult)
    {
        switch (min($flowResult)) {
            case $flowResult[0]:
                $icons[self::INDEX_ICON20] = HTML::UMBRELLA;
                $icons[self::INDEX_ICON21] = ($flowResult[1] < $flowResult[2]) ? HTML::UMBRELLA: HTML::CLOUD;
                $icons[self::INDEX_ICON22] = ($flowResult[1] < $flowResult[2]) ? HTML::CLOUD: HTML::UMBRELLA;
                break;
            case $flowResult[1]:
                $icons[self::INDEX_ICON21] = HTML::UMBRELLA;
                $icons[self::INDEX_ICON20] = ($flowResult[1] < $flowResult[2]) ? HTML::UMBRELLA: HTML::CLOUD;
                $icons[self::INDEX_ICON22] = ($flowResult[1] < $flowResult[2]) ? HTML::CLOUD: HTML::UMBRELLA;
                break;
            case $flowResult[2]:
                $icons[self::INDEX_ICON22] = HTML::UMBRELLA;
                $icons[self::INDEX_ICON20] = ($flowResult[1] < $flowResult[2]) ? HTML::UMBRELLA: HTML::CLOUD;
                $icons[self::INDEX_ICON21] = ($flowResult[1] < $flowResult[2]) ? HTML::CLOUD: HTML::UMBRELLA;
                break;
        }

        return $icons;
    }

    public static function printFlowResult($dataCSVClrepo, $summary_type, $content)
    {
        $array = self::printIcon($dataCSVClrepo, $summary_type);
        foreach ($array as $key => $value) {
            $content = str_replace($key, public_path($value), $content); //phpcs:ignore
        }
        return $content;
    }

    public static function insertConclusion($dataCSVClrepo, $goal_type, $content)
    {
        try {
            $goal = CVImage::goal($dataCSVClrepo, $goal_type);
            $actual = CVImage::actual($dataCSVClrepo);
            $ratio = Helper::devision($actual, $goal);
            $text1 = '基本的には、目標に対して達成ベースで進捗しておりますので、急務な改善はございません。ただし、改善したほうがよい可能性のある個所を上記に「雨マーク」としてご提示させていただきますので、ご検討いただけると幸いです。';
            $text2 = 'この結果から、目標達成に向けて下記のご提案をさせて頂きます。';

            if (!$goal) {
                throw new CustomException('No data in csv (build CV goal)');
            }
            if ($ratio == CVImage::SUN_NUMBER_EQUAL || $ratio >= CVImage::SUN_NUMBER_BIGGER) {
                $content = str_replace('CONTAINER_HEIGHT', self::calculatePVHeight(Constant::SIZE_IMAGE_PV, $goal_type), $content);
                return [
                    Constant::SIZE_IMAGE_PV,
                    str_replace("CONCLUSION", $text1, $content)
                ];
            }
            $content = str_replace('CONTAINER_HEIGHT', self::calculatePVHeight(Constant::SIZE_IMAGE_PV_ONE_LINE, $goal_type), $content);
            return [
                Constant::SIZE_IMAGE_PV_ONE_LINE,
                str_replace("CONCLUSION", $text2, $content)
            ];
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return [];
        }
    }

    private static function calculatePVHeight($sizeImagePV, $goal_type)
    {
        $heightPV = explode(',', HTML::SIZE_IMAGE[$sizeImagePV])[1];
        if ($goal_type == HTML::GOAL_TYPE_1 || $goal_type == HTML::GOAL_TYPE_2) {
            $heightPV += Constant::HEIGHT_ADDED_WHEN_GOAL_TYPE_IS2_AND_IS3;
        }
        return $heightPV - Constant::DIFFERENCE_NUMBER_OF_HEIGHT_BETWEEN_HTML_AND_HEADLESS;
    }
}
