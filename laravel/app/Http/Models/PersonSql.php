<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 3/8/16
 * Time: 10:32 PM
 */

namespace App\Http\Models;


class PersonSql extends BaseModelSql
{
    /**
     * @var PersonSql
     */
    private static $personSqlSingleton;

    /**
     * @return PersonSql
     */
    public static function getInstance() {
        if(self::$personSqlSingleton == null) {
            self::$personSqlSingleton = new PersonSql();
        }
        return self::$personSqlSingleton;
    }

    public function createSaleInputArray(array $input) {

    }

    public function createOrUpdateMemberForPerson(array $input, $personId) {

        $exist = $this->getConn()->table('members')
            ->where('persons_id', '=', $personId)
            ->exists();

        if($exist == true) {

            if(isset($input['phone_number'])){
                $updateArray['phone'] = $input['phone_number'];
            }

            if(isset($input['vip'])){
                $updateArray['vip'] = $input['vip'];
            }

            if(isset($input['name'])){
                $updateArray['name'] = $input['name'];
            }

            if(isset($input['email'])){
                $updateArray['email'] = $input['email'];
            }

            if(isset($input['address'])){
                $updateArray['address'] = $input['address'];
            }
            if(!empty($updateArray)) {
                $this->getConn()->table('members')
                    ->where('persons_id', '=', $personId)
                    ->update($updateArray);
            }
        }else{
            if(isset($input['phone_number'])){
                $insertArray['phone'] = $input['phone_number'];
            }

            if(isset($input['vip'])){
                $insertArray['vip'] = $input['vip'];
            }

            if(isset($input['name'])){
                $insertArray['name'] = $input['name'];
            }

            if(isset($input['email'])){
                $insertArray['email'] = $input['email'];
            }

            if(isset($input['address'])){
                $insertArray['address'] = $input['address'];
            }
            if(!empty($updateArray)) {
                $this->getConn()->table('members')
                    ->insertGetId($insertArray);
            }

        }

        $person = $this->getConn()->table('persons as p')
            ->join('faces as f', 'p.persons_id', '=', 'f.persons_id')
            ->join('images as i', 'i.faces_id', '=', 'f.faces_id')
            ->leftJoin('members as m', 'm.persons_id', '=', 'p.persons_id')
            ->where('p.persons_id', '=', $personId)
            ->first([
                'p.age',
                'p.gender',
                'i.img_path',
                'm.phone',
                'm.vip',
                'm.name',
                'm.email',
                'm.address'
            ]);

        return (array)$person;

    }

    public function getPerson($personId) {
        //Get person and most recent face img
        $person = $this->getConn()->table('persons as p')
            ->join('faces as f', 'p.persons_id', '=', 'f.persons_id')
            ->join('images as i', 'i.faces_id', '=', 'f.faces_id')
            ->leftJoin('members as m', 'm.persons_id', '=', 'p.persons_id')
            ->where('p.persons_id', '=', $personId)
            ->first([
                'p.age',
                'p.gender',
                'i.img_path',
                'm.phone',
                'm.vip',
                'm.name',
                'm.email',
                'm.address'
            ]);

        $visitsAndTrans = $this->getConn()->table('faces as f')
            ->join('persons as p', 'p.persons_id', '=', 'f.persons_id')
            ->leftJoin('transactions as t', 'f.faces_id', '=', 't.faces_id')
            ->where('p.persons_id', '=', $personId)
            ->get([
                'f.timestamp as face_timestamp',
                't.timestamp as tran_timestamp',
                't.amount'
            ]);

        $visits = $trans = [];
        $amount = 0;
        foreach($visitsAndTrans as $info) {
            $visits['time'] = $info->face_timestamp;
            if($info->amount != null){
                $amount =+ $info->amount;
                $trans[] = array(
                    'time' => $info->tran_timestamp,
                    'amount' => $info->amount
                );
            }
        }

        return array(
            'person' => (array)$person,
            'visits' => $visits,
            'purchases' => array(
                'total_amount' => $amount,
                'history' => $trans
            )
        );
    }

    public function createPersonFromInputArray(array $input) {
        $id = $this->getConn()->table('persons')
            ->insertGetId($input);
        $person = $this->getConn()->table('persons')
            ->where(array('persons_id' => $id))
            ->first();

        return (array)$person;
    }

}