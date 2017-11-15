<?php


namespace white\craft\mailchimp\client\commands;


class RemoveListMember extends BaseCommand
{
    public function __construct($listId, $memberEmail)
    {
        $subscriberHash = md5(strtolower($memberEmail));
        parent::__construct(CommandInterface::METHOD_DELETE, sprintf('lists/%s/members/%s', $listId, $subscriberHash));
    }
}
