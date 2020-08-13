<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\unit\services;

use Yii;
use Codeception\Test\Unit;
use DmitriiKoziuk\yii2UrlIndex\tests\UnitTester;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\ModuleEntityFixture;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UrlEntityFixture;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlModuleEntity;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlIndexServiceInterface;
use DmitriiKoziuk\yii2UrlIndex\exceptions\forms\UrlUpdateFormNotValidException;
use DmitriiKoziuk\yii2UrlIndex\exceptions\UrlNotFoundException;

class UrlIndexServiceMethodUpdateUrlTest extends Unit
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
        $form = new UrlUpdateForm([]);

        $this->assertFalse($form->validate());
        $this->expectException(UrlUpdateFormNotValidException::class);
        $service->updateUrl($form);
    }

    public function testThrowExceptionIfTryUpdateNotExistUrl()
    {
        /** @var UrlIndexServiceInterface $service */
        $service = Yii::$container->get(UrlIndexServiceInterface::class);
        /** @var UrlModuleEntity $moduleEntityFixture */
        $moduleEntityFixture = $this->tester->grabFixture('modules', 'shopModule');
        /** @var UrlEntity $urlEntityFixture */
        $urlEntityFixture = $this->tester->grabFixture('urls', 'shopUrl1');
        $updatedUrlId = 9999;
        $form = new UrlUpdateForm([
            'id' => $updatedUrlId,
            'url' => '/some-not-exist-url',
            'entity_id' => $urlEntityFixture->entity_id,
            'module_name' => $moduleEntityFixture->module_name,
            'controller_name' => $moduleEntityFixture->controller_name,
            'action_name' => $moduleEntityFixture->action_name,
        ]);

        $this->assertTrue($form->validate());
        $this->tester->dontSeeRecord(UrlEntity::class, ['id' => $updatedUrlId]);
        $this->expectException(UrlNotFoundException::class);
        $service->updateUrl($form);
    }

    public function testSuccessfulUrlUpdate()
    {
        /** @var UrlIndexServiceInterface $service */
        $service = Yii::$container->get(UrlIndexServiceInterface::class);
        /** @var UrlModuleEntity $moduleEntityFixture */
        $moduleEntityFixture = $this->tester->grabFixture('modules', 'shopModule');
        /** @var UrlEntity $urlEntityFixture */
        $urlEntityFixture = $this->tester->grabFixture('urls', 'shopUrl1');
        $oldUrl = $urlEntityFixture->url;
        $newUrl = '/some-new-url';
        $form = new UrlUpdateForm([
            'id' => $urlEntityFixture->id,
            'url' => $newUrl,
            'entity_id' => $urlEntityFixture->entity_id,
            'module_name' => $moduleEntityFixture->module_name,
            'controller_name' => $moduleEntityFixture->controller_name,
            'action_name' => $moduleEntityFixture->action_name,
        ]);

        $this->assertTrue($form->validate());
        $this->tester->seeRecord(UrlEntity::class, [
            'id' => $urlEntityFixture->id,
            'url' => $oldUrl,
        ]);
        $this->tester->dontSeeRecord(UrlEntity::class, ['url' => $newUrl]);
        $service->updateUrl($form);
        $this->tester->seeRecord(UrlEntity::class, [
            'id' => $urlEntityFixture->id,
            'url' => $newUrl,
        ]);
        $this->tester->seeRecord(UrlEntity::class, [
            'url' => $oldUrl,
            'redirect_to_url' => $urlEntityFixture->id,
        ]);
    }

    public function testChangeUrlToRedirect()
    {
        /** @var UrlIndexServiceInterface $service */
        $service = Yii::$container->get(UrlIndexServiceInterface::class);
        /** @var UrlModuleEntity $redirectModuleEntityFixture */
        $redirectModuleEntityFixture = $this->tester->grabFixture('modules', 'redirectModule');
        /** @var UrlModuleEntity $shopModuleEntityFixture */
        $shopModuleEntityFixture = $this->tester->grabFixture('modules', 'shopModule');
        /** @var UrlEntity $updatedUrlEntityFixture */
        $updatedUrlEntityFixture = $this->tester->grabFixture('urls', 'shopUrl1');
        /** @var UrlEntity $destinationUrlEntityFixture */
        $destinationUrlEntityFixture = $this->tester->grabFixture('urls', 'shopUrl2');
        $form = new UrlUpdateForm([
            'id' => $updatedUrlEntityFixture->id,
            'url' => $updatedUrlEntityFixture->url,
            'entity_id' => $updatedUrlEntityFixture->entity_id,
            'module_name' => $shopModuleEntityFixture->module_name,
            'controller_name' => $shopModuleEntityFixture->controller_name,
            'action_name' => $shopModuleEntityFixture->action_name,
            'redirect_to_url' => $destinationUrlEntityFixture->id
        ]);

        $this->assertTrue($form->validate());
        $this->tester->seeRecord(UrlEntity::class, [
            'id' => $updatedUrlEntityFixture->id,
            'url' => $updatedUrlEntityFixture->url,
        ]);
        $service->updateUrl($form);
        $this->tester->seeRecord(UrlEntity::class, [
            'id' => $updatedUrlEntityFixture->id,
            'url' => $updatedUrlEntityFixture->url,
            'redirect_to_url' => $destinationUrlEntityFixture->id,
            'module_id' => $redirectModuleEntityFixture->id,
        ]);
    }
}
