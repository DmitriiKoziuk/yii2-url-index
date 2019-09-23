<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\unit\url;

use Yii;
use yii\di\Container;
use Codeception\Test\Unit;
use DmitriiKoziuk\yii2Base\exceptions\DataNotValidException;
use DmitriiKoziuk\yii2Base\exceptions\ExternalComponentException;
use DmitriiKoziuk\yii2Base\exceptions\InvalidFormException;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2UrlIndex\tests\UnitTester;
use DmitriiKoziuk\yii2UrlIndex\tests\_stubs\UrlRepositoryStub;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlCreateForm;
use DmitriiKoziuk\yii2UrlIndex\services\UrlService;

class UrlServiceCreateUrlMethodTest extends Unit
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
     * @dataProvider validUrlCreateFormDataProvider
     */
    public function testCreateMethodWithValidData(array $data)
    {
        $service = new UrlService(
            new UrlRepositoryStub($data['id'], $data['created_at'], $data['updated_at']),
            null
        );
        $createFormData = $data;
        unset($createFormData['id'], $createFormData['created_at'], $createFormData['updated_at']);

        $createForm = new UrlCreateForm($createFormData);
        $this->assertTrue($createForm->validate());

        $returnForm = $service->createUrl($createForm);
        $this->assertInstanceOf(
            UrlUpdateForm::class,
            $returnForm
        );
        $this->assertEquals(
            $data,
            $returnForm->getAttributes()
        );
    }

    public function testCreateMethodThrowInvalidFormException()
    {
        $service = new UrlService(
            new UrlRepositoryStub(1, '', ''),
            null
        );

        $createForm = new UrlCreateForm();
        $this->assertFalse($createForm->validate());
        $this->expectException(InvalidFormException::class);
        $service->createUrl($createForm);
    }

    public function validUrlCreateFormDataProvider()
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