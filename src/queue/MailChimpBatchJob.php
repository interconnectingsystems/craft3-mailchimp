<?php


namespace white\craft\mailchimp\queue;

use craft\queue\BaseJob;
use white\craft\mailchimp\client\commands\batch\GetBatchOperationStatus;
use white\craft\mailchimp\MailChimpPlugin;
use yii\queue\RetryableJob;

class MailChimpBatchJob extends BaseJob// implements RetryableJob
{
    public $batchId;
    
    public $maxRetryCount = 7;
    
    public $startingWaitInterval = 5;
    
    public $waitIntervalMultiplier = 2.0;
    

    public function execute($queue)
    {
        $attempt = 0;
        $interval = $this->startingWaitInterval;
        while (!$this->checkBatchStatus($queue)) {
            if (++$attempt >= $this->maxRetryCount) {
                throw new JobNotFinishedException('MailChimp batch request takes too long to finish.');
            }
            
            sleep((int)$interval);
            $interval *= $this->waitIntervalMultiplier;
        }
    }
    
    protected function checkBatchStatus($queue)
    {
        $client = MailChimpPlugin::getInstance()->getClient();

        $batchOperation = $client->send(new GetBatchOperationStatus($this->batchId));
        $this->setProgress($queue, $batchOperation['finished_operations'] / $batchOperation['total_operations']);

        if ($batchOperation['status'] == 'finished') {
            return true;
        }
        
        return false;
    }

//    public function getTtr()
//    {
//        return 15*60;
//    }
//
//    public function canRetry($attempt, $error)
//    {
//        return ($attempt < 5) && ($error instanceof JobNotFinishedException);
//    }
    
    protected function defaultDescription(): string
    {
        return \Craft::t('mailchimp', 'MailChimp batch job');
    }
}
