<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests;

use Yii;
use yii\di\Container;
use yii\helpers\Url;
use Codeception\Example;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UserFixture;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UrlsFixture;

class UrlViewPageCest
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
     * @param AcceptanceTester $I
     * @param Example $existUrls
     * @depends trySignIn
     * @dataProvider urlDataProvider
     */
    public function tryOpenViewPage(AcceptanceTester $I, Example $existUrls)
    {
        $I->wantTo('Check is url view page open.');
        $I->amOnPage(Url::toRoute(['/dk-url-index/url/view', 'id' => $existUrls['id']]));
        $I->seeResponseCodeIs(200);

        $I->see("Url created: {$existUrls['url']}", 'h1');
    }

    /**
     * @param AcceptanceTester $I
     * @param Example $existUrls
     * @depends tryOpenViewPage
     * @dataProvider urlDataProvider
     */
    public function checkIsUpdateButtonExistOnPage(AcceptanceTester $I, Example $existUrls)
    {
        $I->wantTo('Check is update button exist on page.');
        $I->amOnPage(Url::toRoute(['/dk-url-index/url/view', 'id' => $existUrls['id']]));
        $I->seeResponseCodeIs(200);

        $I->seeElement('a#update-url');
    }

    /**
     * @param AcceptanceTester $I
     * @param Example $existUrls
     * @depends tryOpenViewPage
     * @dataProvider urlDataProvider
     */
    public function checkIsDeleteButtonExistOnPage(AcceptanceTester $I, Example $existUrls)
    {
        $I->wantTo('Check is delete button exist on page.');
        $I->amOnPage(Url::toRoute(['/dk-url-index/url/view', 'id' => $existUrls['id']]));
        $I->seeResponseCodeIs(200);

        $I->seeElement('a#delete-url');
    }

    /**
     * @param AcceptanceTester $I
     * @param Example $existUrls
     * @depends tryOpenViewPage
     * @dataProvider urlDataProvider
     */
    public function checkIsAllRequiredAttributesExistOnPage(AcceptanceTester $I, Example $existUrls)
    {
        $I->wantTo('Check is all required attributes exist on page.');
        $I->amOnPage(Url::toRoute(['/dk-url-index/url/view', 'id' => $existUrls['id']]));
        $I->seeResponseCodeIs(200);

        $I->see('ID', 'table th');
        $I->see('Url', 'table th');
        $I->see('Redirect To Url', 'table th');
        $I->see('Module Name', 'table th');
        $I->see('Controller Name', 'table th');
        $I->see('Action Name', 'table th');
        $I->see('Entity ID', 'table th');
        $I->see('Created At', 'table th');
        $I->see('Updated At', 'table th');
    }

    /**
     * @return array
     */
    protected function urlDataProvider()
    {
        return include codecept_data_dir() . 'url_data.php';
    }
}