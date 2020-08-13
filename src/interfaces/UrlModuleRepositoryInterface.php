<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\interfaces;

use DmitriiKoziuk\yii2Base\interfaces\ActiveRecordRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlModuleEntity;

interface UrlModuleRepositoryInterface extends ActiveRecordRepositoryInterface
{
    public function getModule(string $moduleName, string $controllerName, string $actionName): ?UrlModuleEntity;
    public function getRedirectModule(): UrlModuleEntity;
}
