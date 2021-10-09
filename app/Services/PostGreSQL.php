<?php


namespace App\Services;

use App\Exceptions\CustomException;
use App\Utils\Log;
use Illuminate\Support\Facades\DB;

class PostGreSQL
{
    protected $log;

    public function __construct()
    {
        $this->log = new Log();
    }

    public function checkDateRunBatch()
    {
        try {
            $now = date('Y-m-d');
            $working_date = DB::table('schedules')->where('working_date', $now)->get();
            if (!count($working_date)) {
                throw new CustomException('Today is not day to run batch, bye !');
            }
            return json_decode($working_date, true);
        } catch (CustomException $exception) {
            $exception::dumpAndLog($exception->getMessage());
            return false;
        } catch (\Exception $exception) {
            $this->log->logError('Cannot connect to master database .');
            return false;
        }
    }

    public function getListShop()
    {
        try {
            $groups = DB::table('shops')->select(['email1', 'email2', 'email3', 'grouping_key'])->groupBy(['email1', 'email2', 'email3', 'grouping_key'])->get()->toArray();
            if (!count($groups)) {
                throw new CustomException('No Any Group In Database !');
            }
            $shops = DB::table('shops')->get()->toArray();
            if (!count($groups)) {
                throw new CustomException('No Any Shop In Database !');
            }

            $shopGroup = $this->createShopGroup($groups, $shops);

            if (!count($shopGroup)) {
                throw new CustomException('No Shop Found In Database !');
            }
            return $shopGroup;
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return false;
        } catch (\Exception $exception) {
            $this->log->logError('Cannot connect to master database .');
            return false;
        }
    }

    public function createShopGroup($groups, $shops)
    {
        $shopGroup = [];

        foreach ($groups as $keyGroups => $group) {
            foreach ($shops as $shop) {
                if (($shop->email1 === $group->email1)
                    && ($shop->email2 === $group->email2)
                    && ($shop->email3 === $group->email3)
                    && ($shop->grouping_key === $group->grouping_key)) {
                    if (!isset($shopGroup[$keyGroups]) || !is_array($shopGroup[$keyGroups])) {
                        $shopGroup[$keyGroups] = [];
                    }
                    array_push($shopGroup[$keyGroups], (array)$shop);
                }
            }
            if (!isset($shopGroup[$keyGroups]['grouping_key'])) {
                $shopGroup[$keyGroups]['grouping_key'] = $group->grouping_key;
            }
            if (!isset($shopGroup[$keyGroups]['staff_names'])) {
                $shopGroup[$keyGroups]['staff_names'] = $this->getStaffName([
                    $group->email1,
                    $group->email2,
                    $group->email3
                ]);
            }
            if (!isset($shopGroup[$keyGroups]['shop_emails'])) {
                $shopGroup[$keyGroups]['shop_emails'] = [];
                if ($group->email1) {
                    array_push($shopGroup[$keyGroups]['shop_emails'], $group->email1);
                }
                if ($group->email2) {
                    array_push($shopGroup[$keyGroups]['shop_emails'], $group->email2);
                }
                if ($group->email3) {
                    array_push($shopGroup[$keyGroups]['shop_emails'], $group->email3);
                }
            }
        }

        return $shopGroup;
    }

    public function getStaffName($emails)
    {
        try {
            $staffs = DB::table('staffs')->select('name')->whereIn('email', $emails)->get()->pluck('name')->toArray();
            if (empty($staffs)) {
                throw new CustomException('Not found any staff match with email');
            }
            return $staffs;
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return [];
        } catch (\Exception $exception) {
            $this->log->logError('Cannot connect to master database .');
            return false;
        }
    }

    public function getDateReportRunBatch()
    {
        try {
            $before7DateFromNow = date('Y-m-d', strtotime('-7 days'));
            $dateReportRunBatch = DB::table('months')->whereDate('start_date', '<=', $before7DateFromNow)->whereDate('end_date', '>=', $before7DateFromNow)->first();
            if (empty($dateReportRunBatch)) {
                throw new CustomException('Not found any date match.');
            }
            return $dateReportRunBatch;
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return false;
        } catch (\Exception $exception) {
            $this->log->logError('Cannot connect to master database .');
            return false;
        }
    }

    public function getIssueMonth()
    {
        $date = date('Y-m-d');
        $issueMonthMatchDate = DB::table('months')->whereDate('start_date', '<=', $date)->whereDate('end_date', '>=', $date)->first();
        try {
            if (empty($issueMonthMatchDate)) {
                throw new CustomException('Not found any months match.');
            }
            return $issueMonthMatchDate->issue_month;
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return false;
        } catch (\Exception $exception) {
            $this->log->logError('Cannot connect to master database .');
            return false;
        }
    }

    public function updateFairListFlowLastCommentDate($c_code, $issue_month)
    {
        try {
            DB::table('shops')->where('c_code', $c_code)->update(['fair_list_flow_last_commented_date' => $issue_month]);
        } catch (\Exception $exception) {
            $this->log->logError('Cannot connect to master database .');
            return false;
        }
    }

    public function updateFairDetailFlowLastCommentDate($c_code, $issue_month)
    {
        try {
            DB::table('shops')->where('c_code', $c_code)->update(['fair_detail_flow_last_commented_date' => $issue_month]);
        } catch (\Exception $exception) {
            $this->log->logError('Cannot connect to master database .');
            return false;
        }
    }

    public function updateReservationFlowFlowLastCommentDate($c_code, $issue_month)
    {
        try {
            DB::table('shops')->where('c_code', $c_code)->update(['reservation_flow_last_commented_date' => $issue_month]);
        } catch (\Exception $exception) {
            $this->log->logError('Cannot connect to master database .');
            return false;
        }
    }

    public function getHolidays($timeStart, $timeEnd)
    {
        try {
            $holidays = DB::table('holidays')->where('holiday', '<', $timeEnd)->where('holiday', '>', $timeStart)->get()->toArray();
            if (!count($holidays)) {
                throw new CustomException("Don't have holiday matching with condition !");
            }
            return $holidays;
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return [];
        } catch (\Exception $exception) {
            $this->log->logError('Cannot connect to master database .');
            return false;
        }
    }

    public function searchDayIsHoliday($day)
    {
        try {
            $holidays = DB::table('holidays')->where('holiday', $day)->first();
            if (empty($holidays)) {
                throw new CustomException("Don't have holiday matching with condition !");
            }
            return (array)$holidays;
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return [];
        } catch (\Exception $exception) {
            $this->log->logError('Cannot connect to master database .');
            return false;
        }
    }

    public function getCompetitorCCodes($c_code)
    {
        try {
            $competitorCCodes = array();
            $cCode = DB::table('shops')->where('c_code', $c_code)->first();
            if ($cCode->competitor_c_code1) {
                array_push($competitorCCodes, $cCode->competitor_c_code1);
            }
            if ($cCode->competitor_c_code2) {
                array_push($competitorCCodes, $cCode->competitor_c_code2);
            }
            if ($cCode->competitor_c_code3) {
                array_push($competitorCCodes, $cCode->competitor_c_code3);
            }
            if ($cCode->competitor_c_code4) {
                array_push($competitorCCodes, $cCode->competitor_c_code4);
            }
            if ($cCode->competitor_c_code5) {
                array_push($competitorCCodes, $cCode->competitor_c_code5);
            }
            if ($cCode->competitor_c_code6) {
                array_push($competitorCCodes, $cCode->competitor_c_code6);
            }
            if ($cCode->competitor_c_code7) {
                array_push($competitorCCodes, $cCode->competitor_c_code7);
            }
            if ($cCode->competitor_c_code8) {
                array_push($competitorCCodes, $cCode->competitor_c_code8);
            }
            if ($cCode->competitor_c_code9) {
                array_push($competitorCCodes, $cCode->competitor_c_code9);
            }
            if ($cCode->competitor_c_code10) {
                array_push($competitorCCodes, $cCode->competitor_c_code10);
            }
            return $competitorCCodes;
        } catch (\Exception $exception) {
            $this->log->logError('Cannot connect to master database .');
            return false;
        }
    }
}
