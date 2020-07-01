<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\interfaces;

use DmitriiKoziuk\yii2Base\interfaces\ActiveRecordRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\entities\ModuleEntity;

interface UrlModuleRepositoryInterface extends ActiveRecordRepositoryInterface
{
    public function getModule(string $moduleName, string $controllerName, string $actionName): ?ModuleEntity;
    public function getRedirectModule(): ModuleEntity;
}
