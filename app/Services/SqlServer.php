<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Utils\Log;
use Illuminate\Support\Facades\DB;

class SqlServer
{
    protected $connect;
    protected $log;

    public function __construct()
    {
        $this->log = new Log();
        $this->connect = DB::connection('nabiSqlsrv');
    }

    public function showTable()
    {
        return $this->connect->select('SELECT name FROM sys.databases');
    }

    public function getSCode($c_code)
    {
        $s_code = $this->connect->selectOne("select s_cd from dbo.m_c_cd_s_cd where c_cd like '$c_code'");
        try {
            if (empty($s_code)) {
                throw new CustomException('Not found any s_code match with c_code'.$c_code);
            }

            if (empty($s_code->s_cd)) {
                throw new CustomException('Cannot get s_cd from object !');
            }

            return $s_code->s_cd;
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return false;
        } catch (\Exception $exception) {
            $this->log->logWarnning("Cannot execute query to get s_code for each c_code");
            return false;
        }
    }

    public function getShopName($c_code)
    {
        $client_nm = $this->connect->selectOne("select * from dbo.m_client where c_cd like '$c_code'");
        try {
            if (empty($client_nm)) {
                throw new CustomException('Not found any client_nm match with c_code '.$c_code);
            }

            if (empty($client_nm->client_nm)) {
                throw new CustomException('Cannot get client_nm from object !');
            }

            return $client_nm->client_nm;
        } catch (CustomException $exception) {
            $exception::dumpAndLogWarnning($exception->getMessage());
            return false;
        } catch (\Exception $exception) {
            $this->log->logWarnning("Cannot execute query to get shop name for each c_code");
            return false;
        }
    }
}
