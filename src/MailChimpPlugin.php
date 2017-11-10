<?php

namespace white\craft\mailchimp;

use Craft;
use craft\base\Plugin;
use white\craft\mailchimp\models\Settings;

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

        Craft::info(
            Craft::t(
                'mailchimp',
                '{name} plugin loaded ({handle})',
                ['name' => $this->name, 'handle' => $this->handle]
            ),
            __METHOD__
        );
    }
    
    protected function createSettingsModel()
    {
        return new Settings();
    }

    protected function settingsHtml()
    {
        return \Craft::$app->getView()->renderTemplate('mailchimp/settings.html', [
            'settings' => $this->getSettings()
        ]);
    }
}
