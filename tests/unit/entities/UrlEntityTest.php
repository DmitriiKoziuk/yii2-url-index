<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\unit\entities;

use Yii;
use yii\di\Container;
use Codeception\Test\Unit;
use Faker\Provider\Base;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UrlsFixture;
use DmitriiKoziuk\yii2UrlIndex\tests\UnitTester;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;

class UrlEntityTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
        return [
            'url' => [
                'class' => UrlsFixture::class,
                'dataFile' => codecept_data_dir() . 'url_data.php'
            ]
        ];
    }

    protected function _after()
    {
        Yii::$container = new Container();
    }

    /**
     * @param array $attributes
     * @dataProvider validDataProvider
     */
    public function testValid(array $attributes)
    {
        $urlEntity = new UrlEntity();
        $urlEntity->setAttributes($attributes);
        $v = $urlEntity->validate();
        $this->assertTrue($v);
    }

    public function testIsRedirectMethod()
    {
        $urlEntity = new UrlEntity();
        $this->assertFalse($urlEntity->isRedirect());
        $urlEntity->redirect_to_url = 1;
        $this->assertTrue($urlEntity->isRedirect());
    }

    /**
     * @param string $attributeName
     * @param mixed $attributeValue
     * @param string $attributeErrorMessage
     * @dataProvider notValidDataProvider
     */
    public function testNotValid(string $attributeName, $attributeValue, string $attributeErrorMessage)
    {
        $urlEntity = new UrlEntity();
        $urlEntity->setAttribute($attributeName, $attributeValue);
        $v = $urlEntity->validate();
        $this->assertFalse($v);
        $this->assertTrue($urlEntity->hasErrors($attributeName));
        $this->assertContains($attributeErrorMessage, $urlEntity->getErrors()[ $attributeName ]);
    }

    public function validDataProvider()
    {
        return [
            [
                [
                    'url' => '/some-valid-url.html',
                    'redirect_to_url' => null,
                    'module_name' => null,
                    'controller_name' => 'c',
                    'action_name' => 'a',
                    'entity_id' => '1',
                ],
            ],
            [
                [
                    'url' => '/some-valid-url.html',
                    'redirect_to_url' => null,
                    'module_name' => 'module_name',
                    'controller_name' => 'controller_name',
                    'action_name' => 'action_name',
                    'entity_id' => 'entity_id',
                ],
            ],
            [
                [
                    'url' => '/redirect-some-url.html',
                    'redirect_to_url' => 1,
                    'module_name' => 'module_name',
                    'controller_name' => 'controller_name',
                    'action_name' => 'action_name',
                    'entity_id' => 'entity_id',
                ],
            ]
        ];
    }

    public function notValidDataProvider()
    {
        /** @var array $fixtures */
        $fixtures = require codecept_data_dir() . 'url_data.php';
        $fixtureNumber = count($fixtures);
        return [
            'url blank' => [
                'url',
                '',
                'Url cannot be blank.',
            ],
            'url duplicate' => [
                'url',
                '/some-url.html',
                'Url "/some-url.html" has already been taken.',
            ],
            'url max length 255' => [
                'url',
                '/' . Base::lexify(str_repeat('?', 255)),
                'Url should contain at most 255 characters.',
            ],
            'redirect_to_url link to non exist url id' => [
                'redirect_to_url',
                $fixtureNumber + 1,
                'Redirect To Url is invalid.'
            ],
            'module_name max length 45' => [
                'module_name',
                Base::lexify(str_repeat('?', 46)),
                'Module Name should contain at most 45 characters.',
            ],
            'controller_name blank' => [
                'controller_name',
                '',
                'Controller Name cannot be blank.',
            ],
            'controller_name max length 45' => [
                'controller_name',
                Base::lexify(str_repeat('?', 255)),
                'Controller Name should contain at most 45 characters.',
            ],
            'action_name blank' => [
                'action_name',
                '',
                'Action Name cannot be blank.',
            ],
            'action_name max length 45' => [
                'action_name',
                Base::lexify(str_repeat('?', 255)),
                'Action Name should contain at most 45 characters.',
            ],
            'entity_id blank' => [
                'entity_id',
                '',
                'Entity ID cannot be blank.',
            ],
            'entity_id max length 45' => [
                'entity_id',
                Base::lexify(str_repeat('?', 255)),
                'Entity ID should contain at most 45 characters.',
            ],
        ];
    }
}