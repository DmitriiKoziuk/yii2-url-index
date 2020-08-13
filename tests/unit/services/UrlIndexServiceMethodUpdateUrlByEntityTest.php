<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\unit\services;

use Yii;
use Codeception\Test\Unit;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\ModuleEntityFixture;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UrlEntityFixture;
use DmitriiKoziuk\yii2UrlIndex\tests\UnitTester;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\services\UrlIndexUpdateService;

class UrlIndexServiceMethodUpdateUrlByEntityTest extends Unit
{
    public UnitTester $tester;

    public function _fixtures()
    {
        return [
            'modules' => ModuleEntityFixture::class,
            'urls' => UrlEntityFixture::class,
        ];
    }

    public function testUpdateUrl()
    {
        /** @var UrlEntity $url */
        $url = $this->tester->grabFixture('urls', 'appUrl');
        /** @var UrlIndexUpdateService $service */
        $service = Yii::$container->get(UrlIndexUpdateService::class);
        $oldUrl = $url->url;
        $newUrl = '/some-new-url-that-not-use';

        $this->tester->dontSeeRecord(UrlEntity::class, ['url' => $newUrl]);
        $service->updateUrlByEntity($url, $newUrl);
        $this->tester->seeRecord(UrlEntity::class, ['url' => $newUrl]);
        $this->tester->seeRecord(UrlEntity::class, ['url' => $oldUrl, 'entity_id' => 302]);
    }
}
