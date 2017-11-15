<?php


namespace white\craft\mailchimp\client\commands;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;

abstract class BaseCommand extends Request implements CommandInterface
{ 
    private $operationId;

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $params
     * @param array|null $body
     */
    public function __construct(string $method, string $endpoint, array $params = [], array $body = null)
    {
        $uri = new Uri($endpoint);
        if (!empty($params)) {
            $uri = $uri->withQuery(http_build_query($params, null, '&', PHP_QUERY_RFC3986));
        }

        if ($body !== null && !is_string($body)) {
            $body  = \GuzzleHttp\json_encode($body);
        }
        
        parent::__construct($method, $uri, [], $body);
    }

    /** {@inheritdoc} */
    public function getOperationId()
    {
        if ($this->operationId === null) {
            $this->operationId = uniqid('', true);
        }
        return $this->operationId;
    }

    public function withOperationId(string $operationId)
    {
        $new = clone $this;
        $new->operationId = $operationId;
        return $new;
    }

}
