<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\unit\services\url;

use Yii;
use yii\di\Container;
use Codeception\Test\Unit;
use DmitriiKoziuk\yii2UrlIndex\tests\UnitTester;
use DmitriiKoziuk\yii2UrlIndex\tests\_stubs\UrlRepositoryStub;
use DmitriiKoziuk\yii2UrlIndex\services\UrlIndexService;

class UrlIndexServiceIsUrlExistMethodTest extends Unit
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
     * @dataProvider validDataProvider
     */
    public function testUrlExist(array $data): void
    {
        $service = new UrlIndexService(
            new UrlRepositoryStub($data),
            null
        );
        $this->assertTrue($service->isUrlExist($data['url']));
    }

    /**
     * @param array $data
     * @dataProvider validDataProvider
     */
    public function testUrlNotExist(array $data): void
    {
        $service = new UrlIndexService(
            new UrlRepositoryStub(),
            null
        );
        $this->assertFalse($service->isUrlExist($data['url']));
    }

    public function validDataProvider(): array
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
