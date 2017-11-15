<?php


namespace white\craft\mailchimp\client\middleware;

use Assert\Assertion;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Psr\Http\Message\RequestInterface;

class RequestHandler implements MiddlewareInterface
{
    const DEFAULT_BASE_URL_PATTERN = 'https://{dc}.api.mailchimp.com/3.0/';
    
    const DEFAULT_USERNAME = 'api';
    
    /** @var Uri */
    private $baseUrl;
    
    /** @var string */
    private $username;
    
    /** @var string */
    private $password;

    public function __construct(Uri $baseUrl, string $username, string $password)
    {
        $this->baseUrl = $baseUrl;
        $this->username = $username;
        $this->password = $password;
    }
    
    public static function fromApiKey(string $apiKey, string $baseUrlPattern = self::DEFAULT_BASE_URL_PATTERN)
    {
        Assertion::contains($baseUrlPattern, '{dc}', 'Invalid MailChimp baseUrl pattern: should contain the "{dc}" placeholder.');
        
        list(, $dc) = explode('-', $apiKey . '-');
        if (empty($dc)) {
            throw new \InvalidArgumentException('Invalid MailChimp credentials: token should contain a DC part.');
        }

        $baseUrl = str_replace('{dc}', $dc, $baseUrlPattern);
        return new static(new Uri($baseUrl), static::DEFAULT_USERNAME, $apiKey);
    }

    /**
     * @return Uri
     */
    public function getBaseUrl() : Uri
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getUsername() : string
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getPassword() : string
    {
        return $this->password;
    }

    public function __invoke(callable $handler) : callable
    {
        return function (RequestInterface $request, array $options) use ($handler) {

            // Merge with the base URL
            $uri = UriResolver::resolve($this->getBaseUrl(), $request->getUri());

            // Add credentials
            $uri = $uri->withUserInfo($this->getUsername(), $this->getPassword());
            
            $request = $request
                ->withUri($uri)
                ->withHeader('Accept', 'application/json')
                ->withHeader('Content-Type', 'application/json');

            return $handler($request, $options);
        };
    }
}
