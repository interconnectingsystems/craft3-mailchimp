<?php


namespace white\craft\mailchimp\client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

class HandlerStackClient implements ClientInterface
{
    /** @var HandlerStack */
    private $handlerStack;

    public function __construct(HandlerStack $handlerStack)
    {
        $this->handlerStack = $handlerStack;
    }

    /** {@inheritdoc} */
    public function send(RequestInterface $request, array $options = [])
    {
        return $this->sendAsync($request, $options)->wait();
    }

    /** {@inheritdoc} */
    public function sendAsync(RequestInterface $request, array $options = [])
    {
        $stack = $this->handlerStack;

        /** @var PromiseInterface $result */
        $result = $stack($request, $options);

        return $result;
    }

    /** {@inheritdoc} */
    public function request($method, $uri, array $options = [])
    {
        return $this->send(new Request($method, $uri), $options);
    }

    /** {@inheritdoc} */
    public function requestAsync($method, $uri, array $options = [])
    {
        return $this->sendAsync(new Request($method, $uri), $options);
    }

    /** {@inheritdoc} */
    public function getConfig($option = null)
    {
        return null;
    }
}
