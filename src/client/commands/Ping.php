<?php


namespace white\craft\mailchimp\client\commands;

class Ping extends BaseCommand
{
    public function __construct()
    {
        parent::__construct(CommandInterface::METHOD_GET, '');
    }
}
