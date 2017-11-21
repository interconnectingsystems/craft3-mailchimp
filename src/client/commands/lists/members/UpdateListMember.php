<?php


namespace white\craft\mailchimp\client\commands\lists\members;

use white\craft\mailchimp\client\commands\Command;
use white\craft\mailchimp\client\commands\CommandInterface;

class UpdateListMember extends Command
{
    public function __construct($listId, $memberEmail, array $memberData)
    {
        $subscriberHash = md5(strtolower($memberEmail));
        parent::__construct(CommandInterface::METHOD_PATCH, sprintf('lists/%s/members/%s', $listId, $subscriberHash), [], $memberData);
    }
}
