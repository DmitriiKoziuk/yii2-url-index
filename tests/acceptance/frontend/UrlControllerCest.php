<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\acceptance;

use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UrlsFixture;
use DmitriiKoziuk\yii2UrlIndex\tests\AcceptanceTester;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;

class UrlControllerCest
{
    public function _fixtures()
    {
        return [
            'urls' => [
                'class' => UrlsFixture::class,
                'dataFile' => codecept_data_dir() . 'url_data.php'
            ]
        ];
    }

    public function _before(AcceptanceTester $I)
    {
    }

    /**
     * @env frontend
     * @param AcceptanceTester $I
     */
    public function tryRedirect(AcceptanceTester $I)
    {
        $startUrl = '/some-url-3.html';
        $destinationUrl = '/some-url.html';
        $I->seeRecord(UrlEntity::class, ['url' => $startUrl, 'redirect_to_url' => 1]);
        $I->seeRecord(UrlEntity::class, ['id' => 1, 'url' => $destinationUrl]);
        $I->amOnPage($startUrl);
        $I->seeInCurrentUrl($destinationUrl);
    }
}
