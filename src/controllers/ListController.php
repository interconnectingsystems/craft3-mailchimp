<?php


namespace white\craft\mailchimp\controllers;

use Craft;
use craft\web\Controller;
use white\craft\mailchimp\client\commands\AddOrUpdateListMember;
use white\craft\mailchimp\client\commands\GetListMember;
use white\craft\mailchimp\client\MailChimpException;
use white\craft\mailchimp\MailChimpPlugin;
use white\craft\mailchimp\services\MailChimpClient;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;

class ListController extends Controller
{
    protected $allowAnonymous = ['subscribe', 'check-if-subscribed'];
    
    
    
    /** @var MailChimpClient */
    private $client;

    
    public function init()
    {
        parent::init();
        
        $this->client = MailChimpPlugin::getInstance()->getClient();
    }
    
    
    public function actionSubscribe()
    {
        $request = Craft::$app->getRequest();
        try {
            if (!$request->getIsPost()) {
                throw new MethodNotAllowedHttpException();
            }
            
            $listIds = $request->getValidatedBodyParam('listId');
            if (!$listIds) {
                $listIds = MailChimpPlugin::getInstance()->getSettings()->defaultListId;
                if (!$listIds) {
                    throw new BadRequestHttpException("Target MailChimp list ID not found.");
                }
            }

            $email = $request->getParam('email');
            if (!$email) {
                throw new BadRequestHttpException("Email address is not specified.");
            }
            $memberData = [
                'email_address' => $email,
                'status_if_new' => $request->getValidatedBodyParam('status') ?? 'subscribed',
                'email_type' => $request->getParam('emailType', 'html'),
                'language' => $request->getParam('language', ''),
                'vip' => (bool)($request->getValidatedBodyParam('vip') ?? false),
            ];

            $vars = $request->getParam('vars');
            if (!empty($vars) && is_array($vars)) {
                $memberData['merge_fields'] = array_map('strval', $vars);
            }

            $interests = $request->getParam('interests');
            if (!empty($interests) && is_array($interests)) {
                $memberData['interests'] = array_map('boolval', $interests);
            }

            $location = $request->getParam('location');
            if (isset($location['latitude']) && isset($location['longitude'])) {
                $memberData['location'] = [
                    'latitude' => (float)$location['latitude'],
                    'longitude' => (float)$location['longitude'],
                ];
            }

            foreach (explode(',', $listIds) as $listId) {
                if (empty($listId)) {
                    continue;
                }
                $this->client->send(new AddOrUpdateListMember($listId, $email, $memberData));
            }

            if ($request->getIsAjax()) {
                return $this->asJson([
                    'success' => true,
                ]);
            } else {
                Craft::$app->getSession()->setNotice(Craft::t('mailchimp', 'Subscribed successfully.'));
                return $this->redirectToPostedUrl();
            }
            
        } catch (\Exception $exception) {
            
            if ($request->getIsAjax()) {
                $response = $this->asJson([
                    'error' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                ])->setStatusCode(500);

                if ($exception instanceof HttpException) {
                    $response->setStatusCode($exception->statusCode);
                }
                return $response;
            } else {
                Craft::$app->getSession()->setError($exception->getMessage());
                return $this->redirectToPostedUrl();
            }
        }
    }
    
    
    public function actionCheckIfSubscribed()
    {
        $request = Craft::$app->getRequest();
        try {
            $listId = $request->getValidatedBodyParam('listId');
            if (!$listId) {
                $listId = MailChimpPlugin::getInstance()->getSettings()->defaultListId;
                if (!$listId) {
                    throw new BadRequestHttpException("Target MailChimp list ID not found.");
                }
            }

            $email = $request->getParam('email');
            if (!$email) {
                throw new BadRequestHttpException("Email address is not specified.");
            }
            
            $member = null;
            try {
                $member = $this->client->send(new GetListMember($listId, $email));
            } catch (MailChimpException $exception) {
                if ($exception->getCode() != 404) {
                    throw $exception;
                }
            }
            
            return $this->asJson([
                'subscribed' => (bool)$member,
            ]);
        } catch (\Exception $exception) {
            $response = $this->asJson([
                'error' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ])->setStatusCode(500);
            
            if ($exception instanceof HttpException) {
                $response->setStatusCode($exception->statusCode);
            }
            return $response;
        }
    }
}
