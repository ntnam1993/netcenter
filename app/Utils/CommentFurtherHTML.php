<?php


namespace App\Utils;

use App\Utils\CommentFairListHTML;
use App\Utils\CommentFairDetailHTML;

class CommentFurtherHTML
{
    public static function buildHtmlCommentDFurther1($shop)
    {
        return CommentFairListHTML::buildHtmlCommentWithThreshold(Constant::COMMENT_D_FURTHER_1_HTML_PATH, Constant::COMMENT_D_FURTHER_1_LAYOUT_PATH, 'D1_THRESHOLD', $shop['d1_threshold']);
    }

    public static function buildHtmlCommentDFurther2()
    {
        return CommentFairListHTML::buildHtmlComment(Constant::COMMENT_D_FURTHER_2_HTML_PATH, Constant::COMMENT_D_FURTHER_2_LAYOUT_PATH);
    }

    public static function buildHtmlCommentDFurther3($dates)
    {
        return CommentFairDetailHTML::buildHtmlCommentWithDates(Constant::COMMENT_D_FURTHER_3_HTML_PATH, Constant::COMMENT_D_FURTHER_3_LAYOUT_PATH, $dates);
    }

    public static function buildHtmlCommentDFurther4()
    {
        return CommentFairListHTML::buildHtmlComment(Constant::COMMENT_D_FURTHER_4_HTML_PATH, Constant::COMMENT_D_FURTHER_4_LAYOUT_PATH);
    }
}
