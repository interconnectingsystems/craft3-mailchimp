<?php


namespace white\craft\mailchimp\client\commands\lists\members;

use white\craft\mailchimp\client\commands\Command;
use white\craft\mailchimp\client\commands\CommandInterface;

class GetListMember extends Command
{
    public function __construct(string $listIds, $memberEmail, array $options = [])
    {
        $subscriberHash = md5(strtolower($memberEmail));
        parent::__construct(CommandInterface::METHOD_GET, sprintf('lists/%s/members/%s', $listIds, $subscriberHash), $options);
    }
}
