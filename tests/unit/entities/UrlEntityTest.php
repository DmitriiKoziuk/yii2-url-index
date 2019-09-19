<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\unit\entities;

use Yii;
use yii\di\Container;
use Codeception\Test\Unit;
use DmitriiKoziuk\yii2UrlIndex\tests\UnitTester;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;

class UrlEntityTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public static function setUpBeforeClass(): void
    {

    }
    
    protected function _before()
    {
    }

    protected function _after()
    {
        Yii::$container = new Container();
    }

    /**
     * @param array $attributes
     * @dataProvider validUrlEntityDataProvider
     */
    public function testEntityValidateTrue(array $attributes)
    {
        $urlEntity = new UrlEntity();
        $urlEntity->setAttributes($attributes);
        $this->assertTrue($urlEntity->validate());
    }

    /**
     * @param string $url
     * @param string $expectErrorMessage
     * @dataProvider notValidUrlDataProvider
     */
    public function testUrlNotValid(string $url, string $expectErrorMessage)
    {
        $urlEntity = new UrlEntity();
        $urlEntity->setAttribute('url', $url);
        $this->assertFalse($urlEntity->validate());
        $this->assertTrue($urlEntity->hasErrors('url'));
        $this->assertContains($expectErrorMessage, $urlEntity->getErrors('url'));
    }

    /**
     * @param string $redirectToUrl
     * @param string $expectErrorMessage
     * @dataProvider notValidRedirectTUrlDataProvider
     */
    public function testRedirectToUrlNotValid(string $redirectToUrl, string $expectErrorMessage)
    {
        $urlEntity = new UrlEntity();
        $urlEntity->setAttribute('redirect_to_url', $redirectToUrl);
        $this->assertFalse($urlEntity->validate());
        $this->assertTrue($urlEntity->hasErrors('redirect_to_url'));
        $this->assertContains($expectErrorMessage, $urlEntity->getErrors('redirect_to_url'));
    }

    /**
     * @param string $fieldName
     * @param string $value
     * @param string $expectErrorMessage
     * @dataProvider notValidFieldsDataProvider
     */
    public function testFieldNotValid(string $fieldName, string $value, string $expectErrorMessage)
    {
        $urlEntity = new UrlEntity();
        $urlEntity->setAttribute($fieldName, $value);
        $this->assertFalse($urlEntity->validate());
        $this->assertTrue($urlEntity->hasErrors($fieldName));
        $this->assertContains($expectErrorMessage, $urlEntity->getErrors($fieldName));
    }

    public function validUrlEntityDataProvider()
    {
        $fixtures = include codecept_data_dir() . 'url_data.php';
        $fixtures = array_map(function ($array) {
            $list = [];
            unset($array['created_at'], $array['updated_at']);
            array_push($list, $array);
            return $list;
        }, $fixtures);
        return $fixtures;
    }

    public function notValidUrlDataProvider()
    {
        return [
            'Url > cannot be blank.' => [
                '',
                'Url cannot be blank.'
            ],
            'Url > contain more then 255 characters' => [
                '/some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url.html',
                'Url should contain at most 255 characters.'
            ],
        ];
    }

    public function notValidRedirectTUrlDataProvider()
    {
        return [
            'Redirect To Url > contain more then 255 characters' => [
                '/some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url.html',
                'Redirect To Url should contain at most 255 characters.'
            ],
        ];
    }

    public function notValidFieldsDataProvider()
    {
        return [
            'Module Name > contain more then 45 characters' => [
                'module_name',
                '/some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url.html',
                'Module Name should contain at most 45 characters.'
            ],
            'Controller Name > cannot be blank.' => [
                'controller_name',
                '',
                'Controller Name cannot be blank.'
            ],
            'Controller Name > contain more then 45 characters' => [
                'controller_name',
                '/some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url.html',
                'Controller Name should contain at most 45 characters.'
            ],
            'Action Name > cannot be blank.' => [
                'action_name',
                '',
                'Action Name cannot be blank.'
            ],
            'Action Name > contain more then 45 characters' => [
                'action_name',
                '/some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url.html',
                'Action Name should contain at most 45 characters.'
            ],
            'Entity ID > cannot be blank.' => [
                'entity_id',
                '',
                'Entity ID cannot be blank.'
            ],
            'Entity ID > contain more then 45 characters' => [
                'entity_id',
                '/some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url-some-long-url.html',
                'Entity ID should contain at most 45 characters.'
            ],
        ];
    }
}