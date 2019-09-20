<?php
declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex;

use yii\di\Container;
use yii\db\Connection;
use yii\base\Module;
use yii\web\Application as WebApp;
use yii\base\Application as BaseApp;
use yii\console\Application as ConsoleApp;
use DmitriiKoziuk\yii2ModuleManager\interfaces\ModuleInterface;
use DmitriiKoziuk\yii2UrlIndex\repositories\UrlRepository;
use DmitriiKoziuk\yii2UrlIndex\services\UrlService;

class UrlIndexModule extends Module implements ModuleInterface
{
    const ID = 'dk-url-index';

    const TRANSLATE = self::ID;

    /**
     * @var Container
     */
    public $diContainer;

    /**
     * @var Connection
     */
    public $dbConnection;

    /**
     * Overwrite this param if you backend app id is different from default.
     * @var string
     */
    public $backendAppId;

    /**
     * Overwrite this param if you backend app id is different from default.
     * @var string
     */
    public $frontendAppId;

    public static function getId(): string
    {
        return self::ID;
    }

    public function getBackendMenuItems(): array
    {
        return ['label' => 'Url index', 'url' => ['/' . self::ID . '/url/index']];
    }

    public static function requireOtherModulesToBeActive(): array
    {
        return [];
    }

    public function init(): void
    {
        /** @var BaseApp $app */
        $app = $this->module;
        $this->initLocalProperties($app);
        $this->registerTranslation($app);
        $this->registerClassesToDIContainer($app);
    }

    private function initLocalProperties(BaseApp $app)
    {
        if (empty($this->backendAppId)) {
            throw new \InvalidArgumentException('Property backendAppId not set.');
        }
        if (empty($this->frontendAppId)) {
            throw new \InvalidArgumentException('Property frontendAppId not set.');
        }
        if ($app instanceof WebApp && $app->id == $this->backendAppId) {
            $this->controllerNamespace = __NAMESPACE__ . '\controllers\backend';
            $this->viewPath = '@DmitriiKoziuk/yii2UrlIndex/views/backend';
        }
        if ($app instanceof WebApp && $app->id == $this->frontendAppId) {
            $this->controllerNamespace = __NAMESPACE__ . '\controllers\frontend';
            $this->viewPath = '@DmitriiKoziuk/yii2UrlIndex/views/frontend';
        }
        if ($app instanceof ConsoleApp) {
            $app->controllerMap['migrate']['migrationNamespaces'][] = __NAMESPACE__ . '\migrations';
        }
    }

    private function registerTranslation(BaseApp $app): void
    {
        $app->i18n->translations[self::TRANSLATE] = [
            'class'          => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en',
            'basePath'       => '@DmitriiKoziuk/yii2UrlIndex/messages',
        ];
    }

    /**
     * @param BaseApp $app
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    private function registerClassesToDIContainer(BaseApp $app): void
    {
        $this->diContainer->setSingleton(
            UrlRepository::class,
            function () {
                return new UrlRepository();
            }
        );
        /** @var UrlRepository $urlRepository */
        $urlRepository = $this->diContainer->get(UrlRepository::class);

        $this->diContainer->setSingleton(UrlService::class, function () use (
            $urlRepository
        ) {
            return new UrlService(
                $urlRepository,
                $this->dbConnection
            );
        });
    }
}