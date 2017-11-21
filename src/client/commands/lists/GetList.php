<?php


namespace white\craft\mailchimp\client\commands\lists;

use white\craft\mailchimp\client\commands\Command;
use white\craft\mailchimp\client\commands\CommandInterface;

class GetList extends Command
{
    public function __construct(string $id, array $options = [])
    {
        parent::__construct(CommandInterface::METHOD_GET, sprintf('lists/%s', $id), $options);
    }
}
