<?php

namespace white\craft\mailchimp;

use Craft;
use craft\base\Plugin;

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
}
