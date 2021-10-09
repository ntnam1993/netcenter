<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Utils\Constant;
use App\Utils\Helper;
use Illuminate\Support\Facades\DB;

class MySql
{
    protected $connect;

    public function __construct()
    {
        $this->connect = DB::connection('nabiMySql');
    }

    public function getQuantityOrderEachDay($log)
    {
        $log->logInfo('Get Quantity Order Of Each Day');
        $firstDayOfCurrent = self::getFisrtDayOfCurrent();
        $lastDayOfCurrent = self::getLastDayOfCurrent();
        try {
            $quantity = $this->connect->select("select
                b.client_cd,
                area_cd,
                date(b.hold_date) as hold_date,
                sum(case when a.act_id is not null then 1 else 0 end) as count
            from
                dwh_zxy_admxzbr__hall_fair_toc b
                join
                    m_client_area_todofuken_han_cd c
                on  b.client_cd = c.client_cd
                left join
                    keiro_preprocessed_fair_yoyaku a
                on  a.fair_id = b.product_cd
            where
                taisyo_ymd = (
                    select
                        max(taisyo_ymd)
                    from
                        m_client_area_todofuken_han_cd
                )
            and b.hold_date between '$firstDayOfCurrent' and '$lastDayOfCurrent'
            group by 1,2,3
            order by 1,2,3");

            if (!count((array)$quantity)) {
                throw new CustomException("Don't get any quantity order for each day !");
            }

            return self::formatQuantity($quantity, $log);
        } catch (CustomException $exception) {
            $exception::dumpAndLog($exception->getMessage());
            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function getFairAndContentTypeEachDay($log)
    {
        $log->logInfo('Get Fair And Content Type Of Each Day');
        if (env('APP_ENV') == Constant::LOCAL_ENV) {
            return Constant::LOCAL_ENV;
        }
        $yesterdayOneYearAgo = self::getYesterdayOneYearAgo();
        $lastOfNextMonth = self::getLastDayOfNextMonth();
        try {
            $fairAndContent = $this->connect->select("select
                a.client_cd,
                a.product_cd,
                a.hold_date,
                a.fair_nm,
                b.tkch_004,
                b.tkch_005,
                b.tkch_006,
                b.tkch_007,
                b.tkch_008,
                b.tkch_009,
                b.tkch_010,
                b.tkch_011,
                b.tkch_012,
                b.tkch_013,
                c.page_view,
                c.cv
            from
                dwh_zxy_admxzbr__hall_fair_toc a
                join
                    dwh_zxy_admxzxy_custom__parts b
                on  a.product_cd = b.product_cd
                join
                    bfm_pv_uu_click c
                on  a.product_cd = c.product_cd
            where
                hold_date between '$yesterdayOneYearAgo' and '$lastOfNextMonth'
                and a.actual_head_fair_flg <> 0
            order by
                client_cd,product_cd");

            if (!count((array)$fairAndContent)) {
                throw new CustomException("Don't get any quantity order for each day !");
            }

            return $fairAndContent;
        } catch (CustomException $exception) {
            $exception::dumpAndLog($exception->getMessage());
            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function getFisrtDayOfCurrent()
    {
        return date("Y-m-01");
    }

    private function getLastDayOfCurrent()
    {
        return date("Y-m-t");
    }

    private function getYesterdayOneYearAgo()
    {
        $date = strtotime(date('Y-m-d'));
        $yeaterdayOneYearAgo = strtotime('-1 day -1 year', $date);
        return date('Y-m-d', $yeaterdayOneYearAgo);
    }

    private function getLastDayOfNextMonth()
    {
        return date('Y-m-d', strtotime('last day of next month'));
    }

    /**
     * @SuppressWarnings("unused")
     */
    private function formatQuantity($quantitys, $log)
    {
        $log->logInfo('Start format Quantity, count = '.count((array)$quantitys));
        $totalCountAs = [];
        $totalCountBInDates = [];
        $totalCountBInMonths = [];
        $newQuantity = [];
        foreach ($quantitys as $quantityOut) {
            $clientCd = $quantityOut->client_cd;
            $areaCd = $quantityOut->area_cd;
            $holdDate = $quantityOut->hold_date;
            $count = $quantityOut->count;

            $keyTotalCountA = $clientCd.$this->dateFomat($holdDate, 'Ym');
            $totalCountAs[$keyTotalCountA] = isset($totalCountAs[$keyTotalCountA]) ? $totalCountAs[$keyTotalCountA] + $count : $count;

            $keyTotalCountBInDate = $areaCd.$this->dateFomat($holdDate, 'Ymd');
            $totalCountBInDates[$keyTotalCountBInDate] = isset($totalCountBInDates[$keyTotalCountBInDate]) ? $totalCountBInDates[$keyTotalCountBInDate] + $count : $count;

            $keyTotalCountBInMonth = $areaCd.$this->dateFomat($holdDate, 'Ym');
            $totalCountBInMonths[$keyTotalCountBInMonth] = isset($totalCountBInMonths[$keyTotalCountBInMonth]) ? $totalCountBInMonths[$keyTotalCountBInMonth] + $count : $count;
        }

        foreach ($quantitys as $key => $quantity) {
            $count = $quantity->count;
            $clientCd = $quantity->client_cd;
            $holdDate = $quantity->hold_date;
            $areaCd = $quantity->area_cd;
            $keyTotalCountA = $clientCd.$this->dateFomat($holdDate, 'Ym');
            $keyTotalCountBInDate = $areaCd.$this->dateFomat($holdDate, 'Ymd');
            $keyTotalCountBInMonth = $areaCd.$this->dateFomat($holdDate, 'Ym');
            $a = Helper::devision($count, $totalCountAs[$keyTotalCountA]);
            $bDate = $totalCountBInDates[$keyTotalCountBInDate];
            $bMonth = $totalCountBInMonths[$keyTotalCountBInMonth];
            $b = Helper::devision($bDate, $bMonth);
            $c = $b - $a;
            $newQuantity[$key] = [
                'client_cd' => $clientCd,
                'hold_date' => $holdDate,
                'c' => $c
            ];
        }

        $log->logInfo('End Format Quantity!');
        return $newQuantity;
    }

    private function dateFomat($date, $format = "Y-m")
    {
        return date($format, strtotime($date));
    }

    public function getFairAndContentTypeEachDayWithCCode($cCode)
    {
        $yesterdayOneYearAgo = self::getYesterdayOneYearAgo();
        $lastOfNextMonth = self::getLastDayOfNextMonth();
        try {
            $fairAndContent = $this->connect->select("select
                a.client_cd,
                a.product_cd,
                a.hold_date,
                a.fair_nm,
                b.tkch_004,
                b.tkch_005,
                b.tkch_006,
                b.tkch_007,
                b.tkch_008,
                b.tkch_009,
                b.tkch_010,
                b.tkch_011,
                b.tkch_012,
                b.tkch_013,
                c.page_view,
                c.cv
            from
                dwh_zxy_admxzbr__hall_fair_toc a
                join
                    dwh_zxy_admxzxy_custom__parts b
                on  a.product_cd = b.product_cd
                join
                    bfm_pv_uu_click c
                on  a.product_cd = c.product_cd
            where
                hold_date between '$yesterdayOneYearAgo' and '$lastOfNextMonth'
                and a.actual_head_fair_flg <> 0
                and a.client_cd = '$cCode'
            order by
                client_cd,product_cd");

            if (!count((array)$fairAndContent)) {
                throw new CustomException("Don't get any quantity order for each day !");
            }

            return $fairAndContent;
        } catch (CustomException $exception) {
            $exception::dumpAndLog($exception->getMessage());
            return false;
        }
    }
}
