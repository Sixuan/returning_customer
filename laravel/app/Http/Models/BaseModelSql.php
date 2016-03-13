<?php

/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 3/8/16
 * Time: 10:08 PM
 */
namespace App\Http\Models;

class BaseModelSql
{
    /**
     * @var \Illuminate\Database\Connection
     */
    protected $conn;

    /**
     * BaseModelSql constructor.
     */
    public function __construct()
    {
        $this->conn = \DB::connection();
    }

    /**
     * @return \Illuminate\Database\Connection
     */
    public function getConn()
    {
        return $this->conn;
    }


}