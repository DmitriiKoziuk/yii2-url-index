<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests;

use Codeception\Example;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UserFixture;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UrlsFixture;

class UpdateUrlCest
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

    public function trySignIn(AcceptanceTester $I)
    {
        $I->amOnPage('/site/login');
        $I->see('Please fill out the following fields to login:');
        $I->fillField('LoginForm[username]', 'erau');
        $I->fillField('LoginForm[password]', 'password_0');
        $I->click('Login', '#login-form');
        $I->seeResponseCodeIs(200);
        $I->see('Logout (erau)');
        $I->dontSeeLink('Login');
        $I->dontSeeLink('Signup');
    }

    /**
     * @param AcceptanceTester $I
     * @param Example $existUrls
     * @param Example $dataForUpdate
     * @depends trySignIn
     * @dataProvider urlDataProvider
     * @dataProvider urlUpdateDataProvider
     */
    public function tryUpdateValue(AcceptanceTester $I, Example $existUrls, Example $dataForUpdate)
    {
        $I->amOnPage("/dk-url-index/url/update?id={$existUrls['id']}");
        $I->see("Update Url Index Entity: {$existUrls['id']}", 'h1');

        $I->fillField(['name' => "UrlUpdateForm[url]"], $dataForUpdate['url']);
        $value = $dataForUpdate['redirect_to_url'] ?? '';
        $I->fillField(['name' => 'UrlUpdateForm[redirect_to_url]'], $value);
        $value = $dataForUpdate['module_name'] ?? '';
        $I->fillField(['name' => 'UrlUpdateForm[module_name]'], $value);
        $I->fillField(['name' => 'UrlUpdateForm[controller_name]'], $dataForUpdate['controller_name']);
        $I->fillField(['name' => 'UrlUpdateForm[action_name]'], $dataForUpdate['action_name']);
        $I->fillField(['name' => 'UrlUpdateForm[action_name]'], $dataForUpdate['action_name']);
        $I->click('#save-url');

        $I->seeResponseCodeIs(200);
        $I->see("Url created: {$dataForUpdate['url']}", 'h1');
        $I->seeElement('#delete-url');
        $I->click('#delete-url');

        $I->seeResponseCodeIs(200);
        $I->dontSee('Url created');
        $I->see('Urls', 'h1');
    }

    /**
     * @return array
     */
    protected function urlDataProvider()
    {
        return include codecept_data_dir() . 'url_data.php';
    }

    /**
     * @return array
     */
    protected function urlUpdateDataProvider()
    {
        return include codecept_data_dir() . 'url_update_data.php';
    }
}