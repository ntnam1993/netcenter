<?php


namespace App\Utils;

class CommentFairListHTML
{
    public static function buildHtmlComment($htmlPath, $layoutPath)
    {
        $handle = fopen(resource_path($htmlPath), 'w');

        $content = file_get_contents(resource_path($layoutPath));

        fwrite($handle, $content);

        fclose($handle);

        return resource_path($htmlPath);
    }

    public static function buildHtmlCommentWithThreshold($htmlPath, $layoutPath, $aThreshold, $shopAThreshold)
    {
        $handle = fopen(resource_path($htmlPath), 'w');

        $content = file_get_contents(resource_path($layoutPath));
        $content = str_replace($aThreshold, $shopAThreshold, $content);

        fwrite($handle, $content);

        fclose($handle);

        return resource_path($htmlPath);
    }

    public static function buildHtmlCommentATop1($shop)
    {
        return self::buildHtmlCommentWithThreshold(Constant::COMMENT_A_TOP_1_HTML_PATH, Constant::COMMENT_A_TOP_1_LAYOUT_PATH, "A1_THRESHOLD", $shop['a1_threshold']);
    }

    public static function buildHtmlCommentATop2()
    {
        return self::buildHtmlComment(Constant::COMMENT_A_TOP_2_HTML_PATH, Constant::COMMENT_A_TOP_2_LAYOUT_PATH);
    }

    public static function buildHtmlCommentATop3()
    {
        return self::buildHtmlComment(Constant::COMMENT_A_TOP_3_HTML_PATH, Constant::COMMENT_A_TOP_3_LAYOUT_PATH);
    }

    public static function buildHtmlCommentAPhotoGallery()
    {
        return self::buildHtmlComment(Constant::COMMENT_A_PHOTO_GALLERY_HTML_PATH, Constant::COMMENT_A_PHOTO_GALLERY_LAYOUT_PATH);
    }

    public static function buildHtmlCommentAPlan1($shop)
    {
        return self::buildHtmlCommentWithThreshold(Constant::COMMENT_A_PLAN_1_HTML_PATH, Constant::COMMENT_A_PLAN_1_LAYOUT_PATH, "A5_THRESHOLD", $shop['a5_threshold']);
    }

    public static function buildHtmlCommentAPlan2()
    {
        return self::buildHtmlComment(Constant::COMMENT_A_PLAN_2_HTML_PATH, Constant::COMMENT_A_PLAN_2_LAYOUT_PATH);
    }

    public static function buildHtmlCommentAPlan3($shop)
    {
        return self::buildHtmlCommentWithThreshold(Constant::COMMENT_A_PLAN_3_HTML_PATH, Constant::COMMENT_A_PLAN_3_LAYOUT_PATH, "A7_THRESHOLD", $shop['a7_threshold']);
    }

    public static function buildHtmlCommentAPlan4()
    {
        return self::buildHtmlComment(Constant::COMMENT_A_PLAN_4_HTML_PATH, Constant::COMMENT_A_PLAN_4_LAYOUT_PATH);
    }
}
