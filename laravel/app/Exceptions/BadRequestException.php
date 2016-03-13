<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 3/12/16
 * Time: 4:24 PM
 */

namespace App\Exceptions;


class BadRequestException extends \Exception
{
    /**
     * @var array
     */
    protected $context;
    /**
     * @var string
     */
    protected $statusCode;

    public function __construct($message, $status_code = 'bad_request',
                                array $context, $code = 0, \Exception $previous = null)
    {
        $this->context = $context;
        $this->statusCode = $status_code;
        parent::__construct($message, $code, $previous = null);
    }

    /**
     * @return string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    function __toString()
    {
        return "Request context: " . json_encode($this->context) .
        parent::__toString();
    }

}