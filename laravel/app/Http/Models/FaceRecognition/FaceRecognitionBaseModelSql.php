<?php

/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 4/17/16
 * Time: 3:11 PM
 */
namespace App\Http\Models\FaceRecognition;

class FaceRecognitionBaseModelSql
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
        $this->conn = \DB::connection('mysql_face');
    }

    /**
     * @return \Illuminate\Database\Connection
     */
    public function getConn()
    {
        return $this->conn;
    }

}