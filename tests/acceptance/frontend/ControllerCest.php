<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests;

use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UrlsFixture;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UserFixture;

class ControllerCest
{
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'login_data.php'
            ],
            'urls' => [
                'class' => UrlsFixture::class,
                'dataFile' => codecept_data_dir() . 'url_data.php'
            ]
        ];
    }

    /**
     * @param AcceptanceTester $I
     * @env frontend
     */
    public function tryRedirect(AcceptanceTester $I)
    {
        $I->amOnPage('/some-url-3.html');
        $I->seeResponseCodeIs(302);
    }
}