<?php

namespace white\craft\mailchimp\helpers;

use white\craft\mailchimp\client\commands\lists\members\AddOrUpdateListMember;
use white\craft\mailchimp\client\commands\lists\members\GetListMember;
use white\craft\mailchimp\client\MailChimpException;
use white\craft\mailchimp\MailChimpPlugin;
use yii\web\Response;

class MailClientHelper
{
    /**
     * @return \white\craft\mailchimp\services\MailChimpClient
     */
    private function getClient()
    {
        return MailChimpPlugin::getInstance()->getClient();
    }

    /**
     * @param $listIds
     * @param $email
     * @return array
     * @throws MailChimpException
     */
    public function getMember($listIds, $email): array
    {
        $member = null;

        try {
            $member = $this->getClient()->send(new GetListMember($listIds, $email));
        } catch (MailChimpException $exception) {
            if ($exception->getCode() != 404) {
                throw $exception;
            }
        }
        return $member;
    }

    /**
     * @param string $listIds
     * @param string $email
     * @param array $memberData
     */
    public function addOrUpdateListMembers(string $listIds, string $email, array $memberData): void
    {
        foreach (explode(',', $listIds) as $listId) {
            if (empty($listId)) {
                continue;
            }

            $this->getClient()->send(new AddOrUpdateListMember($listId, $email, $memberData));
        }
    }

    /**
     * @param $apiKey
     * @return mixed|ResponseInterface
     */
    public function getLists($apiKey)
    {
        $response = $this->getClient()->createClient($apiKey)
            ->send(new GetLists([
                'count' => 100,
                'fields' => 'lists.id,lists.name,lists.date_created,lists.list_rating,lists.visibility,lists.stats.member_count',
                'sort_field' => 'date_created',
                'sort_dir' => 'DESC',
            ]));
        return $response;
    }

    /**
     * @param $lists
     * @return array
     */
    public function parseLists($lists)
    {
        $data = [];

        foreach ($lists['lists'] as $list) {
            $data[] = [
                'id' => $list['id'],
                'name' => $list['name'],
                'date_created' => $list['date_created'],
                'list_rating' => $list['list_rating'],
                'visibility' => $list['visibility'],
                'stats' => $list['stats'],
            ];
        }

        return $data;
    }
}
