<?php


namespace white\craft\mailchimp\controllers;

use Craft;
use craft\web\Controller;
use Exception;
use white\craft\mailchimp\client\commands\lists\GetLists;
use white\craft\mailchimp\client\commands\lists\members\AddOrUpdateListMember;
use white\craft\mailchimp\client\commands\lists\members\GetListMember;
use white\craft\mailchimp\client\MailChimpException;
use white\craft\mailchimp\MailChimpPlugin;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;

class ListController extends Controller
{
    protected $allowAnonymous = ['subscribe', 'check-if-subscribed'];
    
    
    protected function getClient()
    {
        return MailChimpPlugin::getInstance()->getClient();
    }
    
    
    public function actionSubscribe()
    {
        $request = Craft::$app->getRequest();

        try {
            if (!$request->getIsPost()) {
                throw new MethodNotAllowedHttpException();
            }

            $listIds = $this->getListIds($request);
            $email = $this->getEmail($request);
            $memberData = $this->getMemberData($email, $request);

            $this->addOrUpdateListMembers($listIds, $email, $memberData);

            if ($request->getIsAjax()) {
                return $this->asJson([
                    'success' => true,
                ]);
            } else {
                Craft::$app->getSession()->setNotice(Craft::t('mailchimp', 'Subscribed successfully.'));
                return $this->redirectToPostedUrl();
            }
            
        } catch (Exception $exception) {
            
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
            $listIds = $this->getListIds($request);
            $email = $this->getEmail($request);
            
            $member = null;
            try {
                $member = $this->getClient()->send(new GetListMember($listIds, $email));
            } catch (MailChimpException $exception) {
                if ($exception->getCode() != 404) {
                    throw $exception;
                }
            }
            
            return $this->asJson([
                'subscribed' => (bool)$member,
            ]);
        } catch (Exception $exception) {
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

    public function actionGetLists()
    {
        $request = Craft::$app->getRequest();
        $apiKey = $request->getParam('apiKey');
        if (!$apiKey) {
            throw new BadRequestHttpException();
        }
        
        $response = $this->getClient()->createClient($apiKey)
            ->send(new GetLists([
                'count' => 100,
                'fields' => 'lists.id,lists.name,lists.date_created,lists.list_rating,lists.visibility,lists.stats.member_count',
                'sort_field' => 'date_created',
                'sort_dir' => 'DESC',
            ]));
        
        $data = [];
        foreach ($response['lists'] as $list) {
            $data[] = [
                'id' => $list['id'],
                'name' => $list['name'],
                'date_created' => $list['date_created'],
                'list_rating' => $list['list_rating'],
                'visibility' => $list['visibility'],
                'stats' => $list['stats'],
            ];
        }
        return $this->asJson($data);
    }

    /**
     * @param $request
     * @return null
     * @throws BadRequestHttpException
     */
    private function getListIds($request): null
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
    private function getEmail($request)
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
    private function getMemberData($email, $request): array
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
     * @param string $listIds
     * @param string $email
     * @param array $memberData
     */
    private function addOrUpdateListMembers(string $listIds, string $email, array $memberData): void
    {
        foreach (explode(',', $listIds) as $listId) {
            if (empty($listId)) {
                continue;
            }

            $this->getClient()->send(new AddOrUpdateListMember($listId, $email, $memberData));
        }
    }
}
