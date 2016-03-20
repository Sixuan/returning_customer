<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 3/8/16
 * Time: 10:32 PM
 */

namespace App\Http\Models;


use App\Exceptions\MemberExistingException;

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

    public function updateSaleInfo(array $input, $id) {

    }

    public function createPersonFromInputArray(array $input) {

        $id = $this->getConn()->table('persons')
            ->insertGetId($input);

        return (array)$this->getConn()->table('persons')
            ->where('persons_id', '=', $id)
            ->first();
    }

    public function updateMember(array $input, $personId) {

        if(isset($input['phone'])){
            $updateArray['phone'] = $input['phone'];
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
            $this->getConn()->table('persons')
                ->where('persons_id', '=', $personId)
                ->update($updateArray);
        }


        $person = $this->getConn()->table('persons as p')
            ->join('faces as f', 'p.persons_id', '=', 'f.persons_id')
            ->join('images as i', 'i.faces_id', '=', 'f.faces_id')
            ->where('p.persons_id', '=', $personId)
            ->first([
                'p.age',
                'p.gender',
                'i.img_path',
                'p.phone',
                'p.vip',
                'p.name',
                'p.email',
                'p.address'
            ]);

        return (array)$person;

    }

    public function getMember($personId) {
        $person = $this->getConn()->table('persons')
            ->where('persons_id', '=', $personId)
            ->first();

        return (array)$person;
    }

    public function getPersons($personIds) {
        $personsInfo = $this->getConn()->table('persons as p')
            ->whereIn('p.persons_id', explode(',', $personIds))
            ->get(['p.name', 'p.persons_id']);

        return (array)$personsInfo;
    }

    public function getPerson($personId) {
        //Get person and most recent face img
        $person = $this->getConn()->table('persons as p')
            ->join('faces as f', 'p.persons_id', '=', 'f.persons_id')
            ->join('images as i', 'i.faces_id', '=', 'f.faces_id')
            ->where('p.persons_id', '=', $personId)
            ->first([
                'p.age',
                'p.gender',
                'i.img_path',
                'p.phone',
                'p.vip',
                'p.name',
                'p.email',
                'p.address',
                \DB::raw('IF(p.time_joined IS NULL, "N", "Y") as isMember')
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

    public function createPersonMemberFromInputArray(array $input) {

        $faceId = $input['faces_id'];
        $personId = $this->getConn()->table('faces')
            ->where('faces_id', '=', $faceId)
            ->pluck('persons_id');
        $personId = $personId[0];

        $this->getConn()->beginTransaction();
        //if person existing
        if(!empty($personId)){
            $updateArray = [];
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

            if(sizeof($updateArray) > 0){
                $this->getConn()->table('persons')
                    ->where('persons_id', '=', $personId)
                    ->update($updateArray);
            }
            //insert person(member)

        }else{

            $insertArray = [
                'time_joined' => \DB::raw("now()")
            ];

            if(isset($input['age'])){
                $insertArray['age'] = $input['age'];
            }

            if(isset($input['gender'])){
                $insertArray['gender'] = $input['gender'];
            }

            if(isset($input['phone'])){
                $insertArray['phone'] = $input['phone'];
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

            $personId = $this->getConn()->table('persons')
                ->insertGetId($insertArray);


            $this->getConn()->table('faces')
                ->where('faces_id', '=', $faceId)
                ->update(['persons_id' => $personId]);

        }

        $this->getConn()->commit();
        $person = $this->getConn()->table('persons')
            ->where('persons_id', '=', $personId)
            ->first();

        return (array)$person;
    }

}