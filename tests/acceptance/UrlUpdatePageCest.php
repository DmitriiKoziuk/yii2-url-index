<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests;

use Codeception\Example;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UrlsFixture;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UserFixture;

class UrlUpdatePageCest
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
     * @depends trySignIn
     * @dataProvider urlDataProvider
     */
    public function tryOpenUpdatePage(AcceptanceTester $I, Example $existUrls)
    {
        $I->wantTo('Check is url update page open.');
        $I->amOnPage("/dk-url-index/url/update?id={$existUrls['id']}");
        $I->seeResponseCodeIs(200);

        $I->see("Update Url Index Entity: {$existUrls['id']}", 'h1');
    }

    /**
     * @param AcceptanceTester $I
     * @param Example $existUrls
     * @depends tryOpenUpdatePage
     * @dataProvider urlDataProvider
     */
    public function existsOnPage(AcceptanceTester $I, Example $existUrls)
    {
        $I->wantTo('Check is all field exist on page.');
        $I->amOnPage("/dk-url-index/url/update?id={$existUrls['id']}");
        $I->seeResponseCodeIs(200);
        $I->see("Update Url Index Entity: {$existUrls['id']}", 'h1');

        $I->seeElement('input', ['name' => "UrlUpdateForm[url]"]);
        $I->seeElement('input', ['name' => 'UrlUpdateForm[redirect_to_url]']);
        $I->seeElement('input', ['name' => 'UrlUpdateForm[module_name]']);
        $I->seeElement('input', ['name' => 'UrlUpdateForm[controller_name]']);
        $I->seeElement('input', ['name' => 'UrlUpdateForm[action_name]']);
        $I->seeElement('input', ['name' => 'UrlUpdateForm[entity_id]']);
    }

    /**
     * @param AcceptanceTester $I
     * @param Example $existUrls
     * @depends tryOpenUpdatePage
     * @dataProvider urlDataProvider
     */
    public function hasRelevantData(AcceptanceTester $I, Example $existUrls)
    {
        $I->wantTo('Check is all field has relevant data.');
        $I->amOnPage("/dk-url-index/url/update?id={$existUrls['id']}");
        $I->seeResponseCodeIs(200);
        $I->see("Update Url Index Entity: {$existUrls['id']}", 'h1');

        $I->seeInField(['name' => "UrlUpdateForm[url]"], $existUrls['url']);
        $value = $existUrls['redirect_to_url'] ?? '';
        $I->seeInField(['name' => 'UrlUpdateForm[redirect_to_url]'], $value);
        $value = $existUrls['module_name'] ?? '';
        $I->seeInField(['name' => 'UrlUpdateForm[module_name]'], $value);
        $I->seeInField(['name' => 'UrlUpdateForm[controller_name]'], $existUrls['controller_name']);
        $I->seeInField(['name' => 'UrlUpdateForm[action_name]'], $existUrls['action_name']);
        $I->seeInField(['name' => 'UrlUpdateForm[action_name]'], $existUrls['action_name']);
    }

    /**
     * @param AcceptanceTester $I
     * @param Example $existUrls
     * @depends tryOpenUpdatePage
     * @dataProvider urlDataProvider
     */
    public function hasSaveButton(AcceptanceTester $I, Example $existUrls)
    {
        $I->wantTo('Check is page has "Save" button');
        $I->amOnPage("/dk-url-index/url/update?id={$existUrls['id']}");
        $I->seeResponseCodeIs(200);
        $I->see("Update Url Index Entity: {$existUrls['id']}", 'h1');

        $I->seeElement('button#save-url[type="submit"]');
    }

    /**
     * @return array
     */
    protected function urlDataProvider()
    {
        return include codecept_data_dir() . 'url_data.php';
    }
}