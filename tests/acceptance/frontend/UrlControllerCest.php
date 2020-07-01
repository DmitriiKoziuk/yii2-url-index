<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\acceptance;

use Yii;
use yii\di\Container;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UrlEntityFixture;
use DmitriiKoziuk\yii2UrlIndex\tests\AcceptanceTester;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;

class UrlControllerCest
{
    public function _fixtures()
    {
        return [
            'urls' => [
                'class' => UrlEntityFixture::class,
                'dataFile' => codecept_data_dir() . 'url_data.php'
            ]
        ];
    }

    public function _after(AcceptanceTester $I)
    {
        Yii::$container = new Container();
    }

    /**
     * @env frontend
     * @param AcceptanceTester $I
     */
    public function tryOpenTestPage(AcceptanceTester $I)
    {
        $testPageUrl = '/test';
        $I->seeRecord(UrlEntity::class, ['url' => $testPageUrl]);
        $I->amOnPage($testPageUrl);
        $I->seeResponseCodeIs(200);
        $I->see('Test page open successful.');
    }

    /**
     * @env frontend
     * @param AcceptanceTester $I
     */
    public function tryOpenRedirectUrl(AcceptanceTester $I)
    {
        $startUrl = '/some-url-3.html';
        $destinationUrl = '/some-url.html';
        $I->seeRecord(UrlEntity::class, ['url' => $startUrl, 'redirect_to_url' => 1]);
        $I->seeRecord(UrlEntity::class, ['id' => 1, 'url' => $destinationUrl]);
        $I->amOnPage($startUrl);
        $I->seeInCurrentUrl($destinationUrl);
    }
}
