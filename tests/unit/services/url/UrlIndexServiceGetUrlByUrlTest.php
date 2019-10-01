<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\unit\services\url;

use Yii;
use yii\di\Container;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\MockObject;
use DmitriiKoziuk\yii2UrlIndex\tests\UnitTester;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2UrlIndex\repositories\UrlRepository;
use DmitriiKoziuk\yii2UrlIndex\services\UrlIndexService;

class UrlIndexServiceGetUrlByUrlTest extends Unit
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
     * @dataProvider validDataProvider
     */
    public function testWithValidData(array $data): void
    {
        /** @var MockObject|UrlRepository $repository */
        $repository = $this->createMock(UrlRepository::class);
        $repository->expects($this->exactly(1))
            ->method('getByUrl')
            ->willReturn(new UrlEntity($data));

        $service = new UrlIndexService(
            $repository,
            null
        );

        $serviceReturn = $service->getUrlByUrl($data['url']);
        $this->assertInstanceOf(UrlUpdateForm::class, $serviceReturn);
        $this->assertEquals($data, $serviceReturn->getAttributes());
    }

    public function validDataProvider(): array
    {
        return [
            [
                [
                    'id' => 1,
                    'url' => '/some-url.html',
                    'redirect_to_url' => NULL,
                    'module_name' => 'module',
                    'controller_name' => 'controller',
                    'action_name' => 'action',
                    'entity_id' => '1',
                    'created_at' => '1392559490',
                    'updated_at' => '1392559490',
                ],
            ],
        ];
    }
}