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
            if(!empty($insertArray)) {
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

    public function getMember($memberId) {
        $member = $this->getConn()->table('members as m')
            ->join('persons as p', 'm.persons_id', '=', 'p.persons_id')
            ->where('m.members_id', '=', $memberId)
            ->first();

        return (array)$member;
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

    public function createPersonMemberFromInputArray(array $input) {

        $faceId = $input['faces_id'];
        $personId = $this->getConn()->table('faces')
            ->where('faces_id', '=', $faceId)
            ->pluck('persons_id');
        $personId = $personId[0];

        $this->getConn()->beginTransaction();

        if(!empty($personId)){

            $memberExist = $this->getConn()->table('members')
                ->where('persons_id', '=', $personId)
                ->exists();

            if($memberExist) {
                throw new MemberExistingException("Member exist with this face ".$faceId,
                    'member_existing', array());
            }

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

            $insertArray['persons_id'] = $personId;

            $memberId = $this->getConn()->table('members')
                ->insertGetId($insertArray);
            //insert member
        }else{

            $personInsertArray = [];

            if(isset($input['age'])){
                $personInsertArray['age'] = $input['age'];
            }

            if(isset($input['gender'])){
                $personInsertArray['gender'] = $input['gender'];
            }

            $personId = $this->getConn()->table('persons')
                ->insertGetId($personInsertArray);


            $this->getConn()->table('faces')
                ->where('faces_id', '=', $faceId)
                ->update(['persons_id' => $personId]);


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

            $insertArray['persons_id'] = $personId;;

            $memberId = $this->getConn()->table('members')
                ->insertGetId($insertArray);

        }

        $this->getConn()->commit();
        $member = $this->getConn()->table('members as m')
            ->join('persons as p', 'm.persons_id', '=', 'p.persons_id')
            ->where('m.members_id', '=', $memberId)
            ->first();

        return (array)$member;
    }

}