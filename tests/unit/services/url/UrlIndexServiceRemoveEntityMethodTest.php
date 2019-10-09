<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests;

use Yii;
use yii\di\Container;
use DmitriiKoziuk\yii2Base\exceptions\DataNotValidException;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UrlsFixture;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\forms\RemoveEntityForm;
use DmitriiKoziuk\yii2UrlIndex\exceptions\RemoveEntityFormNotValidException;
use DmitriiKoziuk\yii2UrlIndex\exceptions\EntityUrlNotFoundException;
use DmitriiKoziuk\yii2UrlIndex\services\UrlIndexService;

class UrlIndexServiceRemoveEntityMethodTest extends \Codeception\Test\Unit
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
     * @throws RemoveEntityFormNotValidException
     * @throws DataNotValidException|RemoveEntityFormNotValidException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     * @throws EntityUrlNotFoundException
     */
    public function testRemoveEntityFormNotValid()
    {
        $form = new RemoveEntityForm();
        /** @var UrlIndexService $service */
        $service = Yii::$container->get(UrlIndexService::class);
        $this->expectException(RemoveEntityFormNotValidException::class);
        $service->removeEntityUrl($form);
    }

    /**
     * @throws DataNotValidException
     * @throws EntityUrlNotFoundException
     * @throws RemoveEntityFormNotValidException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function testTryRemoveEntityWithoutUrl()
    {
        $removeEntityAttributes = [
            'module_name' => 'module_zzz',
            'controller_name' => 'controller_zzz',
            'action_name' => 'action_zzz',
            'entity_id' => '1',
        ];
        $this->tester->dontSeeRecord(UrlEntity::class, $removeEntityAttributes);
        $form = new RemoveEntityForm($removeEntityAttributes);
        $this->assertTrue($form->validate());
        $this->expectException(EntityUrlNotFoundException::class);
        /** @var UrlIndexService $service */
        $service = Yii::$container->get(UrlIndexService::class);
        $service->removeEntityUrl($form);
    }

    /**
     * @throws DataNotValidException
     * @throws RemoveEntityFormNotValidException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     * @throws EntityUrlNotFoundException
     */
    public function testExecuteSuccessful()
    {
        $removeEntityAttributes = [
            'module_name' => 'module',
            'controller_name' => 'controller',
            'action_name' => 'action',
            'entity_id' => '1',
        ];
        $this->tester->seeRecord(UrlEntity::class, $removeEntityAttributes);

        $form = new RemoveEntityForm($removeEntityAttributes);
        $this->assertTrue($form->validate());
        /** @var UrlIndexService $service */
        $service = Yii::$container->get(UrlIndexService::class);
        $service->removeEntityUrl($form);

        $this->tester->dontSeeRecord(UrlEntity::class, $removeEntityAttributes);
    }
}