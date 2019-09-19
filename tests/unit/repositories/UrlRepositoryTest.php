<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\unit\repositories;

use Yii;
use yii\di\Container;
use Codeception\Test\Unit;
use DmitriiKoziuk\yii2UrlIndex\tests\UnitTester;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UrlsFixture;
use DmitriiKoziuk\yii2UrlIndex\repositories\UrlRepository;

class UrlRepositoryTest extends Unit
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
    
    protected function _before()
    {
    }

    protected function _after()
    {
        Yii::$container = new Container();
    }

    /**
     * @param int $id
     * @dataProvider existUrlDataProvider
     */
    public function testMethodGetByIdReturnEntity(int $id)
    {
        /** @var UrlRepositoryInterface $urlRepository */
        $urlRepository = new UrlRepository();
        $entity = $urlRepository->getById($id);
        $this->assertInstanceOf(UrlEntity::class, $entity);
        $this->assertEquals($id, $entity->id);
    }

    public function testMethodGetByIdReturnNull()
    {
        /** @var UrlRepositoryInterface $urlRepository */
        $urlRepository = new UrlRepository();
        $this->assertNull($urlRepository->getById(333));
    }

    /**
     * @param int $id
     * @param string $url
     * @dataProvider existUrlDataProvider
     */
    public function testMethodGetByUrlReturnEntity(int $id, string $url)
    {
        /** @var UrlRepositoryInterface $urlRepository */
        $urlRepository = new UrlRepository();
        $entity = $urlRepository->getByUrl($url);
        $this->assertInstanceOf(UrlEntity::class, $entity);
        $this->assertEquals($url, $entity->url);
    }

    public function testMethodGetByUrlReturnNull()
    {
        /** @var UrlRepositoryInterface $urlRepository */
        $urlRepository = new UrlRepository();
        $this->assertNull($urlRepository->getByUrl('/some-fake-url.html'));
    }

    public function existUrlDataProvider()
    {
        return include codecept_data_dir() . 'url_data.php';
    }
}