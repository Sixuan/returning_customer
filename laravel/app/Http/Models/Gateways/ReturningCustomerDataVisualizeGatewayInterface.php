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
    /**
     * @return \App\Http\Services\Socket\Contracts\SocketResponseInterface
     */
    public function get($method, array $payloads = []);

    /**
     * @return \App\Http\Services\Socket\Contracts\SocketResponseInterface
     */
    public function test();
}