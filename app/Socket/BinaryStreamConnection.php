<?php

namespace App\Socket;

use InvalidArgumentException;

class BinaryStreamConnection extends BinaryStreamConnectionProperties
{
    use StreamHandler;

    /**
     * @var resource communication stream
     */
    private $stream;

    public function __construct(BinaryStreamConnectionBuilder $builder)
    {
        $this->uri = $builder->getUri();
        $this->host = $builder->getHost();
        $this->port = $builder->getPort();
        $this->timeoutSec = $builder->getTimeoutSec();
        $this->connectTimeoutSec = $builder->getConnectTimeoutSec();
        $this->readTimeoutSec = $builder->getReadTimeoutSec();
        $this->writeTimeoutSec = $builder->getWriteTimeoutSec();
        $this->protocol = $builder->getProtocol();
    }

    public static function getBuilder()
    {
        return new BinaryStreamConnectionBuilder();
    }

    public function connect()
    {
        $uri = $this->uri;
        if ($uri === null) {
            $protocol = strtolower($this->getProtocol());
            if (!($protocol === 'tcp' || $protocol === 'udp')) {
                throw new InvalidArgumentException("Unknown protocol, should be 'TCP' or 'UDP'");
            }
            $uri = "{$protocol}://{$this->host}:{$this->port}";
        }

        $this->stream = @stream_socket_client(
            $uri,
            $errno,
            $errstr,
            $this->connectTimeoutSec,
            STREAM_CLIENT_CONNECT
        );

        if (false === $this->stream) {
            $message = "Unable to create client socket to {$uri}: {$errstr}";
            throw new \RuntimeException($message, $errno);
        }

        stream_set_blocking($this->stream, false); // use non-blocking stream

        // set as stream timeout as we use 'stream_select' to read data and this method has its own timeout
        // this call will only affect our fwrite parts (send data method)
        stream_set_timeout(
            $this->stream,
            (int) $this->getWriteTimeoutSec(),
            $this->extractUsec($this->getWriteTimeoutSec())
        );

        return $this;
    }

    public function receive()
    {
        $result = $this->receiveFrom([$this->stream], $this->getReadTimeoutSec());
        return reset($result);
    }

    public function send($packet)
    {
        if (!\is_resource($this->stream) || @\feof($this->stream)) {
            throw new \RuntimeException('Can not write - stream closed by the peer');
        }

        fwrite($this->stream, $packet, strlen($packet));

        return $this;
    }

    public function sendAndReceive($packet)
    {
        return $this->send($packet)->receive();
    }

    public function close()
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
            $this->stream = null;
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * @param float $seconds
     * @return int
     */
    private function extractUsec($seconds)
    {
        return (int) (($seconds - (int) $seconds) * 1e6);
    }

    public function getStream()
    {
        return $this->stream;
    }
}