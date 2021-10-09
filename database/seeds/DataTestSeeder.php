<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class DataTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createDataHolidayTable();

        $this->createDataMonthTable();

        $this->createDataScheduleTable();

        $this->createDataShopTable();
        $this->createDataStaffTable();
    }

    public function createDataHolidayTable()
    {
        DB::table('holidays')->truncate();
        DB::table('holidays')->insert([
            [
                'holiday' => '2020-07-23'
            ],
            [
                'holiday' => '2020-07-24'
            ],
            [
                'holiday' => '2020-08-10'
            ]
        ]);
    }

    public function createDataMonthTable()
    {
        DB::table('months')->truncate();
        DB::table('months')->insert([
            [
                'issue_month' => '202005',
                'week_number' => '1',
                'start_date' => '2020-04-27',
                'end_date' => '2020-05-06',
            ],
            [
                'issue_month' => '202005',
                'week_number' => '2',
                'start_date' => '2020-05-07',
                'end_date' => '2020-05-10',
            ],
            [
                'issue_month' => '202005',
                'week_number' => '3',
                'start_date' => '2020-05-11',
                'end_date' => '2020-05-17',
            ],
            [
                'issue_month' => '202005',
                'week_number' => '4',
                'start_date' => '2020-05-18',
                'end_date' => '2020-05-24',
            ],
            [
                'issue_month' => '202006',
                'week_number' => '1',
                'start_date' => '2020-05-25',
                'end_date' => '2020-05-31',
            ],
            [
                'issue_month' => '202006',
                'week_number' => '2',
                'start_date' => '2020-06-01',
                'end_date' => '2020-06-07',
            ],
            [
                'issue_month' => '202006',
                'week_number' => '3',
                'start_date' => '2020-06-08',
                'end_date' => '2020-06-14',
            ],
            [
                'issue_month' => '202006',
                'week_number' => '4',
                'start_date' => '2020-06-15',
                'end_date' => '2020-06-21',
            ],
            [
                'issue_month' => '202006',
                'week_number' => '5',
                'start_date' => '2020-06-22',
                'end_date' => '2020-06-28',
            ],
            [
                'issue_month' => '202007',
                'week_number' => '1',
                'start_date' => '2020-06-29',
                'end_date' => '2020-07-05',
            ],
            [
                'issue_month' => '202007',
                'week_number' => '2',
                'start_date' => '2020-07-06',
                'end_date' => '2020-07-12',
            ],
            [
                'issue_month' => '202007',
                'week_number' => '3',
                'start_date' => '2020-07-13',
                'end_date' => '2020-07-19',
            ],
            [
                'issue_month' => '202007',
                'week_number' => '4',
                'start_date' => '2020-07-20',
                'end_date' => '2020-07-26',
            ],
            [
                'issue_month' => '202008',
                'week_number' => '1',
                'start_date' => '2020-07-27',
                'end_date' => '2020-08-02',
            ],
            [
                'issue_month' => '202008',
                'week_number' => '2',
                'start_date' => '2020-08-03',
                'end_date' => '2020-08-10',
            ],
            [
                'issue_month' => '202008',
                'week_number' => '3',
                'start_date' => '2020-08-11',
                'end_date' => '2020-08-16',
            ],
            [
                'issue_month' => '202008',
                'week_number' => '4',
                'start_date' => '2020-08-17',
                'end_date' => '2020-08-23',
            ],
            [
                'issue_month' => '202009',
                'week_number' => '1',
                'start_date' => '2020-08-24',
                'end_date' => '2020-08-30',
            ],
        ]);
    }

    public function createDataScheduleTable()
    {
        DB::table('schedules')->truncate();
        $schedules = [];
        $year = 2020;
        $month = 5;
        $day = 19;
        for ($i = 1; $i <= 74; $i++) {
            $strDay = $day < 10 ? '0' . $day : $day;
            $strMonth = $month < 10 ? '0' . $month : $month;
            $schedules[] = ['working_date' => strval($year).'-'.strval($strMonth).'-'.strval($strDay)];
            $day += 1;
            if ($day == 32 && $month == 5) {
                $day = 1;
                $month = 6;
            }
            if ($day == 31 && $month == 6) {
                $day = 1;
                $month = 7;
            }
        }
        DB::table('schedules')->insert($schedules);
    }

    public function createDataShopTable()
    {
        DB::table('shops')->truncate();
        DB::table('shops')->insert([
            [
                'c_code' => '7770077880',
                'email1' => 'duong.ht+test1@workhouse.me',
                'email2' => 'nam.nt+test1@workhouse.me',
                'email3' => null,
                'goal_type' => '1',
                'summary_type' => '1',
                'grouping_key' => null,
                'is_client_report_needed' => 't',
                'is_competitor_sheet_needed' => 't',
                'competitor_c_code1' => null,
                'competitor_c_code2' => null,
                'competitor_c_code3' => null,
                'competitor_c_code4' => null,
                'competitor_c_code5' => null,
                'competitor_c_code6' => null,
                'competitor_c_code7' => null,
                'competitor_c_code8' => null,
                'competitor_c_code9' => null,
                'competitor_c_code10' => null
            ],
            [
                'c_code' => '7770078211',
                'email1' => 'duong.ht+test1@workhouse.me',
                'email2' => 'nam.nt+test1@workhouse.me',
                'email3' => null,
                'goal_type' => '2',
                'summary_type' => '2',
                'grouping_key' => null,
                'is_client_report_needed' => 't',
                'is_competitor_sheet_needed' => 'f',
                'competitor_c_code1' => null,
                'competitor_c_code2' => null,
                'competitor_c_code3' => null,
                'competitor_c_code4' => null,
                'competitor_c_code5' => null,
                'competitor_c_code6' => null,
                'competitor_c_code7' => null,
                'competitor_c_code8' => null,
                'competitor_c_code9' => null,
                'competitor_c_code10' => null
            ],
            [
                'c_code' => '7770038315',
                'email1' => 'duong.ht+test1@workhouse.me',
                'email2' => 'nam.nt+test1@workhouse.me',
                'email3' => 'tri.vv+test1@workhouse.me',
                'goal_type' => '3',
                'summary_type' => '3',
                'grouping_key' => null,
                'is_client_report_needed' => 'f',
                'is_competitor_sheet_needed' => 't',
                'competitor_c_code1' => null,
                'competitor_c_code2' => null,
                'competitor_c_code3' => null,
                'competitor_c_code4' => null,
                'competitor_c_code5' => null,
                'competitor_c_code6' => null,
                'competitor_c_code7' => null,
                'competitor_c_code8' => null,
                'competitor_c_code9' => null,
                'competitor_c_code10' => null
            ],
            [
                'c_code' => '7770013690',
                'email1' => 'duong.ht+test1@workhouse.me',
                'email2' => 'nam.nt+test1@workhouse.me',
                'email3' => null,
                'goal_type' => '2',
                'summary_type' => '2',
                'grouping_key' => '1',
                'is_client_report_needed' => 'f',
                'is_competitor_sheet_needed' => 'f',
                'competitor_c_code1' => null,
                'competitor_c_code2' => null,
                'competitor_c_code3' => null,
                'competitor_c_code4' => null,
                'competitor_c_code5' => null,
                'competitor_c_code6' => null,
                'competitor_c_code7' => null,
                'competitor_c_code8' => null,
                'competitor_c_code9' => null,
                'competitor_c_code10' => null
            ],
            [
                'c_code' => '7770044557',
                'email1' => 'duong.ht+test1@workhouse.me',
                'email2' => 'nam.nt+test1@workhouse.me',
                'email3' => null,
                'goal_type' => '3',
                'summary_type' => '3',
                'grouping_key' => '1',
                'is_client_report_needed' => 't',
                'is_competitor_sheet_needed' => 't',
                'competitor_c_code1' => null,
                'competitor_c_code2' => null,
                'competitor_c_code3' => null,
                'competitor_c_code4' => null,
                'competitor_c_code5' => null,
                'competitor_c_code6' => null,
                'competitor_c_code7' => null,
                'competitor_c_code8' => null,
                'competitor_c_code9' => null,
                'competitor_c_code10' => null
            ],
            [
                'c_code' => '7770078976',
                'email1' => 'duong.ht+test1@workhouse.me',
                'email2' => 'nam.nt+test1@workhouse.me',
                'email3' => null,
                'goal_type' => '1',
                'summary_type' => '1',
                'grouping_key' => '200',
                'is_client_report_needed' => 't',
                'is_competitor_sheet_needed' => 'f',
                'competitor_c_code1' => null,
                'competitor_c_code2' => null,
                'competitor_c_code3' => null,
                'competitor_c_code4' => null,
                'competitor_c_code5' => null,
                'competitor_c_code6' => null,
                'competitor_c_code7' => null,
                'competitor_c_code8' => null,
                'competitor_c_code9' => null,
                'competitor_c_code10' => null
            ],
            [
                'c_code' => '7770045782',
                'email1' => 'duong.ht+test1@workhouse.me',
                'email2' => '',
                'email3' => null,
                'goal_type' => '2',
                'summary_type' => '2',
                'grouping_key' => '1',
                'is_client_report_needed' => 'f',
                'is_competitor_sheet_needed' => 't',
                'competitor_c_code1' => null,
                'competitor_c_code2' => null,
                'competitor_c_code3' => null,
                'competitor_c_code4' => null,
                'competitor_c_code5' => null,
                'competitor_c_code6' => null,
                'competitor_c_code7' => null,
                'competitor_c_code8' => null,
                'competitor_c_code9' => null,
                'competitor_c_code10' => null
            ],
            [
                'c_code' => '7770025879',
                'email1' => 'duong.ht+test1@workhouse.me',
                'email2' => 'nam.nt+test1@workhouse.me',
                'email3' => null,
                'goal_type' => '3',
                'summary_type' => '3',
                'grouping_key' => null,
                'is_client_report_needed' => 'f',
                'is_competitor_sheet_needed' => 'f',
                'competitor_c_code1' => null,
                'competitor_c_code2' => null,
                'competitor_c_code3' => null,
                'competitor_c_code4' => null,
                'competitor_c_code5' => null,
                'competitor_c_code6' => null,
                'competitor_c_code7' => null,
                'competitor_c_code8' => null,
                'competitor_c_code9' => null,
                'competitor_c_code10' => null
            ]
        ]);
    }

    public function createDataStaffTable()
    {
        DB::table('staffs')->truncate();
        DB::table('staffs')->insert([
            [
                'email' => 'tri.vv+test1@workhouse.me',
                'name' => 'Vo Van Tri 1'
            ],
            [
                'email' => 'duong.ht+test1@workhouse.me',
                'name' => 'Ho Thuy Duong 1'
            ],
            [
                'email' => 'nam.nt+test1@workhouse.me',
                'name' => 'Nguyen Thanh Nam 1'
            ],
            [
                'email' => 'kyota.shinzato+test1@workhouse.me',
                'name' => '新里強太 1'
            ],
            [
                'email' => 'ngoc.ptd+test1@workhouse.me',
                'name' => 'Phan Tran Doan Ngoc 1'
            ],
            [
                'email' => 'thuan.nd+test1@workhouse.me',
                'name' => 'Nguyen Duc Thuan 1'
            ],
        ]);
    }
}
