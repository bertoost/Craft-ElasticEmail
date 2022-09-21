<?php

namespace bertoost\ElasticEmail\Craft\adapters;

use bertoost\ElasticEmail\SwiftMailer\ElasticEmailTransport;
use Craft;
use craft\behaviors\EnvAttributeParserBehavior;
use craft\helpers\App;
use craft\mail\transportadapters\BaseTransportAdapter;
use ElasticEmail\Api\EmailsApi;
use ElasticEmail\Configuration;
use GuzzleHttp\Client;
use Swift_Events_SimpleEventDispatcher;

class ElasticEmailAdapter extends BaseTransportAdapter
{
    /**
     * @var string
     */
    public $apiKey;

    public static function displayName(): string
    {
        return 'Elastic Email';
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['parser'] = [
            'class' => EnvAttributeParserBehavior::class,
            'attributes' => [
                'apiKey',
            ],
        ];

        return $behaviors;
    }

    public function attributeLabels(): array
    {
        return [
            'apiKey' => Craft::t('elastic-email', 'API Key'),
        ];
    }

    public function rules(): array
    {
        return [
            [['apiKey'], 'required'],
        ];
    }

    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('elastic-email/settings', [
            'adapter' => $this,
        ]);
    }

    public function defineTransport()
    {
        // Configure API key authorization: apikey
        $config = Configuration::getDefaultConfiguration()
            ->setApiKey('X-ElasticEmail-ApiKey', App::parseEnv($this->apiKey));

        $apiInstance = new EmailsApi(new Client(), $config);

        return [
            'class' => ElasticEmailTransport::class,
            'constructArgs' => [
                [
                    'class' => Swift_Events_SimpleEventDispatcher::class,
                ],
                $apiInstance,
            ],
        ];
    }
}