<?php

namespace App\Services;

use App\Console\Commands\BuildEML;
use App\Exceptions\CustomException;
use App\Services\Service;
use App\Utils\Constant;
use DateTime;
use GuzzleHttp\Client;

class Zexy extends Service
{
    const KEY_CHECK_PLAN_SHUBETSU_KBN = '04';

    protected $defaultQuery = [];
    protected $plans;
    protected $photos;
    protected $arrival;
    protected $clientFair;
    protected $searchClientFairSomeday;
    protected $searchClientFairSomedayWithSunSatHoliday;
    protected $searchClientFairSomedayWithSunSatHolidayCompetitorCCode;
    protected $clientFairWithProductCd;

    public function __construct($clientCd)
    {
        parent::__construct();
        $this->defaultQuery = [
            "key" => config('app.keyZexyAPI'),
            "gyoshuCd" => config('app.gyoshuCdZexyAPI'),
            "clientCd" => $clientCd
        ];
    }

    public function uri()
    {
        return env('ZEXY_API');
    }

    public function getAll()
    {
        $response = $this->client->get($this->uri());

        return $response->getHeaders();
    }

    private function newArrival()
    {
        try {
            if (!$this->arrival) {
                $response = $this->client->get($this->uri() . 'client/newArrival/v1/', [
                    'query' => $this->defaultQuery
                ]);

                return $this->arrival = $response->xml();
            }
            return $this->arrival;
        } catch (\Exception $exception) {
            CustomException::dumpAndLogWarnning($exception->getMessage());
            return (object)[];
        }
    }

    private function photos()
    {
        try {
            if (!$this->photos) {
                $response = $this->client->get($this->uri() . 'client/photo/v1/', [
                    'query' => $this->defaultQuery
                ]);

                return $this->photos = $response->xml();
            }
            return $this->photos;
        } catch (\Exception $exception) {
            CustomException::dumpAndLogWarnning($exception->getMessage());
            return (object)[];
        }
    }

    private function plans()
    {
        try {
            if (!$this->plans) {
                $response = $this->client->get($this->uri() . 'client/plan/v1/', [
                    'query' => $this->defaultQuery
                ]);

                return $this->plans = $response->xml();
            }
            return $this->plans;
        } catch (\Exception $exception) {
            CustomException::dumpAndLogWarnning($exception->getMessage());
            return (object)[];
        }
    }

    private function clientFair($query)
    {
        try {
            $response = $this->client->get($this->uri() . 'client/fair/v1/'.$query);
            if (empty($response->xml()) || empty($response->xml()->fair_list) || empty($response->xml()->fair_list->fair)) {
                return false;
            }
            return $this->clientFair = $response->xml()->fair_list->fair;
        } catch (\Exception $exception) {
            CustomException::dumpAndLogWarnning($exception->getMessage());
            return (object)[];
        }
    }

    private function searchClientFair($query)
    {
        try {
            $response = $this->client->get($this->uri() . 'search/clientFair/v1/'.$query);
            if (empty($response->xml()) || empty($response->xml()->client_fair_list) || empty($response->xml()->client_fair_list->client_fair)) {
                return false;
            }
            return $response->xml()->client_fair_list->client_fair;
        } catch (\Exception $exception) {
            CustomException::dumpAndLogWarnning($exception->getMessage());
            return (object)[];
        }
    }

    public function getClientFairWithProductCd($productCd)
    {
        $query = '?'.$this->parseArrayToString($this->defaultQuery).'&productCd='.$productCd;
        $clientFairWithProductCd = $this->clientFair($query);
        return $clientFairWithProductCd;
    }

    public function searchClientFairWithDaysAfterSomeday()
    {
        if ($this->searchClientFairSomeday) {
            return $this->searchClientFairSomeday;
        }
        $query = '?'.$this->parseArrayToString($this->defaultQuery).$this->getDaysAfterSomeDay();
        return $this->searchClientFairSomeday = $this->searchClientFair($query);
    }

    public function searchClientFairWithDaysAfterSomeDayWithOnlyStaturdaySunDayHoliday()
    {
        if ($this->searchClientFairSomedayWithSunSatHoliday) {
            return $this->searchClientFairSomedayWithSunSatHoliday;
        }
        $query = '?'.$this->parseArrayToString($this->defaultQuery).$this->getDaysAfterSomeDayWithOnlyStaturdaySunDayHoliday();
        return $this->searchClientFairSomedayWithSunSatHoliday = $this->searchClientFair($query);
    }

    private function getQueryWithCompetitorCCode($competitor_c_code)
    {
        return 'key='.config('app.keyZexyAPI').'&gyoshuCd='.config('app.gyoshuCdZexyAPI').'&clientCd='.$competitor_c_code;
    }

    public function searchClientFairWithDaysAfterSomeDayWithOnlyStaturdaySunDayHolidayCompetitorCCode($competitor_c_code)
    {
        $query = '?'.$this->getQueryWithCompetitorCCode($competitor_c_code).'&'.$this->getDaysAfterSomeDayWithOnlyStaturdaySunDayHoliday();
        return $this->searchClientFairSomedayWithSunSatHolidayCompetitorCCode = $this->searchClientFair($query);
    }

    public function getClientFairWithProductCdCompetitorCCode($productCd, $competitor_c_code)
    {
        $query = '?'.$this->getQueryWithCompetitorCCode($competitor_c_code).'&productCd='.$productCd;
        $clientFairWithProductCd = $this->clientFair($query);
        return $this->clientFairWithProductCd = $clientFairWithProductCd;
    }

    private function getDayAfterSomeDay($numberOfDay): string
    {
        return $this->modifyDateFromNow("+$numberOfDay");
    }

    public function getDaysAfterSomeDay($isGetArray = null)
    {
        $dateAfter = strtotime($this->getDayAfterSomeDay(13));
        $time = strtotime(date('Y-m-d'));
        $days = [];
        for ($i=$time; $i<=$dateAfter; $i += Constant::SECOND_IN_ONE_DAY) {
            array_push($days, date("Ymd", $i));
        }
        return $isGetArray ? $days : $this->parseDaysToString($days);
    }

    public function getDaysAfterSomeDayWithOnlyStaturdaySunDayHoliday($isGetArray = null)
    {
        $dateAfter = $this->getDayAfterSomeDay(13);
        $timeNow = date('Y-m-d');
        $days = [];
        for ($i = strtotime($timeNow); $i <= strtotime($dateAfter); $i += Constant::SECOND_IN_ONE_DAY) {
            $date = date('Y M D d', $i);
            if (strpos($date, 'Sat') || strpos($date, 'Sun')) {
                array_push($days, date("Ymd", $i));
            }
        }
        $holidays = BuildEML::postgreSql()->getHolidays($timeNow, $dateAfter);
        if (count($holidays)) {
            foreach ($holidays as $holiday) {
                array_push($days, date('Ymd', strtotime($holiday->holiday)));
            }
        }
        return $isGetArray ? $days : $this->parseDaysToString($days);
    }

    public function parseDaysToString($days) : string
    {
        $stringDays = '';
        for ($i = 0; $i < count($days); $i++) {
            (count($days)-1 == $i) ? $stringDays .= 'date='.$days[$i] : $stringDays .= 'date='.$days[$i].'&';
        }
        return $stringDays;
    }

    public function parseArrayToString($array) : string
    {
        $stringQuery = '';
        foreach ($array as $key => $value) {
            $stringQuery .= $key.'='.$value.'&';
        }
        return $stringQuery;
    }

    public function getKeisaiStartDateFromArrival(): string
    {
        return !empty($this->newArrival()->new_arrival->keisai_start_date) ? $this->newArrival()->new_arrival->keisai_start_date : '';
    }

    public function getAutoLinkUrlFromArrival(): string
    {
        return !empty($this->newArrival()->new_arrival->auto_link_url) ? $this->newArrival()->new_arrival->auto_link_url : '';
    }

    public function checkStatusCodeAutoLink()
    {
        try {
            $autoLink = $this->getAutoLinkUrlFromArrival();
            if (!$autoLink) {
                return false;
            }
            $client = new Client();
            $response = $client->get(
                $autoLink,
                [
                    'headers' => [
                        'Content-Type'  => 'application/x-www-form-urlencoded'
                    ],
                    'allow_redirects' => false
                ]
            );
            return ($response->getStatusCode() == 301) || ($response->getStatusCode() == 302);
        } catch (\Exception $exception) {
            CustomException::dumpAndLogWarnning($exception->getMessage());
            return false;
        }
    }

    public function getQuantityTotalPhotoGallery(): int
    {
        return (!empty($this->photos()->photo_gallery_list) || !empty($this->photos()->photo_gallery_list->photo_gallery)) ? count($this->photos()->photo_gallery_list->photo_gallery) : 0;
    }

    private function getPlanLists($topNumber = null)
    {
        if (!$this->plans) {
            $this->plans = $this->plans()->plan_list->plan;
        }
        if (empty($this->plans) || !count((array)$this->plans)) {
            return (object)[];
        }

        if (is_null($topNumber)) {
            return $this->plans;
        }

        $newPlanListsA = new \ArrayObject();
        $newPlanListsB = new \ArrayObject();

        foreach ($this->plans as $plan) {
            if ($plan->plan_shubetsu_kbn == self::KEY_CHECK_PLAN_SHUBETSU_KBN) {
                $newPlanListsA->append($plan);
            }

            if ($plan->plan_shubetsu_kbn != self::KEY_CHECK_PLAN_SHUBETSU_KBN) {
                $newPlanListsB->append($plan);
            }
        }
        $newPlanLists = array_merge((array)$newPlanListsA, (array)$newPlanListsB);

        $topPlanList = array_slice($newPlanLists, 0, $topNumber ? $topNumber : count((array)$newPlanLists));

        if (!count((array)$topPlanList)) {
            return (object)[];
        }

        return (object)$topPlanList;
    }

    public function checkDateA5ThresHold($a5Threshold): bool
    {
        try {
            $planLists = self::getPlanLists(Constant::GET_TOP_3_OF_PLAN);
            if (!count((array)$planLists)) {
                return false;
            }
            foreach ($planLists as $planList) {
                $applyPeriod = $planList->apply_period;
                $e = mb_convert_kana($applyPeriod, "a");
                if (preg_match('/(\d{2,4}){0,1}年{0,1}(\d{1,2})月(\d{1,2}){0,1}(日){0,1}(まで|迄)/u', $e, $a)) {
                    list(, $y, $m) = $a;
                }
                if (preg_match('/(\d{2,4}){0,1}\/(\d{1,2})\/{0,1}(\d{1,2}){0,1}(まで|迄)/u', $e, $a)) {
                    list(, $y, $m) = $a;
                }
                if (preg_match('/(から|～)(\d{2,4}){0,1}年{0,1}(\d{1,2})月(\d{1,2}){0,1}(日){0,1}/u', $e, $a)) {
                    list(, , $y, $m) = $a;
                }
                if (preg_match('/(から|～)(\d{2,4}){0,1}\/(\d{1,2})\/{0,1}(\d{1,2}){0,1}/u', $e, $a)) {
                    list(, , $y, $m) = $a;
                }
                $y = empty($y) ? self::getYValueByM($m) : $y;
                $checkDate = checkdate($m, 1, $y) ? date_format(date_modify(new DateTime(date('Y-m-d')), "{$y}-{$m}-1"), 'Y-m-d') : null;

                if (!$checkDate) {
                    continue;
                }
                $checkDate = $this->formatDayForObject($checkDate);

                $thresHold = $this->modifyMonthFromNow("$a5Threshold");
                if ($checkDate > $thresHold) {
                    continue;
                }

                return true;
            }
            return false;
        } catch (\Exception $exception) {
            CustomException::dumpAndLogWarnning($exception->getMessage());
            return false;
        }
    }

    public function checkDateA6ThresHold(): bool
    {
        try {
            $planLists = self::getPlanLists();
            if (!count((array)$planLists)) {
                return false;
            }
            foreach ($planLists as $planList) {
                $applyPeriod = $planList->apply_period;
                $e = mb_convert_kana($applyPeriod, "a");
                if (preg_match('/(\d{2,4}){0,1}年{0,1}(\d{1,2})月(\d{1,2}){0,1}(日){0,1}(まで|迄)/u', $e, $a)) {
                    list(, $y, $m) = $a;
                    $d = isset($a[3]) ? $a[3] : null;
                } elseif (preg_match('/(\d{2,4}){0,1}\/(\d{1,2})\/{0,1}(\d{1,2}){0,1}(まで|迄)/u', $e, $a)) {
                    list(, $y, $m) = $a;
                    $d = isset($a[3]) ? $a[3] : null;
                } elseif (preg_match('/(から|～)(\d{2,4}){0,1}年{0,1}(\d{1,2})月(\d{1,2}){0,1}(日){0,1}/u', $e, $a)) {
                    list(, , $y, $m) = $a;
                    $d = isset($a[4]) ? $a[4] : null;
                } elseif (preg_match('/(から|～)(\d{2,4}){0,1}\/(\d{1,2})\/{0,1}(\d{1,2}){0,1}/u', $e, $a)) {
                    list(, , $y, $m) = $a;
                    $d = isset($a[4]) ? $a[4] : null;
                }
                $y = empty($y) ? self::getYValueByM($m) : $y;
                $d = empty($d) ? 1 : $d;
                $checkDate = checkdate($m, $d, $y) ? new DateTime("{$y}-{$m}-{$d}") : null;

                if (!$checkDate) {
                    continue;
                }

                $thresHold = new DateTime(date('Y-m-d'));
                if ($checkDate > $thresHold) {
                    continue;
                }

                return true;
            }
            return false;
        } catch (\Exception $exception) {
            CustomException::dumpAndLogWarnning($exception->getMessage());
            return false;
        }
    }

    private static function getYValueByM($m)
    {
        switch (true) {
            case date('m') >= 10 && $m <= 3:
                $y = date('Y') + 1;
                break;
            case date('m') <= 3 && $m >= 10:
                $y = date('Y') - 1;
                break;
            default:
                $y = date('Y');
                break;
        }
        return $y;
    }

    public function checkDateA7ThresHold($a7Threshold): bool
    {
        try {
            $planLists = self::getPlanLists(Constant::GET_TOP_3_OF_PLAN);
            if (!count((array)$planLists)) {
                return false;
            }
            $keyCheck = 0;
            foreach ($planLists as $planList) {
                $tekiyoNinzuTo = $planList->tekiyo_ninzu_to;
                if ($tekiyoNinzuTo <= $a7Threshold) {
                    $keyCheck++;
                }
            }
            if ($keyCheck == Constant::GET_TOP_3_OF_PLAN) {
                return true;
            }
            return false;
        } catch (\Exception $exception) {
            CustomException::dumpAndLogWarnning($exception->getMessage());
            return false;
        }
    }

    public function checkDateA8ThresHold(): bool
    {
        try {
            $planLists = self::getPlanLists();
            if (!count((array)$planLists)) {
                return false;
            }
            foreach ($planLists as $planList) {
                $perk = $planList->perk;
                if (!isset($perk) || !$perk) {
                    return true;
                }
            }
            return false;
        } catch (\Exception $exception) {
            CustomException::dumpAndLogWarnning($exception->getMessage());
            return false;
        }
    }

    public function modifyMonthFromNow($numberOfMonthModify)
    {
        return date_format(date_modify(new DateTime(date('Y-m-d')), "$numberOfMonthModify months"), 'Y-m-d');
    }

    public function modifyDateFromNow($numberOfDayModify)
    {
        return date_format(date_modify(new DateTime(date('Y-m-d')), "$numberOfDayModify days"), 'Y-m-d');
    }

    public function formatDayForObject($day)
    {
        return date_format(date_modify(new DateTime(date('Y-m-d')), $day), 'Y-m-d');
    }
}
