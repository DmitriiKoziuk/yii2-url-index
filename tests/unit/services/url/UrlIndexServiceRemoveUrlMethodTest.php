<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\unit\services\url;

use Yii;
use yii\di\Container;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\MockObject;
use DmitriiKoziuk\yii2UrlIndex\tests\UnitTester;
use DmitriiKoziuk\yii2UrlIndex\repositories\UrlRepository;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\services\UrlIndexService;

class UrlIndexServiceRemoveUrlMethodTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    protected function _before()
    {
        Yii::$container = new Container();
    }

    /**
     * @param array $data
     * @dataProvider validUrlDataProvider
     */
    public function testWithValidData(array $data)
    {
        /** @var MockObject|UrlRepository $urlRepository */
        $urlRepository = $this->createMock(UrlRepository::class);
        $urlRepository->method('getByUrl')->willReturn(new UrlEntity($data));

        $service = new UrlIndexService(
            $urlRepository,
            null
        );

        $service->removeUrl($data['url']);
    }

    public function validUrlDataProvider(): array
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