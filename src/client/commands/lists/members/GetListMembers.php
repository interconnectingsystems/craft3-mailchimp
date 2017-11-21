<?php


namespace white\craft\mailchimp\client\commands\lists\members;

use white\craft\mailchimp\client\commands\Command;
use white\craft\mailchimp\client\commands\CommandInterface;

class GetListMembers extends Command
{
    public function __construct(string $id, array $options = [])
    {
        parent::__construct(CommandInterface::METHOD_GET, sprintf('lists/%s/members', $id), $options);
    }
}
