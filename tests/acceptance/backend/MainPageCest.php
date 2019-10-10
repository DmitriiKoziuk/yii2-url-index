<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests;

use Yii;
use yii\di\Container;
use yii\helpers\Url;
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

    public function _before()
    {
        Yii::$container = new Container();
    }

    /**
     * @env backend
     * @param AcceptanceTester $I
     */
    public function trySignIn(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute(['/site/login']));
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
     * @env backend
     * @param AcceptanceTester $I
     * @depends trySignIn
     */
    public function tryOpenMainPage(AcceptanceTester $I)
    {
        $I->wantTo('Check is main page open.');
        $I->amOnPage(Url::toRoute(['/dk-url-index/url/index']));
        $I->seeResponseCodeIs(200);

        $I->see('Urls', 'h1');
    }

    /**
     * @env backend
     * @param AcceptanceTester $I
     * @depends trySignIn
     */
    public function checkIsCreateUrlButtonExistOnPage(AcceptanceTester $I)
    {
        $I->wantTo('Check is create url button exist on page.');
        $I->amOnPage(Url::toRoute(['/dk-url-index/url/index']));
        $I->seeResponseCodeIs(200);

        $I->see('Create Url Index Entity', 'a');
    }

    /**
     * @env backend
     * @param AcceptanceTester $I
     * @param Example $existUrls
     * @depends tryOpenMainPage
     * @dataProvider urlDataProvider
     */
    public function checkIsUrlsExistOnMainPage(AcceptanceTester $I, Example $existUrls)
    {
        $I->wantTo('Check is urls load and view on page.');
        $I->amOnPage(Url::toRoute(['/dk-url-index/url/index']));
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
     * @env backend
     * @param AcceptanceTester $I
     * @depends tryOpenMainPage
     */
    public function checkIsUrlNumberEquivalentOfUrlsInDB(AcceptanceTester $I)
    {
        $I->wantTo('Check is url number equivalent of urls in DB.');
        $I->amOnPage(Url::toRoute(['/dk-url-index/url/index']));
        $I->seeResponseCodeIs(200);

        $I->see('Showing 1-6 of 6 items.');
    }

    /**
     * @env backend
     * @param AcceptanceTester $I
     * @param Example $url
     * @depends tryOpenMainPage
     * @dataProvider oneUrlDataProvider
     */
    public function trySearchById(AcceptanceTester $I, Example $url)
    {
        $I->wantTo('try search by id field.');
        $I->amOnPage(Url::toRoute(['/dk-url-index/url/index', 'UrlSearchForm[id]' => $url['id']]));
        $I->seeResponseCodeIs(200);
        $I->see($url['url']);
    }

    /**
     * @env backend
     * @param AcceptanceTester $I
     * @param Example $url
     * @depends tryOpenMainPage
     * @dataProvider oneUrlDataProvider
     */
    public function trySearchUrlField(AcceptanceTester $I, Example $url)
    {
        $I->wantTo('try search by url field.');
        $I->amOnPage(Url::toRoute(['/dk-url-index/url/index', 'UrlSearchForm[url]' => $url['url']]));
        $I->seeResponseCodeIs(200);
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