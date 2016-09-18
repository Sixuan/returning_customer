<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 9/18/16
 * Time: 2:11 PM
 */
namespace App\Http\Models\Gateways;

interface ReturningCustomerDataVisualizeGatewayInterface
{
    public function findAll($storeId, array $filters = []);
}