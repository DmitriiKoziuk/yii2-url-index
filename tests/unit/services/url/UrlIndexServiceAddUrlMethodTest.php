<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\unit\services\url;

use Yii;
use yii\di\Container;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\MockObject;
use DmitriiKoziuk\yii2Base\exceptions\DataNotValidException;
use DmitriiKoziuk\yii2Base\exceptions\ExternalComponentException;
use DmitriiKoziuk\yii2UrlIndex\exceptions\UrlAlreadyHasBeenTakenException;
use DmitriiKoziuk\yii2Base\exceptions\InvalidFormException;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2UrlIndex\tests\UnitTester;
use DmitriiKoziuk\yii2UrlIndex\tests\_stubs\UrlRepositoryStub;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlCreateForm;
use DmitriiKoziuk\yii2UrlIndex\services\UrlIndexService;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\repositories\UrlRepository;

class UrlIndexServiceAddUrlMethodTest extends Unit
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
     * @throws DataNotValidException
     * @throws ExternalComponentException
     * @throws InvalidFormException
     * @throws UrlAlreadyHasBeenTakenException
     * @dataProvider validUrlCreateFormDataProvider
     */
    public function testWithValidData(array $data): void
    {
        /** @var MockObject|UrlRepository $urlRepository */
        $urlRepository = $this->createMock(UrlRepository::class);
        $urlRepository->method('getByUrl')->willReturn(null);
        $urlRepository->method('save')->willReturn(new UrlEntity($data));

        $service = new UrlIndexService(
            $urlRepository,
            null
        );

        $createFormData = $data;
        unset($createFormData['id'], $createFormData['created_at'], $createFormData['updated_at']);
        $createForm = new UrlCreateForm($createFormData);
        $this->assertTrue($createForm->validate());

        $returnForm = $service->addUrl($createForm);
        $this->assertInstanceOf(
            UrlUpdateForm::class,
            $returnForm
        );
        $this->assertEquals(
            $data,
            $returnForm->getAttributes()
        );
    }

    public function testThrowInvalidFormException(): void
    {
        $service = new UrlIndexService(
            new UrlRepositoryStub(),
            null
        );

        $createForm = new UrlCreateForm();
        $this->assertFalse($createForm->validate());
        $this->expectException(InvalidFormException::class);
        $service->addUrl($createForm);
    }

    /**
     * @param array $data
     * @throws DataNotValidException
     * @throws ExternalComponentException
     * @throws InvalidFormException
     * @throws UrlAlreadyHasBeenTakenException
     * @depends      testWithValidData
     * @dataProvider validUrlCreateFormDataProvider
     */
    public function testThrowUrlAlreadyExistException(array $data): void
    {
        $service = new UrlIndexService(
            new UrlRepositoryStub($data),
            null
        );
        $createFormData = $data;
        unset($createFormData['id'], $createFormData['created_at'], $createFormData['updated_at']);
        $createForm = new UrlCreateForm($createFormData);
        $this->expectException(UrlAlreadyHasBeenTakenException::class);
        $service->addUrl($createForm);
    }

    public function validUrlCreateFormDataProvider(): array
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
