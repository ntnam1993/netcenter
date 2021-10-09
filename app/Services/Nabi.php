<?php

namespace App\Services;

class Nabi
{
    public $mySql;
    public $sqlServer;

    public function __construct()
    {
        $this->mySql = new MySql(); //phpcs:ignore
        $this->sqlServer = new SqlServer(); //phpcs:ignore
    }
}
