<?php

namespace white\craft\mailchimp\helpers;

use white\craft\mailchimp\MailChimpPlugin;
use yii\web\BadRequestHttpException;

class ListControllerRequestHelper
{
    /**
     * @param $request
     * @return null
     * @throws BadRequestHttpException
     */
    public function getListIds($request): null
    {
        $listIds = $request->getValidatedBodyParam('listId');

        if (!$listIds) {
            $listIds = MailChimpPlugin::getInstance()->getSettings()->defaultListId;
        }

        if (!$listIds) {
            throw new BadRequestHttpException("Target MailChimp list ID not found.");
        }

        return $listIds;
    }

}