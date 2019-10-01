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
use DmitriiKoziuk\yii2UrlIndex\exceptions\UrlNotFoundException;

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
     * @throws \Exception
     * @dataProvider validUrlDataProvider
     */
    public function testDeleteNonExistUrl(array $data)
    {
        /** @var MockObject|UrlRepository $urlRepository */
        $urlRepository = $this->createMock(UrlRepository::class);
        $urlRepository->method('getByUrl')->willReturn(null);

        $service = new UrlIndexService(
            $urlRepository,
            null
        );

        $this->expectException(UrlNotFoundException::class);
        $service->removeUrl($data['url']);
    }

    /**
     * @param array $data
     * @throws \Exception
     * @dataProvider validUrlDataProvider
     */
    public function testWithValidData(array $data)
    {
        /** @var MockObject|UrlRepository $urlRepository */
        $urlRepository = $this->createMock(UrlRepository::class);
        $urlRepository->method('getByUrl')->willReturn(new UrlEntity($data));
        $urlRepository->method('getRedirects')->willReturn([
            new UrlEntity(),
            new UrlEntity(),
        ]);
        $urlRepository->expects($this->exactly(3))->method('delete');

        $service = new UrlIndexService(
            $urlRepository,
            null
        );

        $service->removeUrl($data['url']);
    }

    public function validUrlDataProvider(): array
    {
        return [
            [
                [
                    'id' => 1,
                    'url' => '/some-url'
                ],
            ],
        ];
    }
}