<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests;

use Codeception\Example;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UserFixture;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UrlsFixture;

class MainPageCest
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
     * @depends trySignIn
     */
    public function tryOpenMainPage(AcceptanceTester $I)
    {
        $I->wantTo('Check is main page open.');
        $I->amOnPage('/dk-url-index/url/index');
        $I->seeResponseCodeIs(200);

        $I->see('Urls', 'h1');
    }

    /**
     * @param AcceptanceTester $I
     * @depends trySignIn
     */
    public function checkIsCreateUrlButtonExistOnPage(AcceptanceTester $I)
    {
        $I->wantTo('Check is create url button exist on page.');
        $I->amOnPage('/dk-url-index/url/index');
        $I->seeResponseCodeIs(200);

        $I->see('Create Url Index Entity', 'a');
    }

    /**
     * @param AcceptanceTester $I
     * @param Example $existUrls
     * @depends tryOpenMainPage
     * @dataProvider urlDataProvider
     */
    public function checkIsUrlsExistOnMainPage(AcceptanceTester $I, Example $existUrls)
    {
        $I->wantTo('Check is urls load and view on page.');
        $I->amOnPage('/dk-url-index/url/index');
        $I->seeResponseCodeIs(200);

        $I->see($existUrls['url']);
        if (!empty($existUrls['redirect_to_url'])) {
            $I->see($existUrls['redirect_to_url']);
        }
        if (!empty($existUrls['module_name'])) {
            $I->see($existUrls['module_name']);
        }
        $I->see($existUrls['controller_name']);
        $I->see($existUrls['action_name']);
        $I->see($existUrls['entity_id']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends tryOpenMainPage
     */
    public function checkIsUrlNumberEquivalentOfUrlsInDB(AcceptanceTester $I)
    {
        $I->wantTo('Check is url number equivalent of urls in DB.');
        $I->amOnPage('/dk-url-index/url/index');
        $I->seeResponseCodeIs(200);

        $I->see('Showing 1-3 of 3 items.');
    }

    /**
     * @param AcceptanceTester $I
     * @param Example $url
     * @depends tryOpenMainPage
     * @dataProvider oneUrlDataProvider
     */
    public function trySearchById(AcceptanceTester $I, Example $url)
    {
        $I->wantTo('try search by id field.');
        $I->amOnPage("/dk-url-index/url/index?UrlSearchForm[id]={$url['id']}");
        $I->seeResponseCodeIs(200);

        $I->see('Showing 1-1 of 1 item.');
        $I->see($url['url']);
    }

    /**
     * @param AcceptanceTester $I
     * @param Example $url
     * @depends tryOpenMainPage
     * @dataProvider oneUrlDataProvider
     */
    public function trySearchUrlUrl(AcceptanceTester $I, Example $url)
    {
        $I->wantTo('try search by url field.');
        $I->amOnPage("/dk-url-index/url/index?UrlSearchForm[url]={$url['url']}");
        $I->seeResponseCodeIs(200);

        $I->see('Showing 1-1 of 1 item.');
        $I->see($url['url']);
    }

    /**
     * @return array
     */
    protected function urlDataProvider()
    {
        return include codecept_data_dir() . 'url_data.php';
    }

    protected function oneUrlDataProvider(): array
    {
        $array = include codecept_data_dir() . 'url_data.php';
        return [
            array_shift($array),
        ];
    }
}