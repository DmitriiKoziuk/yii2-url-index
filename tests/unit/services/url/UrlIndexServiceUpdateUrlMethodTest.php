<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\unit\services\url;

use Yii;
use yii\di\Container;
use Codeception\Test\Unit;
use DmitriiKoziuk\yii2Base\exceptions\InvalidFormException;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2UrlIndex\tests\UnitTester;
use DmitriiKoziuk\yii2UrlIndex\tests\_stubs\UrlRepositoryStub;
use DmitriiKoziuk\yii2UrlIndex\services\UrlIndexService;

class UrlIndexServiceUpdateUrlMethodTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
        Yii::$container = new Container();
    }

    protected function _after()
    {
    }

    /**
     * @param array $data
     * @throws \Throwable
     * @dataProvider validDataProvider
     */
    public function testUpdateUrlMethodNotValidUrlUpdateForm(array $data)
    {
        $service = new UrlIndexService(
            new UrlRepositoryStub($data),
            null
        );

        $updateForm = new UrlUpdateForm();
        $this->expectException(InvalidFormException::class);
        $service->updateUrl($updateForm);
    }

    /**
     * @param array $data
     * @throws \Throwable
     * @dataProvider validDataProvider
     */
    public function testUpdateUrlMethodNotFoundUpdatedEntity(array $data)
    {
        $service = new UrlIndexService(
            new UrlRepositoryStub(),
            null
        );

        $updateForm = new UrlUpdateForm($data);
        $this->assertTrue($updateForm->validate());
        $this->expectException(EntityNotFoundException::class);
        $service->updateUrl($updateForm);
    }

    /**
     * @param array $data
     * @throws \Throwable
     * @dataProvider validDataProvider
     */
    public function testUpdateUrlMethodWorkFine(array $data)
    {
        $service = new UrlIndexService(
            new UrlRepositoryStub($data),
            null
        );

        $updateForm = new UrlUpdateForm($data);
        $this->assertTrue($updateForm->validate());
        $returnForm = $service->updateUrl($updateForm);
        $this->assertInstanceOf(UrlUpdateForm::class, $returnForm);
        $this->assertEquals($data, $returnForm->getAttributes());
    }

    public function validDataProvider()
    {
        $fixtures = include codecept_data_dir() . 'url_data.php';
        $fixtures = array_map(function ($array) {
            $list = [];
            array_push($list, $array);
            return $list;
        }, $fixtures);
        return $fixtures;
    }

    public function validReversDataProvider()
    {
        $reversData = $this->validDataProvider();
        rsort($reversData);
        return $reversData;
    }
}