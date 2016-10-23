<?php

namespace App\Http\Models\Gateways;
use App\Http\Services\Socket\Contracts\SocketClientInterface;
use App\Http\Services\Socket\SocketClient;
use App\Http\Services\Socket\SocketRequest;

/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 9/18/16
 * Time: 2:08 PM
 */
class ReturningCustomerDataVisualizeGateway implements ReturningCustomerDataVisualizeGatewayInterface
{

    const RECOGNITION_GATEWAY_HOST_DEFAULT = 'localhost';

    const RECOGNITION_PORT_DEFAULT = 54320;

    const RECOGNITION_GATEWAY_CONNECTION_TIMEOUT = 10;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var SocketClientInterface
     */
    private $socketClient;

    /**
     * @var ReturningCustomerDataVisualizeGatewayInterface
     */
    private static $gateway;

    /**
     * @return ReturningCustomerDataVisualizeGatewayInterface
     */
    public static function getInstance() {
        if(self::$gateway == null) {
            self::$gateway = new ReturningCustomerDataVisualizeGateway(SocketClient::getInstance());
        }
        return self::$gateway;
    }

    /**
     * ReturningCustomerDataVisualizeGateway constructor.
     * @param SocketClientInterface $socketClient
     */
    public function __construct(SocketClientInterface $socketClient)
    {
        $this->socketClient = $socketClient;
        $this->host = env('SOCKET_HOST', self::RECOGNITION_GATEWAY_HOST_DEFAULT);
        $this->port = env('SOCKET_PORT', self::RECOGNITION_PORT_DEFAULT);
    }

    public function findAll($storeId, array $filters = [])
    {
        $payload = [
            'method' => 'findAll',
            'payload' => [
                'store_id' => $storeId
            ]
        ];

        $request = new SocketRequest(
            $payload,
            $this->host,
            $this->port,
            self::RECOGNITION_GATEWAY_CONNECTION_TIMEOUT
        );

        $response = $this->socketClient->send($request);
        return $response;

    }

    /**
     * @return \App\Http\Services\Socket\Contracts\SocketResponseInterface
     */
    public function test()
    {
        $payload = [
            'method' => 'test',
            'payload' => [
            ]
        ];

        $request = new SocketRequest(
            $payload,
            $this->host,
            $this->port,
            self::RECOGNITION_GATEWAY_CONNECTION_TIMEOUT
        );

        $response = $this->socketClient->send($request);
        return $response;

    }
}
