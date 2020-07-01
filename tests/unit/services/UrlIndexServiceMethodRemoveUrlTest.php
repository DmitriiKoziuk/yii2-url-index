<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\unit\services;

use Yii;
use Codeception\Test\Unit;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\ModuleEntityFixture;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UrlEntityFixture;
use DmitriiKoziuk\yii2UrlIndex\tests\UnitTester;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlIndexServiceInterface;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\exceptions\UrlNotFoundException;

class UrlIndexServiceMethodRemoveUrlTest extends Unit
{
    public UnitTester $tester;

    public function _fixtures()
    {
        return [
            'modules' => ModuleEntityFixture::class,
            'urls' => UrlEntityFixture::class,
        ];
    }

    public function testRemoveNotExistUrlThrowException()
    {
        /** @var UrlIndexServiceInterface $service */
        $service = Yii::$container->get(UrlIndexServiceInterface::class);
        $dontExistUrl = '/some-not-exist-url';

        $this->tester->dontSeeRecord(UrlEntity::class, [
            'url' => $dontExistUrl
        ]);
        $this->expectException(UrlNotFoundException::class);
        $service->removeUrl($dontExistUrl);
    }

    public function testRemoveExistUrl()
    {
        /** @var UrlIndexServiceInterface $service */
        $service = Yii::$container->get(UrlIndexServiceInterface::class);
        /** @var UrlEntity $urlEntityFixture */
        $urlEntityFixture = $this->tester->grabFixture('urls', 'shopUrl2');

        $this->tester->seeRecord(UrlEntity::class, [
            'url' => $urlEntityFixture->url
        ]);
        $service->removeUrl($urlEntityFixture->url);
        $this->tester->dontSeeRecord(UrlEntity::class, [
            'url' => $urlEntityFixture->url
        ]);
    }

    public function testRemoveExistUrlWithRedirects()
    {
        /** @var UrlIndexServiceInterface $service */
        $service = Yii::$container->get(UrlIndexServiceInterface::class);
        /** @var UrlEntity $urlEntityFixture */
        $urlEntityFixture = $this->tester->grabFixture('urls', 'shopUrl1');
        /** @var UrlEntity $redirectUrlOne */
        $redirectUrlOne = $this->tester->grabFixture('urls', 'shopRedirect1');
        /** @var UrlEntity $redirectUrlTwo */
        $redirectUrlTwo = $this->tester->grabFixture('urls', 'shopRedirect2');

        $this->tester->seeRecord(UrlEntity::class, [
            'url' => $urlEntityFixture->url
        ]);
        $this->tester->seeRecord(UrlEntity::class, [
            'url' => $redirectUrlOne->url
        ]);
        $this->tester->seeRecord(UrlEntity::class, [
            'url' => $redirectUrlTwo->url
        ]);

        $service->removeUrl($urlEntityFixture->url);

        $this->tester->dontSeeRecord(UrlEntity::class, [
            'url' => $urlEntityFixture->url
        ]);
        $this->tester->dontSeeRecord(UrlEntity::class, [
            'url' => $redirectUrlOne->url
        ]);
        $this->tester->dontSeeRecord(UrlEntity::class, [
            'url' => $redirectUrlTwo->url
        ]);
    }
}
