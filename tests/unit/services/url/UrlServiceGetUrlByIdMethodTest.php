<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\unit\services\url;

use Yii;
use yii\di\Container;
use Codeception\Test\Unit;
use DmitriiKoziuk\yii2UrlIndex\tests\UnitTester;
use DmitriiKoziuk\yii2UrlIndex\tests\_stubs\UrlRepositoryStub;
use DmitriiKoziuk\yii2UrlIndex\services\UrlService;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;

class UrlServiceGetUrlByIdMethodTest extends Unit
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
     * @param $data
     * @dataProvider validUrlData
     */
    public function testWithValidData($data): void
    {
        $service = new UrlService(
            new UrlRepositoryStub($data),
            null
        );
        $urlUpdateForm = $service->getUrlById($data['id']);
        $this->assertInstanceOf(UrlUpdateForm::class, $urlUpdateForm);
        $this->assertEquals($data, $urlUpdateForm->getAttributes());
    }

    /**
     * @param $data
     * @dataProvider validUrlData
     */
    public function testWithNotValidData($data): void
    {
        $service = new UrlService(
            new UrlRepositoryStub(),
            null
        );
        $this->assertEquals(null, $service->getUrlById($data['id']));
    }

    public function validUrlData(): array
    {
        $fixtures = include codecept_data_dir() . 'url_data.php';
        $fixtures = array_map(function ($array) {
            $list = [];
            array_push($list, $array);
            return $list;
        }, $fixtures);
        return $fixtures;
    }
}