<?php

namespace Remp\MailerModule\Api\v1\Handlers\Users;

use Remp\MailerModule\Repository\UserSubscriptionsRepository;
use Tomaj\NetteApi\Handlers\BaseHandler;
use Tomaj\NetteApi\Params\InputParam;
use Tomaj\NetteApi\Response\JsonApiResponse;
use Remp\MailerModule\Api\JsonValidationTrait;

class IsUserUnsubscribedHandler extends BaseHandler
{
    private $userSubscriptionsRepository;

    use JsonValidationTrait;

    public function __construct(
        UserSubscriptionsRepository $userSubscriptionsRepository
    ) {
        parent::__construct();
        $this->userSubscriptionsRepository = $userSubscriptionsRepository;
    }

    public function params()
    {
        return [
            new InputParam(InputParam::TYPE_POST_RAW, 'raw')
        ];
    }


    public function handle($params)
    {
        $payload = $this->validateInput($params['raw'], __DIR__ . '/is-user-unsubscribed.schema.json');

        if ($this->hasErrorResponse()) {
            return $this->getErrorResponse();
        }

        $output = $this->userSubscriptionsRepository->isUserUnsubscribed($payload['user_id'], $payload['list_id']);

        return new JsonApiResponse(200, $output);
    }
}
