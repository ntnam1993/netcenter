<?php

namespace App\Utils;

class Constant
{
    const IMAGE_CV_NAME = 'CV'; //'A' added to name to sort it in top when get list image to add EML
    const IMAGE_PV_NAME = 'PV'; //'A' added to name to sort it in top when get list image to add EML and only after CV

    const IMAGE_COMMENT_A_TOP_1_NAME = 'typeCommentABTop1'; //'B' added to name to sort it in top when get list image to add EML
    const IMAGE_COMMENT_A_TOP_2_NAME = 'typeCommentABTop2'; //'B' added to name to sort it in top when get list image to add EML
    const IMAGE_COMMENT_A_TOP_3_NAME = 'typeCommentABTop3'; //'B' added to name to sort it in top when get list image to add EML
    const IMAGE_COMMENT_A_PHOTO_GALLERY_NAME = 'typeCommentAPhotoGallery';
    const IMAGE_COMMENT_A_PLAN_1_NAME = 'typeCommentAPlan1';
    const IMAGE_COMMENT_A_PLAN_2_NAME = 'typeCommentAPlan2';
    const IMAGE_COMMENT_A_PLAN_3_NAME = 'typeCommentAPlan3';
    const IMAGE_COMMENT_A_PLAN_4_NAME = 'typeCommentAPlan4';

    const IMAGE_COMMENT_B_FAIR_DETAIL_1 = 'typeCommentB1';
    const IMAGE_COMMENT_B_FAIR_DETAIL_2 = 'typeCommentB2';
    const IMAGE_COMMENT_B_FAIR_DETAIL_3 = 'typeCommentB3';
    const IMAGE_COMMENT_B_FAIR_DETAIL_4 = 'typeCommentB4';
    const IMAGE_COMMENT_B_FAIR_DETAIL_5 = 'typeCommentB5';
    const IMAGE_COMMENT_B_FAIR_DETAIL_6 = 'typeCommentB6';
    const IMAGE_COMMENT_B_FAIR_DETAIL_7 = 'typeCommentB7';
    const IMAGE_COMMENT_B_FAIR_DETAIL_8 = 'typeCommentB8';
    const IMAGE_COMMENT_B_FAIR_DETAIL_9 = 'typeCommentB9';
    const IMAGE_COMMENT_B_FAIR_DETAIL_10 = 'typeCommentBB10';

    const IMAGE_COMMENT_C_RESERVATION_1 = 'typeCommentC1';
    const IMAGE_COMMENT_C_RESERVATION_2 = 'typeCommentC2';
    const IMAGE_COMMENT_C_RESERVATION_3 = 'typeCommentC3';
    const IMAGE_COMMENT_C_RESERVATION_4 = 'typeCommentC4';
    const IMAGE_COMMENT_C_RESERVATION_5 = 'typeCommentC5';
    const IMAGE_COMMENT_C_RESERVATION_6 = 'typeCommentC6';
    const IMAGE_COMMENT_C_RESERVATION_7 = 'typeCommentC7';
    const IMAGE_COMMENT_C_RESERVATION_8 = 'typeCommentC8';
    const IMAGE_COMMENT_C_RESERVATION_9 = 'typeCommentC9';
    const IMAGE_COMMENT_C_RESERVATION_10 = 'typeCommentCC10';
    const IMAGE_COMMENT_C_RESERVATION_12 = 'typeCommentCC12';
    const IMAGE_COMMENT_C_RESERVATION_13 = 'typeCommentCC13';
    const IMAGE_COMMENT_C_RESERVATION_14 = 'typeCommentCC14';
    const IMAGE_COMMENT_C_RESERVATION_15 = 'typeCommentCC15';
    const IMAGE_COMMENT_C_RESERVATION_16 = 'typeCommentCC16';

    const IMAGE_COMMENT_D_FURTHER_1 = 'typeCommentD1';
    const IMAGE_COMMENT_D_FURTHER_2 = 'typeCommentD2';
    const IMAGE_COMMENT_D_FURTHER_3 = 'typeCommentD3';
    const IMAGE_COMMENT_D_FURTHER_4 = 'typeCommentD4';

    const CV_HTML_PATH = 'views/templateHTML/'.self::IMAGE_CV_NAME.'.html';
    const CV_LAYOUT_PATH = 'views/templateHTML/layoutCV.html';
    const PV_HTML_PATH = 'views/templateHTML/'.self::IMAGE_PV_NAME.'.html';
    const PV_LAYOUT_PATH = 'views/templateHTML/layoutPV.html';

    const COMMENT_A_TOP_1_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_A_TOP_1_NAME.'.html';
    const COMMENT_A_TOP_1_LAYOUT_PATH = 'views/templateHTML/layoutCommentATop1.html';
    const COMMENT_A_TOP_2_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_A_TOP_2_NAME.'.html';
    const COMMENT_A_TOP_2_LAYOUT_PATH = 'views/templateHTML/layoutCommentATop2.html';
    const COMMENT_A_TOP_3_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_A_TOP_3_NAME.'.html';
    const COMMENT_A_TOP_3_LAYOUT_PATH = 'views/templateHTML/layoutCommentATop3.html';
    const COMMENT_A_PHOTO_GALLERY_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_A_PHOTO_GALLERY_NAME.'.html';
    const COMMENT_A_PHOTO_GALLERY_LAYOUT_PATH = 'views/templateHTML/layoutCommentAPhotoGallery.html';
    const COMMENT_A_PLAN_1_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_A_PLAN_1_NAME.'.html';
    const COMMENT_A_PLAN_1_LAYOUT_PATH = 'views/templateHTML/layoutCommentAPlan1.html';
    const COMMENT_A_PLAN_2_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_A_PLAN_2_NAME.'.html';
    const COMMENT_A_PLAN_2_LAYOUT_PATH = 'views/templateHTML/layoutCommentAPlan2.html';
    const COMMENT_A_PLAN_3_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_A_PLAN_3_NAME.'.html';
    const COMMENT_A_PLAN_3_LAYOUT_PATH = 'views/templateHTML/layoutCommentAPlan3.html';
    const COMMENT_A_PLAN_4_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_A_PLAN_4_NAME.'.html';
    const COMMENT_A_PLAN_4_LAYOUT_PATH = 'views/templateHTML/layoutCommentAPlan4.html';

    const COMMENT_B_FAIR_DETAIL_1_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_B_FAIR_DETAIL_1.'.html';
    const COMMENT_B_FAIR_DETAIL_1_LAYOUT_PATH = 'views/templateHTML/layoutCommentB1.html';
    const COMMENT_B_FAIR_DETAIL_2_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_B_FAIR_DETAIL_2.'.html';
    const COMMENT_B_FAIR_DETAIL_2_LAYOUT_PATH = 'views/templateHTML/layoutCommentB2.html';
    const COMMENT_B_FAIR_DETAIL_3_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_B_FAIR_DETAIL_3.'.html';
    const COMMENT_B_FAIR_DETAIL_3_LAYOUT_PATH = 'views/templateHTML/layoutCommentB3.html';
    const COMMENT_B_FAIR_DETAIL_4_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_B_FAIR_DETAIL_4.'.html';
    const COMMENT_B_FAIR_DETAIL_4_LAYOUT_PATH = 'views/templateHTML/layoutCommentB4.html';
    const COMMENT_B_FAIR_DETAIL_5_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_B_FAIR_DETAIL_5.'.html';
    const COMMENT_B_FAIR_DETAIL_5_LAYOUT_PATH = 'views/templateHTML/layoutCommentB5.html';
    const COMMENT_B_FAIR_DETAIL_6_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_B_FAIR_DETAIL_6.'.html';
    const COMMENT_B_FAIR_DETAIL_6_LAYOUT_PATH = 'views/templateHTML/layoutCommentB6.html';
    const COMMENT_B_FAIR_DETAIL_7_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_B_FAIR_DETAIL_7.'.html';
    const COMMENT_B_FAIR_DETAIL_7_LAYOUT_PATH = 'views/templateHTML/layoutCommentB7.html';
    const COMMENT_B_FAIR_DETAIL_8_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_B_FAIR_DETAIL_8.'.html';
    const COMMENT_B_FAIR_DETAIL_8_LAYOUT_PATH = 'views/templateHTML/layoutCommentB8.html';
    const COMMENT_B_FAIR_DETAIL_9_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_B_FAIR_DETAIL_9.'.html';
    const COMMENT_B_FAIR_DETAIL_9_LAYOUT_PATH = 'views/templateHTML/layoutCommentB9.html';
    const COMMENT_B_FAIR_DETAIL_10_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_B_FAIR_DETAIL_10.'.html';
    const COMMENT_B_FAIR_DETAIL_10_LAYOUT_PATH = 'views/templateHTML/layoutCommentB10.html';

    const COMMENT_C_RESERVATION_1_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_C_RESERVATION_1.'.html';
    const COMMENT_C_RESERVATION_1_LAYOUT_PATH = 'views/templateHTML/layoutCommentC1.html';
    const COMMENT_C_RESERVATION_2_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_C_RESERVATION_2.'.html';
    const COMMENT_C_RESERVATION_2_LAYOUT_PATH = 'views/templateHTML/layoutCommentC2.html';
    const COMMENT_C_RESERVATION_3_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_C_RESERVATION_3.'.html';
    const COMMENT_C_RESERVATION_3_LAYOUT_PATH = 'views/templateHTML/layoutCommentC3.html';
    const COMMENT_C_RESERVATION_4_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_C_RESERVATION_4.'.html';
    const COMMENT_C_RESERVATION_4_LAYOUT_PATH = 'views/templateHTML/layoutCommentC4.html';
    const COMMENT_C_RESERVATION_5_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_C_RESERVATION_5.'.html';
    const COMMENT_C_RESERVATION_5_LAYOUT_PATH = 'views/templateHTML/layoutCommentC5.html';
    const COMMENT_C_RESERVATION_6_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_C_RESERVATION_6.'.html';
    const COMMENT_C_RESERVATION_6_LAYOUT_PATH = 'views/templateHTML/layoutCommentC6.html';
    const COMMENT_C_RESERVATION_7_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_C_RESERVATION_7.'.html';
    const COMMENT_C_RESERVATION_7_LAYOUT_PATH = 'views/templateHTML/layoutCommentC7.html';
    const COMMENT_C_RESERVATION_8_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_C_RESERVATION_8.'.html';
    const COMMENT_C_RESERVATION_8_LAYOUT_PATH = 'views/templateHTML/layoutCommentC8.html';
    const COMMENT_C_RESERVATION_9_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_C_RESERVATION_9.'.html';
    const COMMENT_C_RESERVATION_9_LAYOUT_PATH = 'views/templateHTML/layoutCommentC9.html';
    const COMMENT_C_RESERVATION_10_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_C_RESERVATION_10.'.html';
    const COMMENT_C_RESERVATION_10_LAYOUT_PATH = 'views/templateHTML/layoutCommentC10.html';
    const COMMENT_C_RESERVATION_12_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_C_RESERVATION_12.'.html';
    const COMMENT_C_RESERVATION_12_LAYOUT_PATH = 'views/templateHTML/layoutCommentC12.html';
    const COMMENT_C_RESERVATION_13_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_C_RESERVATION_13.'.html';
    const COMMENT_C_RESERVATION_13_LAYOUT_PATH = 'views/templateHTML/layoutCommentC13.html';
    const COMMENT_C_RESERVATION_14_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_C_RESERVATION_14.'.html';
    const COMMENT_C_RESERVATION_14_LAYOUT_PATH = 'views/templateHTML/layoutCommentC14.html';
    const COMMENT_C_RESERVATION_15_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_C_RESERVATION_15.'.html';
    const COMMENT_C_RESERVATION_15_LAYOUT_PATH = 'views/templateHTML/layoutCommentC15.html';
    const COMMENT_C_RESERVATION_16_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_C_RESERVATION_16.'.html';
    const COMMENT_C_RESERVATION_16_LAYOUT_PATH = 'views/templateHTML/layoutCommentC16.html';

    const COMMENT_D_FURTHER_1_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_D_FURTHER_1.'.html';
    const COMMENT_D_FURTHER_1_LAYOUT_PATH = 'views/templateHTML/layoutCommentD1.html';
    const COMMENT_D_FURTHER_2_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_D_FURTHER_2.'.html';
    const COMMENT_D_FURTHER_2_LAYOUT_PATH = 'views/templateHTML/layoutCommentD2.html';
    const COMMENT_D_FURTHER_3_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_D_FURTHER_3.'.html';
    const COMMENT_D_FURTHER_3_LAYOUT_PATH = 'views/templateHTML/layoutCommentD3.html';
    const COMMENT_D_FURTHER_4_HTML_PATH = 'views/templateHTML/'.self::IMAGE_COMMENT_D_FURTHER_4.'.html';
    const COMMENT_D_FURTHER_4_LAYOUT_PATH = 'views/templateHTML/layoutCommentD4.html';

    const ATTACHMENT_PATH = 'views/attachmentFile/';
    const EML_PATH = 'views/emlFiles/';
    const IMAGE_PATH = 'images/';
    const CSV_PATH = 'views/csv/';
    const LOCAL_ENV = 'local';
    const ATTACHMENT_PDF_TIPS_PATH = 'views/PDFtips/fair_title_tips.pdf';
    const PDF_TIPS_FILE_NAME = 'fair_title_tips.pdf';

    const HEADER_EML_PATH = 'views/templateEML/header.eml';
    const HEADER_MULTI_SHOP_EML_PATH = 'views/templateEML/header_multi.eml';
    const HEADER_CID_EML_PATH = 'views/templateEML/header_cid.eml';
    const CONTENT_IMAGE_EML_PATH = 'views/templateEML/content_image.eml';
    const CONTENT_ATTACHMENT_EML_PATH = 'views/templateEML/content_attachment.eml';
    const NO_ATTACHMENT_EML_PATH = 'views/templateEML/no_attachment.eml';
    const CLOSE_ATTACHMENT_EML_PATH = 'views/templateEML/close_attachment.eml';
    const CLOSE_IMAGE_EML_PATH = 'views/templateEML/close_image.eml';

    const KEY_CHECK_PLAN_SHUBETSU_KBN = '04';
    const KEY_CHECK_NUMBER_OF_PHOTO_GALLERY = 20;
    const HEIGHT_ADDED_WHEN_GOAL_TYPE_IS2_AND_IS3 = 20;
    const DIFFERENCE_NUMBER_OF_HEIGHT_BETWEEN_HTML_AND_HEADLESS = 30;

    const SIZE_IMAGE_CV = '1';
    const SIZE_IMAGE_PV = '2';
    const SIZE_IMAGE_PV_ONE_LINE = '3';

    const SECOND_IN_ONE_DAY = 86400;
    const LIMIT_COUNT_OF_DATA_IN_FAIR_DETAIL_10 = 3;
    const CHECK_COUNT_REPORT_PLUS_IMAGE = 3;
    const GET_TOP_3_OF_PLAN = 3;
    const KEY_CHECK_NUMBER_OF_TOUR_COUNT = 2;

    const SIZE_IMAGE_COMMENT_A_TOP_1 = 'SIZE_IMAGE_A_TOP_1';
    const SIZE_IMAGE_COMMENT_A_TOP_2 = 'SIZE_IMAGE_A_TOP_2';
    const SIZE_IMAGE_COMMENT_A_TOP_3 = 'SIZE_IMAGE_A_TOP_3';
    const SIZE_IMAGE_COMMENT_A_PHOTO_GALLERY = 'SIZE_IMAGE_A_PHOTO_GALLERY';
    const SIZE_IMAGE_COMMENT_A_PLAN_1 = 'SIZE_IMAGE_A_PLAN_1';
    const SIZE_IMAGE_COMMENT_A_PLAN_2 = 'SIZE_IMAGE_A_PLAN_2';
    const SIZE_IMAGE_COMMENT_A_PLAN_3 = 'SIZE_IMAGE_A_PLAN_3';
    const SIZE_IMAGE_COMMENT_A_PLAN_4 = 'SIZE_IMAGE_A_PLAN_4';

    const SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_1 = 'SIZE_IMAGE_B_1';
    const SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_2 = 'SIZE_IMAGE_B_2';
    const SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_3 = 'SIZE_IMAGE_B_3';
    const SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_4 = 'SIZE_IMAGE_B_4';
    const SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_5 = 'SIZE_IMAGE_B_5';
    const SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_6 = 'SIZE_IMAGE_B_6';
    const SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_7 = 'SIZE_IMAGE_B_7';
    const SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_8 = 'SIZE_IMAGE_B_8';
    const SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_9 = 'SIZE_IMAGE_B_9';
    const SIZE_IMAGE_COMMENT_B_FAIR_DETAIL_10 = 'SIZE_IMAGE_B_10';

    const SIZE_IMAGE_COMMENT_C_RESERVATION_1 = 'SIZE_IMAGE_C_1';
    const SIZE_IMAGE_COMMENT_C_RESERVATION_2 = 'SIZE_IMAGE_C_2';
    const SIZE_IMAGE_COMMENT_C_RESERVATION_3 = 'SIZE_IMAGE_C_3';
    const SIZE_IMAGE_COMMENT_C_RESERVATION_4 = 'SIZE_IMAGE_C_4';
    const SIZE_IMAGE_COMMENT_C_RESERVATION_5 = 'SIZE_IMAGE_C_5';
    const SIZE_IMAGE_COMMENT_C_RESERVATION_6 = 'SIZE_IMAGE_C_6';
    const SIZE_IMAGE_COMMENT_C_RESERVATION_7 = 'SIZE_IMAGE_C_7';
    const SIZE_IMAGE_COMMENT_C_RESERVATION_8 = 'SIZE_IMAGE_C_8';
    const SIZE_IMAGE_COMMENT_C_RESERVATION_9 = 'SIZE_IMAGE_C_9';
    const SIZE_IMAGE_COMMENT_C_RESERVATION_10 = 'SIZE_IMAGE_C_10';
    const SIZE_IMAGE_COMMENT_C_RESERVATION_12 = 'SIZE_IMAGE_C_12';
    const SIZE_IMAGE_COMMENT_C_RESERVATION_13 = 'SIZE_IMAGE_C_13';
    const SIZE_IMAGE_COMMENT_C_RESERVATION_14 = 'SIZE_IMAGE_C_14';
    const SIZE_IMAGE_COMMENT_C_RESERVATION_15 = 'SIZE_IMAGE_C_15';
    const SIZE_IMAGE_COMMENT_C_RESERVATION_16 = 'SIZE_IMAGE_C_16';

    const SIZE_IMAGE_COMMENT_D_FURTHER_1 = 'SIZE_IMAGE_D_1';
    const SIZE_IMAGE_COMMENT_D_FURTHER_2 = 'SIZE_IMAGE_D_2';
    const SIZE_IMAGE_COMMENT_D_FURTHER_3 = 'SIZE_IMAGE_D_3';
    const SIZE_IMAGE_COMMENT_D_FURTHER_4 = 'SIZE_IMAGE_D_4';

    public static function getURLCSVClrepo()
    {
        return '/csv_file/'.date('Ymd').'/clrepo.csv';
    }

    public static function getURLCSVIchioshiIcon()
    {
        return '/csv_file/'.date('Ymd').'/ichioshi_icon';
    }

    public static function getURLPDFTips()
    {
        return '/fair_title_tips.pdf';
    }

    public static function getURLXLSX($s_code)
    {
        return '/client_report/'.date('Ymd').'/'.$s_code.'.xlsx';
    }

    public static function getURLPDF($s_code)
    {
        return '/competitor_info/'.date('Ymd').'/'.$s_code.'.pdf';
    }
}
