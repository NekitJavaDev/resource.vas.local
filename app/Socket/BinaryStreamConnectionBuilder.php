<?php

namespace App\Socket;

use Error;

class BinaryStreamConnectionBuilder extends BinaryStreamConnectionProperties
{
    /**
     * Return built instance of BinaryStreamConnection
     *
     * @return BinaryStreamConnection built instance
     * @throws InvalidArgumentException
     */
    public function build()
    {
        if ($this->host === null && $this->uri === null) {
            throw new Error('host or uri property can not be left null or empty!');
        }
        return new BinaryStreamConnection($this);
    }

    /**
     * @param float $timeoutSec
     * @return BinaryStreamConnectionBuilder
     */
    public function setTimeoutSec($timeoutSec)
    {
        $this->timeoutSec = $timeoutSec;
        return $this;
    }

    /**
     * @param float $connectTimeoutSec
     * @return BinaryStreamConnectionBuilder
     */
    public function setConnectTimeoutSec($connectTimeoutSec)
    {
        $this->connectTimeoutSec = $connectTimeoutSec;
        return $this;
    }

    /**
     * @param float $readTimeoutSec
     * @return BinaryStreamConnectionBuilder
     */
    public function setReadTimeoutSec($readTimeoutSec)
    {
        $this->readTimeoutSec = $readTimeoutSec;
        return $this;
    }

    /**
     * @param float $writeTimeoutSec
     * @return BinaryStreamConnectionBuilder
     */
    public function setWriteTimeoutSec($writeTimeoutSec)
    {
        $this->writeTimeoutSec = $writeTimeoutSec;
        return $this;
    }

    /**
     * @param string $protocol
     * @return BinaryStreamConnectionBuilder
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
        return $this;
    }

    /**
     * @param string $host
     * @return BinaryStreamConnectionBuilder
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @param string $port
     * @return BinaryStreamConnectionBuilder
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @param string $uri
     * @return BinaryStreamConnectionBuilder
     */
    public function setUri(string $uri)
    {
        $this->uri = $uri;
        return $this;
    }

    public function setFromOptions(array $options = null)
    {
        if ($options !== null) {
            foreach ($options as $option => $value) {
                if (property_exists($this, $option)) {
                    $this->{$option} = $value;
                }
            }
        }

        return $this;
    }
}