<?php

namespace DmitriiKoziuk\yii2UrlIndex\tests;

use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UserFixture;

class MainCest
{
    private $loggedInCookie;
    private $createdPageId;

    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'login_data.php'
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
        $this->loggedInCookie = $I->grabCookie('advanced-backend');
        codecept_debug($this->loggedInCookie);
    }

    /**
     * @param AcceptanceTester $I
     * @depends trySignIn
     */
    public function tryClickToModuleLinkInNavigationMenu(AcceptanceTester $I)
    {
        $I->setcookie('advanced-backend', $this->loggedInCookie);
        $I->amOnPage('/');
        $I->seeResponseCodeIs(200);
        $I->click('Url index', '.dropdown-menu');
        $I->seeResponseCodeIs(200);
        $I->see('Urls');
    }

    /**
     * @param AcceptanceTester $I
     * @depends trySignIn
     */
    public function tryClickToCreateUrlLink(AcceptanceTester $I)
    {
        $I->setcookie('advanced-backend', $this->loggedInCookie);
        $I->amOnPage('/dk-url-index/url/index');
        $I->seeResponseCodeIs(200);
        $I->click('Create Url Index Entity');
        $I->seeResponseCodeIs(200);
        $I->see('Create Url', 'h1');
    }

    /**
     * @param AcceptanceTester $I
     * @depends trySignIn
     */
    public function tryCreateUrlWithAllFieldSet(AcceptanceTester $I)
    {
        $I->setcookie('advanced-backend', $this->loggedInCookie);
        $I->amOnPage('/dk-url-index/url/create');
        $I->seeResponseCodeIs(200);
        $I->see('Create Url', 'h1');
        $I->fillField('UrlCreateForm[url]', '/some-url.html');
        $I->fillField('UrlCreateForm[redirect_to_url]', '/to-new-url.html');
        $I->fillField('UrlCreateForm[module_name]', 'module');
        $I->fillField('UrlCreateForm[controller_name]', 'controller');
        $I->fillField('UrlCreateForm[action_name]', 'action');
        $I->fillField('UrlCreateForm[entity_id]', '1');
        $I->click('#save-url');
        $I->seeResponseCodeIs(200);
        $I->see('Url created', 'h1');
        $I->see('Created At');
        $this->createdPageId = $I->grabFromCurrentUrl('~id=(\d+)$~');
    }

    /**
     * @param AcceptanceTester $I
     * @depends tryCreateUrlWithAllFieldSet
     */
    public function tryDeleteUrl(AcceptanceTester $I)
    {
        $I->setcookie('advanced-backend', $this->loggedInCookie);
        $I->amOnPage("/dk-url-index/url/view?id={$this->createdPageId}");
        $I->click('#delete-url');
        $I->seeResponseCodeIs(200);
    }
}
