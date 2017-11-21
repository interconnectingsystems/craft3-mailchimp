<?php


namespace white\craft\mailchimp\client\commands\batch;

use white\craft\mailchimp\client\commands\Command;
use white\craft\mailchimp\client\commands\CommandInterface;

class GetBatchOperationStatus extends Command
{
    public function __construct(string $id, array $options = [])
    {
        parent::__construct(CommandInterface::METHOD_GET, sprintf('batches/%s', $id), $options);
    }
}
