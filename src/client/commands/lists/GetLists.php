<?php


namespace white\craft\mailchimp\client\commands\lists;

use white\craft\mailchimp\client\commands\Command;
use white\craft\mailchimp\client\commands\CommandInterface;

class GetLists extends Command
{
    public function __construct(array $options = [])
    {
        parent::__construct(CommandInterface::METHOD_GET, 'lists', $options);
    }
}
