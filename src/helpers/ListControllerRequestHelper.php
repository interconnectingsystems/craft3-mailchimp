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


    /**
     * @param $request
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function getEmail(Request $request)
    {
        $email = $request->getParam('email');

        if (!$email) {
            throw new BadRequestHttpException("Email address is not specified.");
        }

        return $email;
    }

    /**
     * @param $email
     * @param $request
     * @return array
     */
    public function getMemberData(string $email, Request $request): array
    {
        $memberData = [
            'email_address' => $email,
            'status_if_new' => $request->getValidatedBodyParam('status') ?? 'subscribed',
            'email_type' => $request->getParam('emailType', 'html'),
            'language' => $request->getParam('language', ''),
            'vip' => (bool)($request->getValidatedBodyParam('vip') ?? false),
        ];

        $vars = $request->getParam('vars');
        $interests = $request->getParam('interests');
        $location = $request->getParam('location');

        if (!empty($vars) && is_array($vars)) {
            $memberData['merge_fields'] = array_map('strval', $vars);
        }

        if (!empty($interests) && is_array($interests)) {
            $memberData['interests'] = array_map('boolval', $interests);
        }

        if (isset($location['latitude']) && isset($location['longitude'])) {
            $memberData['location'] = [
                'latitude' => (float)$location['latitude'],
                'longitude' => (float)$location['longitude'],
            ];
        }

        return $memberData;
    }

    /**
     * @param $request
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function getApiKey($request)
    {
        $apiKey = $request->getParam('apiKey');
        if (!$apiKey) {
            throw new BadRequestHttpException();
        }
        return $apiKey;
    }
}
