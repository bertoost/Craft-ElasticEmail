<?php

namespace bertoost\ElasticEmail\Craft;

use bertoost\ElasticEmail\Craft\adapters\ElasticEmailAdapter;
use Craft;
use craft\base\Plugin as BasePlugin;
use craft\events\RegisterComponentTypesEvent;
use craft\helpers\MailerHelper;
use craft\i18n\PhpMessageSource;
use yii\base\Event;

class Plugin extends BasePlugin
{
    public function init()
    {
        Craft::setAlias('@bertoost\elastic-email', $this->getBasePath());

        parent::init();

        $this->registerEvents();
        $this->registerTranslations();
    }

    /**
     * Register event listeners
     */
    public function registerEvents(): void
    {
        $eventName = defined(sprintf('%s::EVENT_REGISTER_MAILER_TRANSPORT_TYPES', MailerHelper::class))
            ? MailerHelper::EVENT_REGISTER_MAILER_TRANSPORT_TYPES // Craft 4
            /** @phpstan-ignore-next-line */
            : MailerHelper::EVENT_REGISTER_MAILER_TRANSPORTS; // Craft 5+

        Event::on(
            MailerHelper::class,
            $eventName,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = ElasticEmailAdapter::class;
            }
        );
    }

    /**
     * Registers translation definition
     */
    private function registerTranslations()
    {
        Craft::$app->i18n->translations['elastic-email*'] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en',
            'basePath' => $this->getBasePath().'/translations',
            'allowOverrides' => true,
            'fileMap' => [
                'elastic-email' => 'site',
                'elastic-email-app' => 'app',
            ],
        ];
    }
}