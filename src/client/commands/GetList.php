<?php


namespace white\craft\mailchimp\client\commands;


class GetList extends BaseCommand
{
    public function __construct(string $id, array $options = [])
    {
        parent::__construct(CommandInterface::METHOD_GET, sprintf('lists/%s', $id), $options);
    }
}
