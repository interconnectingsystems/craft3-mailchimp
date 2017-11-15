<?php


namespace white\craft\mailchimp\client\commands;

use Psr\Http\Message\RequestInterface;

interface CommandInterface extends RequestInterface
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    
    public function getOperationId();
}
