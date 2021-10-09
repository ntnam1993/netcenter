<?php


namespace App\Utils;

use App\Utils\CommentFairListHTML;
use App\Utils\Helper;

class CommentFairDetailHTML
{
    public static function buildHtmlCommentWithDates($htmlPath, $layoutPath, $dates)
    {
        $handle = fopen(resource_path($htmlPath), 'w');

        $date = self::convertDatesToString($dates);
        $content = file_get_contents(resource_path($layoutPath));
        $content = str_replace("DATE", $date, $content);

        fwrite($handle, $content);

        fclose($handle);

        return resource_path($htmlPath);
    }

    private static function convertDatesToString($dates)
    {
        $datesFormat = array();
        for ($i = 0; $i < count((array)$dates); $i++) {
            $date = self::formatDateGetMonthAndDate($dates[$i]);
            array_push($datesFormat, $date);
        }
        return implode("、 ", $datesFormat);
    }

    public static function buildHtmlCommentBFairDetail1()
    {
        return CommentFairListHTML::buildHtmlComment(Constant::COMMENT_B_FAIR_DETAIL_1_HTML_PATH, Constant::COMMENT_B_FAIR_DETAIL_1_LAYOUT_PATH);
    }

    public static function buildHtmlCommentBFairDetail2()
    {
        return CommentFairListHTML::buildHtmlComment(Constant::COMMENT_B_FAIR_DETAIL_2_HTML_PATH, Constant::COMMENT_B_FAIR_DETAIL_2_LAYOUT_PATH);
    }

    public static function buildHtmlCommentBFairDetail3($dates)
    {
        return self::buildHtmlCommentWithDates(Constant::COMMENT_B_FAIR_DETAIL_3_HTML_PATH, Constant::COMMENT_B_FAIR_DETAIL_3_LAYOUT_PATH, $dates);
    }

    public static function buildHtmlCommentBFairDetail4()
    {
        return CommentFairListHTML::buildHtmlComment(Constant::COMMENT_B_FAIR_DETAIL_4_HTML_PATH, Constant::COMMENT_B_FAIR_DETAIL_4_LAYOUT_PATH);
    }

    public static function buildHtmlCommentBFairDetail5($dates)
    {
        return self::buildHtmlCommentWithDates(Constant::COMMENT_B_FAIR_DETAIL_5_HTML_PATH, Constant::COMMENT_B_FAIR_DETAIL_5_LAYOUT_PATH, $dates);
    }

    public static function buildHtmlCommentBFairDetail6($dates)
    {
        return self::buildHtmlCommentWithDates(Constant::COMMENT_B_FAIR_DETAIL_6_HTML_PATH, Constant::COMMENT_B_FAIR_DETAIL_6_LAYOUT_PATH, $dates);
    }

    public static function buildHtmlCommentBFairDetail7($dates)
    {
        return self::buildHtmlCommentWithDates(Constant::COMMENT_B_FAIR_DETAIL_7_HTML_PATH, Constant::COMMENT_B_FAIR_DETAIL_7_LAYOUT_PATH, $dates);
    }

    public static function buildHtmlCommentBFairDetail8($dates)
    {
        return self::buildHtmlCommentWithDates(Constant::COMMENT_B_FAIR_DETAIL_8_HTML_PATH, Constant::COMMENT_B_FAIR_DETAIL_8_LAYOUT_PATH, $dates);
    }

    public static function buildHtmlCommentBFairDetail9($dates)
    {
        return self::buildHtmlCommentWithDates(Constant::COMMENT_B_FAIR_DETAIL_9_HTML_PATH, Constant::COMMENT_B_FAIR_DETAIL_9_LAYOUT_PATH, $dates);
    }

    public static function buildHtmlCommentBFairDetail10($dataCommentB10)
    {
        $handle = fopen(resource_path(Constant::COMMENT_B_FAIR_DETAIL_10_HTML_PATH), 'w');

        $content = file_get_contents(resource_path(Constant::COMMENT_B_FAIR_DETAIL_10_LAYOUT_PATH));

        $dateMaxCs = [];
        for ($i = 0; $i < 3; $i++) {
            if (isset($dataCommentB10[$i]) && ($dataCommentB10[$i]['date'] != '')) {
                $dateMaxCs[Helper::dateFormat($dataCommentB10[$i]['date'], 'Ymd')] = CommentFairDetailHTML::formatDateGetMonthAndDate($dataCommentB10[$i]['date']);
            }
            $content = str_replace("DATE".$i, !isset($dataCommentB10[$i]) || ($dataCommentB10[$i]['date'] == '') ? '' : self::formatDateGetMonthAndDate($dataCommentB10[$i]['date']), $content);
            $content = str_replace("FAIR_NMS".$i, !isset($dataCommentB10[$i]) || ($dataCommentB10[$i]['fair_nm'] == '') ? '' : $dataCommentB10[$i]['fair_nm'], $content);
            $content = str_replace("PAGE_VIEW".$i, !isset($dataCommentB10[$i]) || ($dataCommentB10[$i]['page_view'] == '') ? '' : $dataCommentB10[$i]['page_view'], $content);
        }

        ksort($dateMaxCs);

        $content = str_replace("DATE_MAX_C", implode("、 ", $dateMaxCs), $content);

        fwrite($handle, $content);

        fclose($handle);

        return resource_path(Constant::COMMENT_B_FAIR_DETAIL_10_HTML_PATH);
    }

    public static function formatDateGetMonthAndDate($date)
    {
        return date("n/j", strtotime($date));
    }
}
