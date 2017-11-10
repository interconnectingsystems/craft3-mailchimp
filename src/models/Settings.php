<?php


namespace white\craft\mailchimp\models;

use craft\base\Model;

class Settings extends Model
{
    public $apiToken = null;
    
    public function rules()
    {
        return [
            [['apiToken'], 'required'],
        ];
    }
}
