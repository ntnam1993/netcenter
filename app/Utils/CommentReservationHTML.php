<?php


namespace App\Utils;

use App\Utils\CommentFairDetailHTML;
use App\Utils\CommentFairListHTML;
use App\Utils\Helper;

class CommentReservationHTML
{
    public static function buildHtmlCommentCReservation1($dates)
    {
        return CommentFairDetailHTML::buildHtmlCommentWithDates(Constant::COMMENT_C_RESERVATION_1_HTML_PATH, Constant::COMMENT_C_RESERVATION_1_LAYOUT_PATH, $dates);
    }

    public static function buildHtmlCommentCReservation2($dates)
    {
        return CommentFairDetailHTML::buildHtmlCommentWithDates(Constant::COMMENT_C_RESERVATION_2_HTML_PATH, Constant::COMMENT_C_RESERVATION_2_LAYOUT_PATH, $dates);
    }

    public static function buildHtmlCommentCReservation3()
    {
        return CommentFairListHTML::buildHtmlComment(Constant::COMMENT_C_RESERVATION_3_HTML_PATH, Constant::COMMENT_C_RESERVATION_3_LAYOUT_PATH);
    }

    public static function buildHtmlCommentCReservation4($dates)
    {
        return CommentFairDetailHTML::buildHtmlCommentWithDates(Constant::COMMENT_C_RESERVATION_4_HTML_PATH, Constant::COMMENT_C_RESERVATION_4_LAYOUT_PATH, $dates);
    }

    public static function buildHtmlCommentCReservation5($dates)
    {
        return CommentFairDetailHTML::buildHtmlCommentWithDates(Constant::COMMENT_C_RESERVATION_5_HTML_PATH, Constant::COMMENT_C_RESERVATION_5_LAYOUT_PATH, $dates);
    }

    public static function buildHtmlCommentCReservation6($dates)
    {
        return CommentFairDetailHTML::buildHtmlCommentWithDates(Constant::COMMENT_C_RESERVATION_6_HTML_PATH, Constant::COMMENT_C_RESERVATION_6_LAYOUT_PATH, $dates);
    }

    public static function buildHtmlCommentCReservation7($dates)
    {
        return CommentFairDetailHTML::buildHtmlCommentWithDates(Constant::COMMENT_C_RESERVATION_7_HTML_PATH, Constant::COMMENT_C_RESERVATION_7_LAYOUT_PATH, $dates);
    }

    public static function buildHtmlCommentCReservation8($dates)
    {
        return CommentFairDetailHTML::buildHtmlCommentWithDates(Constant::COMMENT_C_RESERVATION_8_HTML_PATH, Constant::COMMENT_C_RESERVATION_8_LAYOUT_PATH, $dates);
    }

    public static function buildHtmlCommentCReservation9($dates)
    {
        return CommentFairDetailHTML::buildHtmlCommentWithDates(Constant::COMMENT_C_RESERVATION_9_HTML_PATH, Constant::COMMENT_C_RESERVATION_9_LAYOUT_PATH, $dates);
    }

    public static function buildHtmlCommentCReservation10($dates)
    {
        return CommentFairDetailHTML::buildHtmlCommentWithDates(Constant::COMMENT_C_RESERVATION_10_HTML_PATH, Constant::COMMENT_C_RESERVATION_10_LAYOUT_PATH, $dates);
    }

    public static function buildHtmlCommentCReservation12($dates)
    {
        return CommentFairDetailHTML::buildHtmlCommentWithDates(Constant::COMMENT_C_RESERVATION_12_HTML_PATH, Constant::COMMENT_C_RESERVATION_12_LAYOUT_PATH, $dates);
    }

    public static function buildHtmlCommentCReservation13($dates)
    {
        return CommentFairDetailHTML::buildHtmlCommentWithDates(Constant::COMMENT_C_RESERVATION_13_HTML_PATH, Constant::COMMENT_C_RESERVATION_13_LAYOUT_PATH, $dates);
    }

    public static function buildHtmlCommentCReservation14($dates)
    {
        return CommentFairDetailHTML::buildHtmlCommentWithDates(Constant::COMMENT_C_RESERVATION_14_HTML_PATH, Constant::COMMENT_C_RESERVATION_14_LAYOUT_PATH, $dates);
    }

    public static function buildHtmlCommentCReservation15($dates)
    {
        return CommentFairDetailHTML::buildHtmlCommentWithDates(Constant::COMMENT_C_RESERVATION_15_HTML_PATH, Constant::COMMENT_C_RESERVATION_15_LAYOUT_PATH, $dates);
    }

    public static function buildHtmlCommentCReservation16($dataCommentC16)
    {
        $handle = fopen(resource_path(Constant::COMMENT_C_RESERVATION_16_HTML_PATH), 'w');

        $content = file_get_contents(resource_path(Constant::COMMENT_C_RESERVATION_16_LAYOUT_PATH));

        $dateMaxCs = [];
        for ($i = 0; $i < 3; $i++) {
            if (isset($dataCommentC16[$i]) && ($dataCommentC16[$i]['date'] != '')) {
                $dateMaxCs[Helper::dateFormat($dataCommentC16[$i]['date'], 'Ymd')] = CommentFairDetailHTML::formatDateGetMonthAndDate($dataCommentC16[$i]['date']);
            }
            $content = str_replace("DATE".$i, !isset($dataCommentC16[$i]) || ($dataCommentC16[$i]['date'] == '') ? '' : CommentFairDetailHTML::formatDateGetMonthAndDate($dataCommentC16[$i]['date']), $content);
            $content = str_replace("FAIR_NMS".$i, !isset($dataCommentC16[$i]) || ($dataCommentC16[$i]['fair_nm'] == '') ? '' : $dataCommentC16[$i]['fair_nm'], $content);
            $content = str_replace("PAGE_VIEW".$i, !isset($dataCommentC16[$i]) || ($dataCommentC16[$i]['page_view'] == '') ? '' : $dataCommentC16[$i]['page_view'], $content);
        }

        ksort($dateMaxCs);
        $content = str_replace("DATE_MAX_C", implode("、 ", $dateMaxCs), $content);

        fwrite($handle, $content);

        fclose($handle);

        return resource_path(Constant::COMMENT_C_RESERVATION_16_HTML_PATH);
    }
}
