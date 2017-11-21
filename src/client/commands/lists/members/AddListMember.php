<?php


namespace white\craft\mailchimp\client\commands\lists\members;

use white\craft\mailchimp\client\commands\Command;
use white\craft\mailchimp\client\commands\CommandInterface;

class AddListMember extends Command
{
    public function __construct($listId, array $memberData)
    {
        parent::__construct(CommandInterface::METHOD_POST, sprintf('lists/%s/members', $listId), [], $memberData);
    }
}
