<?php
declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex;

use Yii;
use yii\base\BootstrapInterface;
use DmitriiKoziuk\yii2ConfigManager\ConfigManagerModule;
use DmitriiKoziuk\yii2ConfigManager\services\ConfigService;
use DmitriiKoziuk\yii2ModuleManager\services\ModuleRegistrationService;

class Bootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function bootstrap($app)
    {
        ModuleRegistrationService::addModule(UrlIndexModule::class, function () use ($app) {
            /** @var ConfigService $configService */
            $configService = Yii::$container->get(ConfigService::class);
            return [
                'class' => UrlIndexModule::class,
                'diContainer' => Yii::$container,
                'dbConnection' => $app->db,
                'backendAppId' => $configService->getValue(
                    ConfigManagerModule::GENERAL_CONFIG_NAME,
                    'backendAppId'
                ),
                'frontendAppId' => $configService->getValue(
                    ConfigManagerModule::GENERAL_CONFIG_NAME,
                    'frontendAppId'
                ),
            ];
        });
    }
}