<?php


namespace white\craft\mailchimp\services;

use Craft;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\RequestInterface;
use white\craft\mailchimp\client\commands\CommandInterface;
use white\craft\mailchimp\client\HandlerStackClient;
use white\craft\mailchimp\client\middleware\RequestHandler;
use white\craft\mailchimp\client\middleware\ResponseHandler;
use white\craft\mailchimp\MailChimpPlugin;
use yii\base\Component;

class MailChimpClient extends Component
{
    /** @var ClientInterface */
    private $client;

    public function init()
    {
        $apiKey = MailChimpPlugin::getInstance()->getSettings()->apiKey;
        
        $client = $this->getGuzzleClient();

        $stack = new HandlerStack();
        $stack->push(new ResponseHandler());
        $stack->push(RequestHandler::fromApiKey($apiKey));
        
        $stack->setHandler(function(RequestInterface $request, array $options) use ($client) {
            return $client->sendAsync($request, $options);
        });

        $this->client = new HandlerStackClient($stack);
    }

    
    protected function getGuzzleClient()
    {
        return new Client();
    }

    /**
     * @return ClientInterface
     */
    public function getHttpClient()
    {
        return $this->client;
    }

    public function send(CommandInterface $command)
    {
        return $this->getHttpClient()->send($command);
    }
}
