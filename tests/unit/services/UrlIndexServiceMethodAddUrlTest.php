<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\unit\services;

use Yii;
use Codeception\Test\Unit;
use DmitriiKoziuk\yii2UrlIndex\tests\UnitTester;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\ModuleEntityFixture;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UrlEntityFixture;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlCreateForm;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlModuleEntity;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlIndexServiceInterface;
use DmitriiKoziuk\yii2UrlIndex\exceptions\UrlAlreadyHasBeenTakenException;
use DmitriiKoziuk\yii2UrlIndex\exceptions\forms\UrlCreateFormNotValidException;

class UrlIndexServiceMethodAddUrlTest extends Unit
{
    public UnitTester $tester;

    public function _fixtures()
    {
        return [
            'modules' => ModuleEntityFixture::class,
            'urls' => UrlEntityFixture::class,
        ];
    }

    public function testThrowExceptionIfFormNotValid()
    {
        /** @var UrlIndexServiceInterface $service */
        $service = Yii::$container->get(UrlIndexServiceInterface::class);
        $form = new UrlCreateForm();

        $this->assertFalse($form->validate());
        $this->assertInstanceOf(UrlIndexServiceInterface::class, $service);
        $this->expectException(UrlCreateFormNotValidException::class);
        $service->addUrl($form);
    }

    public function testThrowExceptionIfUrlAlreadyHasBeenTaken()
    {
        /** @var UrlModuleEntity $moduleEntityFixture */
        $moduleEntityFixture = $this->tester->grabFixture('modules', 'shopModule');
        /** @var UrlEntity $urlEntityFixture */
        $urlEntityFixture = $this->tester->grabFixture('urls', 'shopUrl1');
        /** @var UrlIndexServiceInterface $service */
        $service = Yii::$container->get(UrlIndexServiceInterface::class);
        $form = new UrlCreateForm([
            'url' => $urlEntityFixture->url,
            'entity_id' => $urlEntityFixture->entity_id,
            'module_name' => $moduleEntityFixture->module_name,
            'controller_name' => $moduleEntityFixture->controller_name,
            'action_name' => $moduleEntityFixture->action_name,
        ]);

        $this->assertTrue($form->validate());
        $this->assertInstanceOf(UrlIndexServiceInterface::class, $service);
        $this->tester->seeRecord(UrlEntity::class, ['url' => $urlEntityFixture->url]);
        $this->expectException(UrlAlreadyHasBeenTakenException::class);
        $service->addUrl($form);
    }

    public function testAddUrlWithValidData()
    {
        /** @var UrlModuleEntity $moduleEntityFixture */
        $moduleEntityFixture = $this->tester->grabFixture('modules', 'shopModule');
        /** @var UrlIndexServiceInterface $service */
        $service = Yii::$container->get(UrlIndexServiceInterface::class);
        $newUrl = '/some-new-product-url';
        $form = new UrlCreateForm([
            'url' => $newUrl,
            'entity_id' => 1,
            'module_name' => $moduleEntityFixture->module_name,
            'controller_name' => $moduleEntityFixture->controller_name,
            'action_name' => $moduleEntityFixture->action_name,
        ]);

        $this->assertTrue($form->validate());
        $this->assertInstanceOf(UrlIndexServiceInterface::class, $service);
        $this->tester->dontSeeRecord(UrlEntity::class, ['url' => $newUrl]);
        $createdUrlEntity = $service->addUrl($form);
        $this->assertInstanceOf(UrlEntity::class, $createdUrlEntity);
        $this->tester->seeRecord(UrlEntity::class, [
            'id' => $createdUrlEntity,
            'url' => $newUrl,
        ]);
    }
}
