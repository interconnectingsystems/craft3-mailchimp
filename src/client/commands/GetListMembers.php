<?php


namespace white\craft\mailchimp\client\commands;


class GetListMembers extends BaseCommand
{
    public function __construct(string $id, array $options = [])
    {
        parent::__construct(CommandInterface::METHOD_GET, sprintf('lists/%s/members', $id), $options);
    }
}
