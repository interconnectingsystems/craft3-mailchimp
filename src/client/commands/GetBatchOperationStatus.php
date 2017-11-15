<?php


namespace white\craft\mailchimp\client\commands;


class GetBatchOperationStatus extends BaseCommand
{
    public function __construct(string $id, array $options = [])
    {
        parent::__construct(CommandInterface::METHOD_GET, sprintf('batches/%s', $id), $options);
    }
}
