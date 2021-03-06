<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests;

use Yii;
use yii\di\Container;
use yii\helpers\Url;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UserFixture;

class CreateAndDeleteUrlCest
{
    private $loggedInCookie;
    private $createdUrlId;

    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'login_data.php'
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
        $this->loggedInCookie = $I->grabCookie('advanced-backend');
        codecept_debug($this->loggedInCookie);
    }

    /**
     * @env backend
     * @param AcceptanceTester $I
     * @depends trySignIn
     */
    public function tryClickToCreateUrlLink(AcceptanceTester $I)
    {
        $I->setcookie('advanced-backend', $this->loggedInCookie);
        $I->amOnPage(Url::toRoute(['/dk-url-index/url/index']));
        $I->seeResponseCodeIs(200);
        $I->click('Create Url Index Entity');
        $I->seeResponseCodeIs(200);
        $I->see('Create Url', 'h1');
    }

    /**
     * @env backend
     * @param AcceptanceTester $I
     * @depends trySignIn
     */
    public function tryCreateUrlWithAllFieldSet(AcceptanceTester $I)
    {
        $I->setcookie('advanced-backend', $this->loggedInCookie);
        $I->amOnPage(Url::toRoute(['/dk-url-index/url/create']));
        $I->seeResponseCodeIs(200);
        $I->see('Create Url', 'h1');
        $I->fillField('UrlCreateForm[url]', '/some-url.html');
        $I->fillField('UrlCreateForm[redirect_to_url]', '');
        $I->fillField('UrlCreateForm[module_name]', 'module');
        $I->fillField('UrlCreateForm[controller_name]', 'controller');
        $I->fillField('UrlCreateForm[action_name]', 'action');
        $I->fillField('UrlCreateForm[entity_id]', '1');
        $I->click('#save-url');
        $I->seeResponseCodeIs(200);
        $I->see('Url created', 'h1');
        $I->see('Created At');
        $this->createdUrlId = $I->grabFromCurrentUrl('~id=(\d+)$~');
    }

    /**
     * @env backend
     * @param AcceptanceTester $I
     * @depends tryCreateUrlWithAllFieldSet
     */
    public function tryDeleteUrl(AcceptanceTester $I)
    {
        $I->setcookie('advanced-backend', $this->loggedInCookie);
        $I->amOnPage(Url::toRoute(['/dk-url-index/url/view', 'id' => $this->createdUrlId]));
        $I->click('#delete-url');
        $I->seeResponseCodeIs(200);
    }
}