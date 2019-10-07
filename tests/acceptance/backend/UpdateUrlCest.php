<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests;

use Yii;
use yii\di\Container;
use yii\helpers\Url;
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
     * @param Example $data
     * @depends trySignIn
     * @dataProvider urlUpdateDataProvider
     */
    public function tryUpdateValue(AcceptanceTester $I, Example $data)
    {
        $I->amOnPage(Url::toRoute(['/dk-url-index/url/update', 'id' => $data['id']]));
        $I->see("Update Url Index Entity: {$data['id']}", 'h1');
        foreach ($data['fields'] as $name => $value) {
            $I->fillField(['name' => $name], $value);
        }
        $I->click('#save-url');

        $I->seeResponseCodeIs(200);
        foreach ($data['fields'] as $name => $value) {
            $I->see($value);
        }
        $I->seeElement('#delete-url');
        $I->click('#delete-url');

        $I->seeResponseCodeIs(200);
        $I->dontSee('Url created');
        $I->see('Urls', 'h1');
    }

    /**
     * @return array
     */
    protected function urlUpdateDataProvider()
    {
        return [
            [
                'id' => 1,
                'fields' => [
                    'UrlUpdateForm[url]' => '/some-update-url-1.html'
                ]
            ],
            [
                'id' => 1,
                'fields' => [
                    'UrlUpdateForm[url]' => '/some-second-update-url-1.html',
                    'UrlUpdateForm[module_name]' => 'some-module-name',
                    'UrlUpdateForm[controller_name]' => 'some-controller-name',
                    'UrlUpdateForm[action_name]' => 'some-action-name',
                    'UrlUpdateForm[entity_id]' => 'some-entity-id',
                ]
            ],
        ];
    }
}