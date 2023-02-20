<?php

namespace bertoost\ElasticEmail\Craft\adapters;

use bertoost\Mailer\ElasticEmail\Transport\ElasticEmailApiTransport;
use Craft;
use craft\behaviors\EnvAttributeParserBehavior;
use craft\helpers\App;
use craft\mail\transportadapters\BaseTransportAdapter;
use Symfony\Component\Mailer\Transport\AbstractTransport;

class ElasticEmailAdapter extends BaseTransportAdapter
{
    public string $apiKey = '';

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
        return Craft::$app->getView()
            ->renderTemplate('elastic-email/settings', [
                'adapter' => $this,
            ]);
    }

    public function defineTransport(): array|AbstractTransport
    {
        return new ElasticEmailApiTransport(App::parseEnv($this->apiKey));
    }
}