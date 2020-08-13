<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\repositories;

use DmitriiKoziuk\yii2Base\repositories\AbstractActiveRecordRepository;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlModuleRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\UrlIndexModule;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlModuleEntity;

class UrlModuleRepository extends AbstractActiveRecordRepository implements UrlModuleRepositoryInterface
{
    public function getModule(
        string $moduleName,
        string $controllerName,
        string $actionName
    ): ?UrlModuleEntity {
        /** @var UrlModuleEntity|null $moduleEntity */
        $moduleEntity = UrlModuleEntity::find()
            ->where([
                'module_name' => $moduleName,
                'controller_name' => $controllerName,
                'action_name' => $actionName,
            ])->limit(1)->one();
        return $moduleEntity;
    }

    public function getRedirectModule(): UrlModuleEntity
    {
        /** @var UrlModuleEntity|null $moduleEntity */
        $moduleEntity = UrlModuleEntity::find()
            ->where([
                'module_name' => UrlIndexModule::getId(),
                'controller_name' => 'url',
                'action_name' => 'redirect',
            ])->limit(1)->one();
        return $moduleEntity;
    }
}
