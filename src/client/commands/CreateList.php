<?php


namespace white\craft\mailchimp\client\commands;


class CreateList extends BaseCommand
{
    public function __construct(array $listData)
    {
        parent::__construct(CommandInterface::METHOD_POST, 'lists', [], $listData);
    }
}
