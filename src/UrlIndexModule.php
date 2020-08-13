<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex;

use InvalidArgumentException;
use yii\di\Container;
use yii\db\Connection;
use yii\base\Module;
use yii\web\Application as WebApp;
use yii\base\Application as BaseApp;
use yii\console\Application as ConsoleApp;
use DmitriiKoziuk\yii2ModuleManager\interfaces\ModuleInterface;
use DmitriiKoziuk\yii2ModuleManager\ModuleManager;
use DmitriiKoziuk\yii2UrlIndex\services\UrlIndexService;
use DmitriiKoziuk\yii2UrlIndex\services\UrlIndexUpdateService;
use DmitriiKoziuk\yii2UrlIndex\repositories\UrlRepository;
use DmitriiKoziuk\yii2UrlIndex\repositories\UrlModuleRepository;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlModuleRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlIndexServiceInterface;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlRepositoryInterface;

class UrlIndexModule extends Module implements ModuleInterface
{
    const ID = 'dk-url-index';

    const TRANSLATE = self::ID;

    public Container $diContainer;
    public Connection $dbConnection;

    /**
     * Overwrite this param if you backend app id is different from default.
     */
    public string $backendAppId;

    /**
     * Overwrite this param if you backend app id is different from default.
     */
    public string $frontendAppId;

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
        return [
            ModuleManager::class,
        ];
    }

    public function init(): void
    {
        /** @var BaseApp $app */
        $app = $this->module;
        $this->initLocalProperties($app);
        $this->registerTranslation($app);
        $this->registerClassesToDIContainer();
        $this->registerUrlRules($app);
    }

    private function initLocalProperties(BaseApp $app)
    {
        if (empty($this->backendAppId)) {
            throw new InvalidArgumentException('Property backendAppId not set.');
        }
        if (empty($this->frontendAppId)) {
            throw new InvalidArgumentException('Property frontendAppId not set.');
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
            array_push(
                $app->controllerMap['migrate']['migrationNamespaces'],
                __NAMESPACE__ . '\migrations'
            );
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

    private function registerClassesToDIContainer(): void
    {
        $this->diContainer->setSingleton(UrlRepositoryInterface::class, UrlRepository::class);
        $this->diContainer->setSingleton(UrlModuleRepositoryInterface::class, UrlModuleRepository::class);
        $this->diContainer->setSingleton(UrlIndexServiceInterface::class, UrlIndexService::class);
        $this->diContainer->setSingleton(UrlIndexUpdateService::class);
    }

    private function registerUrlRules(BaseApp $app): void
    {
        if ($app instanceof WebApp && $app->id == $this->frontendAppId) {
            $app->getUrlManager()->addRules([
                [
                    'class' => __NAMESPACE__ . '\components\UrlRule',
                ],
            ]);
        }
    }
}
