<?php


namespace white\craft\mailchimp\client\commands;


class GetAllLists extends BaseCommand
{
    public function __construct(array $options = [])
    {
        parent::__construct(CommandInterface::METHOD_GET, 'lists', $options);
    }
}
