<?php


namespace white\craft\mailchimp\client\commands;


class AddListMember extends BaseCommand
{
    public function __construct($listId, array $memberData)
    {
        parent::__construct(CommandInterface::METHOD_POST, sprintf('lists/%s/members', $listId), [], $memberData);
    }
}
