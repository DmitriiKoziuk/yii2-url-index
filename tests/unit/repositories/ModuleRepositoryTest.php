<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\unit\repositories;

use Yii;
use yii\di\Container;
use Codeception\Test\Unit;
use DmitriiKoziuk\yii2UrlIndex\tests\UnitTester;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\ModuleEntityFixture;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UrlEntityFixture;
use DmitriiKoziuk\yii2UrlIndex\entities\ModuleEntity;
use DmitriiKoziuk\yii2UrlIndex\repositories\UrlModuleRepository;

class ModuleRepositoryTest extends Unit
{
    public UnitTester $tester;

    public function _fixtures()
    {
        return [
            'modules' => ModuleEntityFixture::class,
            'urls' => UrlEntityFixture::class,
        ];
    }

    public function _before()
    {
        Yii::$container = new Container();
    }

    public function testMethodGetModuleReturnNull()
    {
        /** @var UrlModuleRepository $moduleRepository */
        $moduleRepository = Yii::$container->get(UrlModuleRepository::class);
        $moduleName = 'notExistModule';
        $controllerName = 'notExistModule';
        $actionName = 'notExistModule';

        $this->tester->dontSeeRecord(ModuleEntity::class, [
            'module_name' => $moduleName,
            'controller_name' => $controllerName,
            'action_name' => $actionName,
        ]);
        /** @var ModuleEntity $moduleEntity */
        $moduleEntity = $moduleRepository->getModule(
            $moduleName,
            $controllerName,
            $actionName
        );
        $this->assertEmpty($moduleEntity);
    }

    /**
     * @param string $fixtureIndex
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     * @dataProvider moduleEntityExistFixtureNameDataProvider
     */
    public function testMethodGetModuleReturnExistModule(string $fixtureIndex)
    {
        /** @var ModuleEntity $fixtureModuleEntity */
        $fixtureModuleEntity = $this->tester->grabFixture('modules', $fixtureIndex);
        /** @var UrlModuleRepository $moduleRepository */
        $moduleRepository = Yii::$container->get(UrlModuleRepository::class);

        $this->tester->seeRecord(ModuleEntity::class, [
            'module_name' => $fixtureModuleEntity->module_name,
            'controller_name' => $fixtureModuleEntity->controller_name,
            'action_name' => $fixtureModuleEntity->action_name,
        ]);
        /** @var ModuleEntity $moduleEntity */
        $moduleEntity = $moduleRepository->getModule(
            $fixtureModuleEntity->module_name,
            $fixtureModuleEntity->controller_name,
            $fixtureModuleEntity->action_name
        );
        $this->assertInstanceOf(ModuleEntity::class, $moduleEntity);
        $this->assertEquals($fixtureModuleEntity->id, $moduleEntity->id);
    }

    public function moduleEntityExistFixtureNameDataProvider()
    {
        return [
            'appModule' => ['appModule'],
            'shopModule' => ['shopModule'],
        ];
    }
}
