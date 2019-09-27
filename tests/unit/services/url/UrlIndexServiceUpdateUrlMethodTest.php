<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\unit\services\url;

use Yii;
use yii\di\Container;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\MockObject;
use DmitriiKoziuk\yii2Base\exceptions\InvalidFormException;
use DmitriiKoziuk\yii2UrlIndex\tests\UnitTester;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UrlsFixture;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\services\UrlIndexService;
use DmitriiKoziuk\yii2UrlIndex\repositories\UrlRepository;
use DmitriiKoziuk\yii2UrlIndex\exceptions\UrlNotFoundException;
use DmitriiKoziuk\yii2UrlIndex\exceptions\UrlAlreadyHasBeenTakenException;

class UrlIndexServiceUpdateUrlMethodTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
        return [
            'url' => [
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
     * @throws \Exception
     */
    public function testUrlUpdateFormNotValid()
    {
        /** @var MockObject|UrlRepository $urlRepository */
        $urlRepository = $this->createMock(UrlRepository::class);

        $service = new UrlIndexService(
            $urlRepository,
            null
        );
        $updateForm = new UrlUpdateForm();

        $this->expectException(InvalidFormException::class);
        $service->updateUrl($updateForm);
    }

    /**
     * @param array $data
     * @throws \Throwable
     * @dataProvider validUrlUpdateFormDataProvider
     */
    public function testUpdatedUrlNotFound(array $data)
    {
        /** @var MockObject|UrlRepository $urlRepository */
        $urlRepository = $this->createMock(UrlRepository::class);
        $urlRepository->method('getById')->willReturn(null);

        $service = new UrlIndexService(
            $urlRepository,
            null
        );
        $updateForm = new UrlUpdateForm($data);

        $this->assertTrue($updateForm->validate());
        $this->expectException(UrlNotFoundException::class);
        $service->updateUrl($updateForm);
    }

    /**
     * @param array $urlUpdateFormData
     * @throws \Throwable
     * @dataProvider validUrlUpdateFormDataProvider
     */
    public function testUrlAlreadyHasBenTaken(array $urlUpdateFormData): void
    {
        /** @var MockObject|UrlRepository $urlRepository */
        $urlRepository = $this->createMock(UrlRepository::class);
        $urlRepository->method('getById')->willReturn(new UrlEntity($urlUpdateFormData));
        $urlRepository->expects($this->exactly(1))
            ->method('getByUrl')
            ->willReturn(new UrlEntity($urlUpdateFormData));

        $service = new UrlIndexService(
            $urlRepository,
            null
        );
        $updateForm = new UrlUpdateForm($urlUpdateFormData);

        $this->assertTrue($updateForm->validate());
        $this->expectException(UrlAlreadyHasBeenTakenException::class);
        $service->updateUrl($updateForm);
    }

    /**
     * @param array $urlUpdateFormData
     * @param array $redirectUrlEntityData
     * @throws \Throwable
     * @dataProvider validUrlUpdateFormDataProvider
     */
    public function testUpdateUrl(array $urlUpdateFormData, array $redirectUrlEntityData): void
    {
        /** @var MockObject|UrlRepository $urlRepository */
        $urlRepository = $this->createMock(UrlRepository::class);
        $urlRepository->method('getById')->willReturn(new UrlEntity($urlUpdateFormData));
        $urlRepository->expects($this->exactly(2))
            ->method('save')
            ->willReturn(new UrlEntity($urlUpdateFormData), new UrlEntity($redirectUrlEntityData));

        $service = new UrlIndexService(
            $urlRepository,
            null
        );
        $updateForm = new UrlUpdateForm($urlUpdateFormData);
        $this->assertTrue($updateForm->validate());

        $returnForm = $service->updateUrl($updateForm);
        $this->assertInstanceOf(UrlUpdateForm::class, $returnForm);
        $this->assertEquals($urlUpdateFormData, $returnForm->getAttributes());
    }

    /**
     * @param array $urlUpdateFormData
     * @param array $redirectUrlEntityData
     * @throws \Throwable
     * @dataProvider validUrlUpdateFormDataProvider
     */
    public function testUpdateUrlWithOverwritingRedirectUrl(array $urlUpdateFormData, array $redirectUrlEntityData)
    {
        /** @var MockObject|UrlRepository $urlRepository */
        $urlRepository = $this->createMock(UrlRepository::class);
        $urlRepository->method('getById')->willReturn(new UrlEntity($urlUpdateFormData));
        $urlRepository->expects($this->exactly(1))
            ->method('getByUrl')
            ->willReturn(new UrlEntity($redirectUrlEntityData));
        $urlRepository->expects($this->exactly(1))
            ->method('delete');
        $urlRepository->expects($this->exactly(2))
            ->method('save')
            ->willReturn(new UrlEntity($urlUpdateFormData), new UrlEntity($redirectUrlEntityData));

        $service = new UrlIndexService(
            $urlRepository,
            null
        );
        $updateForm = new UrlUpdateForm($urlUpdateFormData);
        $this->assertTrue($updateForm->validate());

        $service->updateUrl($updateForm);
    }

    public function validUrlUpdateFormDataProvider(): array
    {
        return  [
            [
                'UrlUpdateForm' => [
                    'id' => 1,
                    'url' => '/some-mock-url.html',
                    'redirect_to_url' => NULL,
                    'module_name' => 'module',
                    'controller_name' => 'controller',
                    'action_name' => 'action',
                    'entity_id' => '1',
                    'created_at' => '1392559490',
                    'updated_at' => '1392559490',
                ],
                'RedirectUrlEntityData' => [
                    'id' => 2,
                    'url' => '/some-mock-redirect-url.html',
                    'redirect_to_url' => 1,
                    'module_name' => 'dk-url-index',
                    'controller_name' => 'url',
                    'action_name' => 'redirect',
                    'entity_id' => '302',
                    'created_at' => '1392559490',
                    'updated_at' => '1392559490',
                ],
            ],
        ];
    }
}