<?php

namespace DmitriiKoziuk\yii2UrlIndex\tests;

use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UserFixture;

class MainCest
{
    private $loggedInCookie;

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
}
