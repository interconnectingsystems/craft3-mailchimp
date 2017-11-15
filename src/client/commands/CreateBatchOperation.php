<?php


namespace white\craft\mailchimp\client\commands;


use Assert\Assertion;

class CreateBatchOperation extends BaseCommand
{
    /**
     * @param CommandInterface[] $operations
     */
    public function __construct(array $operations)
    {
        Assertion::allIsInstanceOf($operations, CommandInterface::class);

        $data = [
            'operations' => []
        ];

        foreach ($operations as $operation) {
            $operationData = [
                'operation_id' => $operation->getOperationId(),
                'method' => $operation->getMethod(),
                'path' => (string)$operation->getUri()->getPath(),
            ];
            
            $query = (string)$operation->getUri()->getQuery();
            if ($query !== '') {
                parse_str($query, $params);
                $operationData['params'] = $params;
            }
            
            $body = (string)$operation->getBody();
            if ($body !== '') {
                $operationData['body'] = $body;
            }
            
            $data['operations'][] = $operationData;
        }

        parent::__construct(CommandInterface::METHOD_POST, 'batches', [], $data);
    }
}