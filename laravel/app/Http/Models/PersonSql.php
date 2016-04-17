<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 3/8/16
 * Time: 10:32 PM
 */

namespace App\Http\Models;


use App\Exceptions\AuthException;
use App\Exceptions\BadRequestException;
use App\Exceptions\NonExistingException;

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

    /**
     * @param array $input
     * @return array
     * @throws AuthException
     * @throws BadRequestException
     */
    public function authenticateSale(array $input) {

        if(!isset($input['username']) || !isset($input['password'])) {
            throw new BadRequestException("username and password required", 'bad_request');
        }

        $exist = $this->getConn()->table('sales')
            ->where('name','=', trim($input['username']))
            ->where('password', '=', md5($input['password']));

        if(isset($input['manager']) && $input['manager'] == 'Y') {
            $exist->where('manager', '=', 'Y');
        }

        $exist = $exist->exists();


        if($exist){
            $token = md5(time());
            $updateArray = [
                'remember_token' => $token,
                'updated_at' => \DB::raw("now()")
            ];

            $this->getConn()->table('sales')
                ->where('name','=', $input['username'])
                ->update($updateArray);


            return (array)$this->getConn()->table('sales as s')
                ->join('stores as st', 's.stores_id', '=', 'st.stores_id')
                ->where('s.name','=', $input['username'])
                ->first([
                    'st.stores_id',
                    's.name',
                    's.remember_token',
                    's.sales_id',
                    's.manager'
                ]);

        }else{
            throw new AuthException("user bad credential or account not existing", "bad_auth_or_account_non_existing");
        }
    }


    public function createSaleInputArray($storeId, array $input) {
        /**
         * {
        "name" : "L B",
        "phone" : "1232222222",
        "dob" : "1985-12-12"
        "email" : "123@d.com",
        "password" : 123456
        "address" : "九龙"
        }

         */

        if(!isset($input['name']) || !isset($input['password'])) {
            throw new BadRequestException("name and password required", 'bad_request');
        }

        $exist = $this->getConn()->table('sales')
            ->where('name','=', trim($input['name']))
            ->exists();

        if($exist){
            throw new BadRequestException("name already existed", 'bad_request');
        }

        $input['password'] = md5($input['password']);
        $input['stores_id'] = $storeId;

        $token = md5(time());
        $input['remember_token'] = $token;
        $input['created_at'] = $input['updated_at'] = \DB::raw("now()");

        $id = $this->getConn()->table('sales')
            ->insertGetId($input);

        return (array)$this->getConn()->table('sales')
            ->where('sales_id', '=', $id)
            ->first([
                'name',
                'dob',
                'email',
                'phone',
                'address',
                'manager',
                'remember_token'
            ]);
    }

    /**
     * @param $saleId
     * @throws NonExistingException
     */
    public function validateSaleExisting($saleId) {
        $sale = $this->getConn()->table('sales')
            ->where('sales_id', '=', $saleId)
            ->exists();

        if(!$sale){
            throw new NonExistingException("Sale id is invalid, can not find.", "sale_non_existing");
        }
    }

    /**
     * @param $saleId
     * @param $password
     * @throws AuthException
     */
    public function validatePasswordForSale($saleId, $password) {
        $sale = $this->getConn()->table('sales')
            ->where('sales_id', '=', $saleId)
            ->where('password', '=', md5($password))
            ->exists();

        if(!$sale){
            throw new AuthException("Wrong password provided.", "wrong_password");
        }
    }

    /**
     * @param array $input
     * @param $saleId
     * @return array
     * @throws AuthException
     * @throws BadRequestException
     */
    public function updateSaleInfo(array $input, $saleId) {

        if(!isset($input['original_password']) || !isset($input['password'])){
            throw new BadRequestException("original_password and password required", 'bad_request');
        }
        $this->validateSaleExisting($saleId);
        $pass = $input['original_password'];
        $this->validatePasswordForSale($saleId, $pass);

        if(isset($input['password'])) {
            $updateArray['password'] = md5($input['password']);
            $this->getConn()->table('sales')
                ->where('sales_id', '=', $saleId)
                ->update($updateArray);
        }

        return (array)$this->getConn()->table('sales')
            ->where('sales_id', '=', $saleId)
            ->first([
                'name',
                'dob',
                'email',
                'phone',
                'address'
            ]);
    }

    /**
     * @param $saleId
     */
    public function deleteSale($saleId) {
        $this->getConn()->table('sales')
            ->where('sales_id', '=', $saleId)
            ->delete();
    }

    /**
     * @param $storeId
     * @return array
     */
    public function getStoreSales($storeId) {

        $sales = (array)$this->getConn()->table('sales')
            ->where('stores_id','=', $storeId)
            ->get([
                'sales_id',
                'name as login_username',
                'manager',
            ]);

        return $sales;

    }

    /**
     * @param array $input
     * @return array
     */
    public function createPersonFromInputArray(array $input) {

        $id = $this->getConn()->table('persons')
            ->insertGetId($input);

        return (array)$this->getConn()->table('persons')
            ->where('persons_id', '=', $id)
            ->first();
    }

    /**
     * @param array $input
     * @param $personId
     * @return array
     */
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

        if(isset($input['age'])){
            $updateArray['age'] = $input['age'];
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

    /**
     * @param $personId
     * @return array
     */
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