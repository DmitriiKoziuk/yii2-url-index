<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\unit\services\url;

use Yii;
use yii\di\Container;
use yii\data\ActiveDataProvider;
use Codeception\Test\Unit;
use DmitriiKoziuk\yii2UrlIndex\tests\UnitTester;
use DmitriiKoziuk\yii2UrlIndex\tests\_stubs\UrlRepositoryStub;
use DmitriiKoziuk\yii2UrlIndex\services\UrlIndexService;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlSearchForm;

class UrlIndexServiceSearchMethodTest extends Unit
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

    public function testReturnStatement()
    {
        $service = new UrlIndexService(
            new UrlRepositoryStub(),
            null
        );
        $urlSearchForm = new UrlSearchForm();
        $this->assertInstanceOf(
            ActiveDataProvider::class,
            $service->search($urlSearchForm)
        );
    }
}