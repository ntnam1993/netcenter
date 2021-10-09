<?php


namespace App\Utils;

class CSV
{
    const ISSUE_MONTH = 0;
    const WEEK_NUMBER = 1;
    const S_CODE = 2;
    const AREA_CODE = 3;
    const PACK = 4;
    const BASIC_INFO_PAGE_PV = 5;
    const REPOPLA_PAGE_PV = 6;
    const BRIDAL_FAIRS_PAGE_PV = 7;
    const BRIDAL_FAIR_DETAILS_PAGE_PV = 8;
    const PHOTO_GALLERY_PAGE_PV = 9;
    const TOTAL_RESERVATIONS = 10;
    const UU_LOVELY_HIMEKO_TYPE = 11;
    const UU_GREEDY_HANAKO_TYPE = 12;
    const UU_STEADY_TAKAKO_TYPE = 13;
    const UU_CLASSIC_KAZUKO_TYPE = 14;
    const UU_NORMAL_TSUNEKO_TYPE = 15;
    const UU_FRIENDLY_EMIKO_TYPE = 16;
    const UU_FAMILY_FUMIKO_TYPE = 17;
    const UU_LAID_BACK_MICHIKO_TYPE = 18;
    const LOVELY_HIMEKO_TYPE_RESERVATIONS = 19;
    const GREEDY_HANAKO_TYPE_RESERVATIONS = 20;
    const STEADY_TAKAKO_TYPE_RESERVATIONS = 21;
    const CLASSIC_KAZUKO_TYPE_RESERVATIONS = 22;
    const NORMAL_TSUNEKO_TYPE_RESERVATIONS = 23;
    const FRIENDLY_EMIKO_TYPE_RESERVATIONS = 24;
    const FAMILY_FUMIKO_TYPE_RESERVATIONS = 25;
    const LAID_BACK_MICHIKO_TYPE_RESERVATIONS = 26;
    const TOP_PAGE_PV = 27;
    const AREA_KEY = 28;
    const TOP_PAGE_PV_AREA_AVG = 29;
    const FAIRS_PAGE_PV_AREA_AVG = 30;
    const FAIR_DETAILS_PAGE_PV_AREA_AVG = 31;
    const VISIT_RESERVATIONS_CV_AREA_AVG = 32;
    const PHOTO_GALLERY_PAGE_PV_AREA_AVG = 33;
    const PLAN_PAGE_PV = 34;
    const AREA_AVG_PLAN_PAGE_PV = 35;

    public static function readCSVClrepo()
    {
        $pathCSVLocal = File::downloadCSV(Constant::getURLCSVClrepo());
        if (!$pathCSVLocal) {
            return false;
        }
        if (($handle = fopen($pathCSVLocal, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                if (!empty($dataCSVClrepo[$data[CSV::S_CODE]])) {
                    $key = count($dataCSVClrepo[$data[CSV::S_CODE]]);

                    $dataCSVClrepo[$data[2]][$key] = [
                        '0' => $data[CSV::ISSUE_MONTH],
                        '1' => $data[CSV::WEEK_NUMBER],
                        '7' => $data[CSV::BRIDAL_FAIRS_PAGE_PV],
                        '8' => $data[CSV::BRIDAL_FAIR_DETAILS_PAGE_PV],
                        '9' => $data[CSV::PHOTO_GALLERY_PAGE_PV],
                        '10' => $data[CSV::TOTAL_RESERVATIONS],
                        '27' => $data[CSV::TOP_PAGE_PV],
                        '29' => $data[CSV::TOP_PAGE_PV_AREA_AVG],
                        '30' => $data[CSV::FAIRS_PAGE_PV_AREA_AVG],
                        '31' => $data[CSV::FAIR_DETAILS_PAGE_PV_AREA_AVG],
                        '32' => $data[CSV::VISIT_RESERVATIONS_CV_AREA_AVG],
                        '33' => $data[CSV::PHOTO_GALLERY_PAGE_PV_AREA_AVG],
                        '34' => $data[CSV::PLAN_PAGE_PV],
                        '35' => $data[CSV::AREA_AVG_PLAN_PAGE_PV],
                    ];
                    continue;
                }
                $dataCSVClrepo[$data[2]] = [
                    0 => [
                        '0' => $data[CSV::ISSUE_MONTH],
                        '1' => $data[CSV::WEEK_NUMBER],
                        '7' => $data[CSV::BRIDAL_FAIRS_PAGE_PV],
                        '8' => $data[CSV::BRIDAL_FAIR_DETAILS_PAGE_PV],
                        '9' => $data[CSV::PHOTO_GALLERY_PAGE_PV],
                        '10' => $data[CSV::TOTAL_RESERVATIONS],
                        '27' => $data[CSV::TOP_PAGE_PV],
                        '29' => $data[CSV::TOP_PAGE_PV_AREA_AVG],
                        '30' => $data[CSV::FAIRS_PAGE_PV_AREA_AVG],
                        '31' => $data[CSV::FAIR_DETAILS_PAGE_PV_AREA_AVG],
                        '32' => $data[CSV::VISIT_RESERVATIONS_CV_AREA_AVG],
                        '33' => $data[CSV::PHOTO_GALLERY_PAGE_PV_AREA_AVG],
                        '34' => $data[CSV::PLAN_PAGE_PV],
                        '35' => $data[CSV::AREA_AVG_PLAN_PAGE_PV],
                    ]
                ];
            }
            fclose($handle);
        }
        unlink($pathCSVLocal);
        return $dataCSVClrepo;
    }

    public static function readCSVIchioshiIcon()
    {
        $pathCSVLocal = File::downloadCSV(Constant::getURLCSVIchioshiIcon());
        if (!$pathCSVLocal) {
            return false;
        }
        $pathCSVIchioshiIcon = [];
        $index = 0;
        if (($handle = fopen($pathCSVLocal, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $index++;
                if ($index == 1) {
                    continue;
                }
                list($s_code, $ichioshiIcon) = explode(' ', $data[0]);
                list($usedNumber, $availableNumber) = explode('/', $ichioshiIcon);
                $pathCSVIchioshiIcon[$s_code] = [
                    'used_number' => $usedNumber,
                    'available_number' => $availableNumber
                ];
            }
            fclose($handle);
        }
        unlink($pathCSVLocal);
        return $pathCSVIchioshiIcon;
    }
}
