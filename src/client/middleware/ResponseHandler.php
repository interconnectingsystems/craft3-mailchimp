<?php


namespace white\craft\mailchimp\client\middleware;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use white\craft\mailchimp\client\MailChimpException;

class ResponseHandler implements MiddlewareInterface
{
    /** @var bool */
    private $assoc;

    public function __construct($assoc = true)
    {
        $this->assoc = $assoc;
    }
    
    public function __invoke(callable $handler) : callable
    {
        return function (RequestInterface $request, array $options) use ($handler) {

            // Disable default Guzzle HTTP status code error handling
            $options['http_errors'] = false;

            /** @var PromiseInterface $promise */
            $promise = $handler($request, $options);

            return $promise->then(function(ResponseInterface $response) {
                if ($response->getStatusCode() >= 400) {
                    throw new MailChimpException($this->getErrorMessage($response), $response->getStatusCode());
                }
                
                return $this->decodeBody($response);
            });
        };
    }

    protected function decodeBody(ResponseInterface $response)
    {
        return \GuzzleHttp\json_decode((string)$response->getBody(), $this->assoc);
    }

    protected function getErrorMessage(ResponseInterface $response)
    {
        $body = $this->decodeBody($response);
        
        $message = sprintf(
            'An unknown error occurred while requesting MailChimp API: %s %s',
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        if (isset($body['detail'])) {
            $message = sprintf('MailChimp API Error: %s', $body['detail']);
            if (isset($body['errors']) && is_array($body['errors'])) {
                $message = str_replace('For field-specific details, see the \'errors\' array.', '', $message);
                $message = trim($message);
                foreach ($body['errors'] as $error) {
                    if (!empty($error['field'])) {
                        $message .= sprintf(' %s:', $error['field']);
                    }
                    $message .= sprintf(' %s.', $error['message']);
                }
            }
        }
        return $message;
    }
}
