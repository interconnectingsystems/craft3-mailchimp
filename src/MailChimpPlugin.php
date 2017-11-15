<?php

namespace white\craft\mailchimp;

use Craft;
use craft\base\Plugin;
use white\craft\mailchimp\client\commands\AddOrUpdateListMember;
use white\craft\mailchimp\client\commands\CreateBatchOperation;
use white\craft\mailchimp\client\commands\GetAllLists;
use white\craft\mailchimp\client\commands\GetBatchOperationStatus;
use white\craft\mailchimp\client\commands\GetList;
use white\craft\mailchimp\client\commands\GetListMember;
use white\craft\mailchimp\client\commands\GetListMembers;
use white\craft\mailchimp\models\Settings;
use white\craft\mailchimp\queue\MailChimpBatchJob;
use white\craft\mailchimp\services\MailChimpClient;

/**
 * Main MailChimp plugin class.
 * 
 * @method Settings getSettings()
 */
class MailChimpPlugin extends Plugin
{
    public function init()
    {
        parent::init();

        $this->registerServices();

        Craft::info(
            Craft::t(
                'mailchimp',
                '{name} plugin loaded ({handle})',
                ['name' => $this->name, 'handle' => $this->handle]
            ),
            __METHOD__
        );
    }

    /**
     * @return MailChimpClient
     */
    public function getClient()
    {
        return $this->{'client'};
    }
    
    protected function createSettingsModel()
    {
        return new Settings();
    }

    protected function settingsHtml()
    {
        return \Craft::$app->getView()->renderTemplate('mailchimp/settings', [
            'settings' => $this->getSettings()
        ]);
    }


    protected function registerServices()
    {
        $this->setComponents([
            'client' => MailChimpClient::class,
        ]);
    }
}
