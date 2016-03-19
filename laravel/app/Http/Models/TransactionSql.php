<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 3/12/16
 * Time: 7:17 PM
 */

namespace App\Http\Models;


class TransactionSql extends BaseModelSql
{
    /**
     * @var TransactionSql
     */
    private static $tranSqlSingleton;

    /**
     * @return TransactionSql
     */
    public static function getInstance() {
        if(self::$tranSqlSingleton == null) {
            self::$tranSqlSingleton = new TransactionSql();
        }
        return self::$tranSqlSingleton;
    }

    public function getTransaction($tranId) {
        $tran = $this->getConn()->table('transactions as t')
            ->leftJoin('faces as f', 't.faces_id', '=', 't.faces_id')
            ->where('t.transactions_id', '=', $tranId)
            ->first([
                't.transactions_id',
                't.amount',
                'f.age',
                'f.gender',
                'f.cameras_id',
                'f.persons_id',
                'f.confident_rate'
            ]);

        return (array)$tran;
    }

    public function createTransactionFromInputArray(array $input) {
        $insertArray = array(
            'amount' => $input['amount'],
            'faces_id' => $input['faces_id']
        );

        $id = $this->getConn()->table('transactions')
            ->insertGetId($insertArray);

        return $this->getTransaction($id);
    }

}