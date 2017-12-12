<?php


namespace white\craft\mailchimp\controllers;

use Craft;
use craft\web\Controller;
use craft\web\Request;
use Exception;
use white\craft\mailchimp\helpers\ListControllerRequestHelper;
use white\craft\mailchimp\helpers\MailClientHelper;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Response;

class ListController extends Controller
{
    protected $allowAnonymous = ['subscribe', 'check-if-subscribed'];

    /**
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionSubscribe()
    {
        $request = Craft::$app->getRequest();

        try {
            if (!$request->getIsPost()) {
                throw new MethodNotAllowedHttpException();
            }

            $requestHelper = new ListControllerRequestHelper();
            $listIds = $requestHelper->getListIds($request);
            $email = $requestHelper->getEmail($request);
            $memberData = $requestHelper->getMemberData($email, $request);

            $mailclientHelper = new MailClientHelper();
            $mailclientHelper->addOrUpdateListMembers($listIds, $email, $memberData);

            return $this->renderSuccessResponse(
                $request,
                ['success' => true],
                $message = Craft::t('mailchimp', 'Subscribed successfully.')
            );

        } catch (Exception $exception) {
            return $this->renderErrorResponse($request, $exception);
        }
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionCheckIfSubscribed()
    {
        $request = Craft::$app->getRequest();
        $requestHelper = new ListControllerRequestHelper();

        try {
            $mailclientHelper = new MailClientHelper();
            $listIds = $requestHelper->getListIds($request);
            $email = $requestHelper->getEmail($request);
            $member = $mailclientHelper->getMember($listIds, $email);
            return $this->asJson(['subscribed' => (bool)$member]);
        } catch (Exception $exception) {
            return $this->renderErrorResponse($request, $exception);
        }
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionGetLists()
    {
        $mailclientHelper = new MailClientHelper();
        $request = Craft::$app->getRequest();
        $requestHelper = new ListControllerRequestHelper();
        $apiKey = $requestHelper->getApiKey($request);
        $lists = $mailclientHelper->getLists($apiKey);

        return $this->asJson($mailclientHelper->parseLists($lists));
    }

    /**
     * @param Request $request
     * @param $data
     * @param string $message
     * @return Response
     * @throws BadRequestHttpException
     */
    private function renderSuccessResponse(Request $request, $data, string $message): Response
    {
        if ($request->getIsAjax()) {
            $response = $this->asJson($data);
        } else {
            Craft::$app->getSession()->setNotice($message);
            $response = $this->redirectToPostedUrl();
        }

        return $response;
    }

    /**
     * @param $request
     * @param $exception
     * @return Response
     * @throws BadRequestHttpException
     */
    private function renderErrorResponse(Request $request, Exception $exception): Response
    {
        if ($request->getIsAjax()) {
            $response = $this->asJson([
                'error' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ]);

            $response->setStatusCode(500);

            if ($exception instanceof HttpException) {
                $response->setStatusCode($exception->statusCode);
            }

        } else {
            Craft::$app->getSession()->setError($exception->getMessage());
            $response = $this->redirectToPostedUrl();
        }

        return $response;
    }
}
