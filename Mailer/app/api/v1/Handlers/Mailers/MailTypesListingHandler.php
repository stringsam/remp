<?php

namespace Remp\MailerModule\Api\v1\Handlers\Mailers;

use Nette\Utils\Arrays;
use Remp\MailerModule\Api\JsonValidationTrait;
use Remp\MailerModule\Repository\ListsRepository;
use Tomaj\NetteApi\Handlers\BaseHandler;
use Tomaj\NetteApi\Params\InputParam;
use Tomaj\NetteApi\Response\JsonApiResponse;

class MailTypesListingHandler extends BaseHandler
{
    private $listsRepository;
    use JsonValidationTrait;

    public function __construct(
        ListsRepository $listsRepository
    ) {
        parent::__construct();
        $this->listsRepository = $listsRepository;
    }

    public function params()
    {
        return [
            new InputParam(InputParam::TYPE_POST_RAW, 'raw')
        ];
    }


    public function handle($params)
    {
        $request = file_get_contents("php://input");
        $payload = $this->validateInput($params['raw'], __DIR__ . '/mail-types.schema.json');

        if ($this->hasErrorResponse()) {
            return $this->getErrorResponse();
        }

        $results = $this->listsRepository->all();

        if (isset($payload['public_listing'])) {
            $results->where(['public_listing' => $payload['public_listing']]);
        }

        $output = Arrays::map($results->fetchAll(), function ($row) {
            return [
                'id' => $row->id,
                'code' => $row->code,
                'mail_type_category_id' => $row->mail_type_category_id,
                'image_url' => $row->image_url,
                'preview_url' => $row->preview_url,
                'title' => $row->title,
                'description' => $row->description,
                'locked' => $row->locked,
                'is_multi_variant' => $row->is_multi_variant,
                'variants' => $row->related('mail_type_variants.mail_type_id')->order('sorting')->fetchPairs('id', 'title'),
            ];
        });

        return new JsonApiResponse(200, $output);
    }
}
