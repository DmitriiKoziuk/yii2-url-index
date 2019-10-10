<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests;

use Yii;
use yii\di\Container;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UrlsFixture;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\forms\UpdateEntityUrlForm;
use DmitriiKoziuk\yii2UrlIndex\exceptions\EntityUrlNotFoundException;
use DmitriiKoziuk\yii2UrlIndex\exceptions\UpdateEntityUrlFormNotValidException;
use DmitriiKoziuk\yii2UrlIndex\exceptions\UrlAlreadyHasBeenTakenException;
use DmitriiKoziuk\yii2UrlIndex\services\UrlIndexService;

class UrlIndexServiceUpdateEntityUrlMethodTest extends \Codeception\Test\Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
        return [
            'urls' => [
                'class' => UrlsFixture::class,
                'dataFile' => codecept_data_dir() . 'url_data.php'
            ]
        ];
    }

    protected function _after()
    {
        Yii::$container = new Container();
    }

    /**
     * @throws EntityUrlNotFoundException
     * @throws UrlAlreadyHasBeenTakenException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function testEntityUrlFormNotValid()
    {
        $form = new UpdateEntityUrlForm();
        /** @var UrlIndexService $service */
        $service = Yii::$container->get(UrlIndexService::class);
        $this->expectException(UpdateEntityUrlFormNotValidException::class);
        $service->updateEntityUrl($form);
    }

    /**
     * @throws EntityUrlNotFoundException
     * @throws UrlAlreadyHasBeenTakenException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function testUpdateNonExistUrl()
    {
        /** @var UrlIndexService $service */
        $service = Yii::$container->get(UrlIndexService::class);
        $updateEntityAttributes = [
            'url' => '/some-non-exist-url',
            'module_name' => 'module_zzz',
            'controller_name' => 'controller_zzz',
            'action_name' => 'action_zzz',
            'entity_id' => '1',
        ];
        $this->tester->dontSeeRecord(UrlEntity::class, $updateEntityAttributes);
        $form = new UpdateEntityUrlForm($updateEntityAttributes);
        $this->assertTrue($form->validate());
        $this->expectException(EntityUrlNotFoundException::class);
        $service->updateEntityUrl($form);
    }

    public function testUpdateRedirectUrl()
    {
        $redirectUrlAttributes = [
            'url' => '/some-url-3.html',
            'redirect_to_url' => 1,
            'module_name' => 'dk-url-index',
            'controller_name' => 'url',
            'action_name' => 'redirect',
        ];
        $updateAttributes = [
            'url' => '/some-url-3.html',
            'module_name' => 'module',
            'controller_name' => 'controller',
            'action_name' => 'action',
            'entity_id' => '2',
        ];
        $this->tester->seeRecord(UrlEntity::class, $redirectUrlAttributes);
        $this->tester->dontSeeRecord(UrlEntity::class, $updateAttributes);

        /** @var UrlIndexService $service */
        $service = Yii::$container->get(UrlIndexService::class);
        $form = new UpdateEntityUrlForm($updateAttributes);
        $this->assertTrue($form->validate());
        $service->updateEntityUrl($form);

        $this->tester->dontSeeRecord(UrlEntity::class, $redirectUrlAttributes);
        $this->tester->seeRecord(UrlEntity::class, $updateAttributes);
    }

    /**
     * @throws EntityUrlNotFoundException
     * @throws UrlAlreadyHasBeenTakenException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function testUpdateOccupiedUrl()
    {
        /** @var UrlIndexService $service */
        $service = Yii::$container->get(UrlIndexService::class);
        $this->tester->seeRecord(UrlEntity::class, [
            'url' => '/some-url.html',
            'module_name' => 'module',
            'controller_name' => 'controller',
            'action_name' => 'action',
            'entity_id' => '1',
        ]);
        $updateEntityAttributes = [
            'url' => '/some-url.html',
            'module_name' => 'module',
            'controller_name' => 'controller',
            'action_name' => 'action',
            'entity_id' => '2',
        ];
        $form = new UpdateEntityUrlForm($updateEntityAttributes);
        $this->assertTrue($form->validate());
        $this->expectException(UrlAlreadyHasBeenTakenException::class);
        $service->updateEntityUrl($form);
    }

    /**
     * @throws EntityUrlNotFoundException
     * @throws UrlAlreadyHasBeenTakenException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function testOverwriteOwnUrlSameData()
    {
        $updatedEntityAttributes = [
            'url' => '/some-url.html',
            'module_name' => 'module',
            'controller_name' => 'controller',
            'action_name' => 'action',
            'entity_id' => '1',
        ];
        $this->tester->seeRecord(UrlEntity::class, $updatedEntityAttributes);

        $form = new UpdateEntityUrlForm($updatedEntityAttributes);
        $this->assertTrue($form->validate());
        /** @var UrlIndexService $service */
        $service = Yii::$container->get(UrlIndexService::class);
        $service->updateEntityUrl($form);

        $this->tester->seeRecord(UrlEntity::class, $updatedEntityAttributes);
    }

    /**
     * @throws EntityUrlNotFoundException
     * @throws UrlAlreadyHasBeenTakenException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function testUpdateUrlSuccessful()
    {
        $oldUrlAttributes = [
            'id' => 2,
            'url' => '/some-url-2.html',
            'module_name' => 'module',
            'controller_name' => 'controller',
            'action_name' => 'action',
            'entity_id' => '2',
        ];
        $updatedEntityAttributes = [
            'url' => '/new-url',
            'module_name' => 'module',
            'controller_name' => 'controller',
            'action_name' => 'action',
            'entity_id' => '2',
        ];
        $this->tester->seeRecord(UrlEntity::class, $oldUrlAttributes);
        $this->tester->dontSeeRecord(UrlEntity::class, $updatedEntityAttributes);

        $form = new UpdateEntityUrlForm($updatedEntityAttributes);
        $this->assertTrue($form->validate());
        /** @var UrlIndexService $service */
        $service = Yii::$container->get(UrlIndexService::class);
        $service->updateEntityUrl($form);

        $this->tester->dontSeeRecord(UrlEntity::class, $oldUrlAttributes);
        $this->tester->seeRecord(UrlEntity::class, $updatedEntityAttributes);
        $this->tester->seeRecord(UrlEntity::class, [
            'url' => '/some-url-2.html',
            'redirect_to_url' => 2,
        ]);
    }
}