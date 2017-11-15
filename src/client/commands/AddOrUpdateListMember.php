<?php


namespace white\craft\mailchimp\client\commands;


class AddOrUpdateListMember extends BaseCommand
{
    public function __construct($listId, $memberEmail, array $memberData)
    {
        $subscriberHash = md5(strtolower($memberEmail));
        parent::__construct(CommandInterface::METHOD_PUT, sprintf('lists/%s/members/%s', $listId, $subscriberHash), [], $memberData);
    }
}
