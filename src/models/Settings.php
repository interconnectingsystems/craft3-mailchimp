<?php


namespace white\craft\mailchimp\models;

use craft\base\Model;

class Settings extends Model
{
    public $apiKey = null;
    public $defaultListId = null;
    
    public function rules()
    {
        return [
            [['apiKey'], 'required'],
        ];
    }
}
