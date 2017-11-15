<?php


namespace white\craft\mailchimp\client\commands;


class GetListMember extends BaseCommand
{
    public function __construct(string $id, $memberEmail, array $options = [])
    {
        $subscriberHash = md5(strtolower($memberEmail));
        parent::__construct(CommandInterface::METHOD_GET, sprintf('lists/%s/members/%s', $id, $subscriberHash), $options);
    }
}
