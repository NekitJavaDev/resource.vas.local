<?php

namespace App\Socket;

/**
 * BinaryStreamConnection immutable properties base class
 *
 * uses same properties as PhpModbus library ModbusMaster class
 */
abstract class BinaryStreamConnectionProperties
{
    /**
     * @var float Total response timeout (seconds, decimals allowed)
     */
    protected $timeoutSec = 5;
    /**
     * @var float maximum timeout when establishing connection (seconds, decimals allowed)
     */
    protected $connectTimeoutSec = 1;
    /**
     * @var float read timeout (seconds, decimals allowed)
     */
    protected $readTimeoutSec = 5;
    /**
     * @var float maximum timeout for write operation on connection (seconds, decimals allowed)
     */
    protected $writeTimeoutSec = 1;

    /**
     * @var string uri to connect to. Has higher priority than $protocol/$host/$port. Example: 'tcp://192.168.0.1:502'
     */
    protected $uri;
    /**
     * @var string network protocol (TCP, UDP)
     */
    protected $protocol = 'UDP';
    /**
     * @var string|null Modbus device IP address
     */
    protected $host;
    /**
     * @var string gateway port
     */
    protected $port = 40000;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @return float Total response timeout (seconds, decimals allowed)
     */
    public function getTimeoutSec()
    {
        return $this->timeoutSec;
    }

    /**
     * @return float maximum timeout when establishing connection (seconds, decimals allowed)
     */
    public function getConnectTimeoutSec()
    {
        return $this->connectTimeoutSec;
    }

    /**
     * @return float read timeout (seconds, decimals allowed)
     */
    public function getReadTimeoutSec()
    {
        return $this->readTimeoutSec;
    }

    /**
     * @return float maximum timeout for write operation on connection (seconds, decimals allowed)
     */
    public function getWriteTimeoutSec()
    {
        return $this->writeTimeoutSec;
    }

    /**
     * @return string network protocol (TCP, UDP)
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @return string Modbus device IP address
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return string gateway port
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }
}