<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\repositories;

use DmitriiKoziuk\yii2Base\repositories\AbstractActiveRecordRepository;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlModuleRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\UrlIndexModule;
use DmitriiKoziuk\yii2UrlIndex\entities\ModuleEntity;

class UrlModuleRepository extends AbstractActiveRecordRepository implements UrlModuleRepositoryInterface
{
    public function getModule(
        string $moduleName,
        string $controllerName,
        string $actionName
    ): ?ModuleEntity
    {
        /** @var ModuleEntity|null $moduleEntity */
        $moduleEntity = ModuleEntity::find()
            ->where([
                'module_name' => $moduleName,
                'controller_name' => $controllerName,
                'action_name' => $actionName,
            ])->limit(1)->one();
        return $moduleEntity;
    }

    public function getRedirectModule(): ModuleEntity
    {
        /** @var ModuleEntity|null $moduleEntity */
        $moduleEntity = ModuleEntity::find()
            ->where([
                'module_name' => UrlIndexModule::getId(),
                'controller_name' => 'url',
                'action_name' => 'redirect',
            ])->limit(1)->one();
        return $moduleEntity;
    }
}
