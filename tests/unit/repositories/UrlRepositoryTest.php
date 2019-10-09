<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\unit\repositories;

use Yii;
use yii\db\ActiveQuery;
use yii\di\Container;
use Codeception\Test\Unit;
use DmitriiKoziuk\yii2UrlIndex\tests\UnitTester;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UrlsFixture;
use DmitriiKoziuk\yii2UrlIndex\repositories\UrlRepository;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlSearchForm;

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

    public function testMethodGetRedirects()
    {
        /** @var UrlRepositoryInterface $urlRepository */
        $urlRepository = new UrlRepository();
        $redirects = $urlRepository->getRedirects(1);
        $this->assertIsArray($redirects);
        $this->assertEquals(1, count($redirects));
    }

    public function testMethodGetEntityUrlReturnEntity(): void
    {
        $searchAttributes = [
            'module_name' => 'module',
            'controller_name' => 'controller',
            'action_name' => 'action',
            'entity_id' => '1',
        ];
        $this->tester->seeRecord(UrlEntity::class, $searchAttributes);

        $urlRepository = new UrlRepository();
        $urlEntity = $urlRepository->getEntityUrl(
            'module',
            'controller',
            'action',
            '1'
        );
        $this->assertNotEmpty($urlEntity);
        $this->assertInstanceOf(UrlEntity::class, $urlEntity);
        $this->assertEquals(
            $searchAttributes,
            $urlEntity->getAttributes(null, [
                'id',
                'url',
                'redirect_to_url',
                'created_at',
                'updated_at',
            ])
        );
    }

    public function testMethodGetEntityUrlReturnNUll(): void
    {
        $searchAttributes = [
            'module_name' => 'module_zzz',
            'controller_name' => 'controller_zzz',
            'action_name' => 'action_zzz',
            'entity_id' => '1',
        ];
        $this->tester->dontSeeRecord(UrlEntity::class, $searchAttributes);

        $urlRepository = new UrlRepository();
        $urlEntity = $urlRepository->getEntityUrl(
            'module_zzz',
            'controller_zzz',
            'action_zzz',
            '1'
        );
        $this->assertEmpty($urlEntity);
    }

    /**
     * @param array $data
     * @dataProvider existUrlsDataProvider
     */
    public function testMethodUrlSearchQueryBuilder(array $data): void
    {
        /** @var UrlRepositoryInterface $urlRepository */
        $urlRepository = new UrlRepository();
        $urlSearchForm = new UrlSearchForm($data);
        $query = $urlRepository->urlSearchQueryBuilder($urlSearchForm);
        $this->assertInstanceOf(
            ActiveQuery::class,
            $query
        );
        /** @var UrlEntity[] $urlEntities */
        $urlEntities = $query
            ->indexBy('id')
            ->all();
        $this->assertNotEmpty($urlEntities);
        $this->assertEquals(1, count($urlEntities));
        $this->assertArrayHasKey($data['id'], $urlEntities);
        $this->assertInstanceOf(
            UrlEntity::class,
            $urlEntities[ $data['id'] ]
        );
        $this->assertEquals($urlEntities[ $data['id'] ]->getAttributes(), $data);
    }

    public function existUrlDataProvider()
    {
        return include codecept_data_dir() . 'url_data.php';
    }

    public function existUrlsDataProvider()
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