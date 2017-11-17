<?php


namespace white\craft\mailchimp\client\commands;


class GetLists extends BaseCommand
{
    public function __construct(array $options = [])
    {
        parent::__construct(CommandInterface::METHOD_GET, 'lists', $options);
    }
}
