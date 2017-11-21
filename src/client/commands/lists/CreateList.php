<?php


namespace white\craft\mailchimp\client\commands\lists;

use white\craft\mailchimp\client\commands\Command;
use white\craft\mailchimp\client\commands\CommandInterface;

class CreateList extends Command
{
    public function __construct(array $listData)
    {
        parent::__construct(CommandInterface::METHOD_POST, 'lists', [], $listData);
    }
}
