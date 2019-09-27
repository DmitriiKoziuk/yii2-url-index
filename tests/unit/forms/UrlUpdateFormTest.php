<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\unit\forms;

use Yii;
use yii\di\Container;
use Faker\Provider\Base;
use Codeception\Test\Unit;
use DmitriiKoziuk\yii2UrlIndex\tests\UnitTester;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;

class UrlUpdateFormTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

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
        $form = new UrlUpdateForm($attributes);
        $v = $form->validate();
        $this->assertTrue($form->validate());
    }

    /**
     * @param string $attributeName
     * @param mixed $attributeValue
     * @param string $attributeErrorMessage
     * @dataProvider notValidDataProvider
     */
    public function testNotValid(string $attributeName, $attributeValue, string $attributeErrorMessage)
    {
        $form = new UrlUpdateForm([$attributeName => $attributeValue]);
        $this->assertFalse($form->validate());
        $this->assertTrue($form->hasErrors($attributeName));
        $this->assertContains($attributeErrorMessage, $form->getErrors()[ $attributeName ]);
    }

    public function validDataProvider()
    {
        return [
            [
                [
                    'id' => 1,
                    'url' => '/some-url.html',
                    'redirect_to_url' => NULL,
                    'module_name' => 'module',
                    'controller_name' => 'controller',
                    'action_name' => 'action',
                    'entity_id' => '1',
                    'created_at' => '1392559490',
                    'updated_at' => '1392559490',
                ],
            ],
            [
                [
                    'id' => 2,
                    'url' => '/some-url-2.html',
                    'redirect_to_url' => NULL,
                    'module_name' => 'module',
                    'controller_name' => 'controller',
                    'action_name' => 'action',
                    'entity_id' => '2',
                    'created_at' => '1392569490',
                    'updated_at' => '1392569490',
                ],
            ],
            [
                [
                    'id' => 3,
                    'url' => '/some-url-3.html',
                    'redirect_to_url' => 1,
                    'module_name' => 'dk-url-index',
                    'controller_name' => 'url',
                    'action_name' => 'redirect',
                    'entity_id' => '302',
                    'created_at' => '1392569490',
                    'updated_at' => '1392569490',
                ],
            ],
        ];
    }

    public function notValidDataProvider()
    {
        return [
            'id blank' => [
                'id',
                '',
                'Id cannot be blank.',
            ],
            'url blank' => [
                'url',
                '',
                'Url cannot be blank.',
            ],
            'url not start from "/"' => [
                'url',
                'not-valid-url.html',
                'Url must start from "/" character.',
            ],
            'url end by space' => [
                'url',
                '/not-valid-url.html ',
                'Url cant end by space.',
            ],
            'url max length 255' => [
                'url',
                '/' . Base::lexify(str_repeat('?', 255)),
                'Url should contain at most 255 characters.',
            ],
            'module_name max length 45' => [
                'module_name',
                Base::lexify(str_repeat('?', 46)),
                'Module Name should contain at most 45 characters.',
            ],
            'module_name contain space between words' => [
                'module_name',
                'some name',
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'module_name contain \n between words' => [
                'module_name',
                "some\nname",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'module_name contain \t between words' => [
                'module_name',
                "some\tname",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'module_name contain space at start' => [
                'module_name',
                " some_name",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'module_name contain \n at start' => [
                'module_name',
                "\nsome_name",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'module_name contain \t at start' => [
                'module_name',
                "\tsome_name",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'module_name contain space at end' => [
                'module_name',
                "some_name ",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'module_name contain \t at end' => [
                'module_name',
                "some_name\t",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
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
            'controller_name contain space between words' => [
                'controller_name',
                'some name',
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'controller_name contain \n between words' => [
                'controller_name',
                "some\nname",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'controller_name contain \t between words' => [
                'controller_name',
                "some\tname",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'controller_name contain space at start' => [
                'module_name',
                " some_name",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'controller_name contain \n at start' => [
                'module_name',
                "\nsome_name",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'controller_name contain \t at start' => [
                'module_name',
                "\tsome_name",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'controller_name contain space at end' => [
                'module_name',
                "some_name ",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'controller_name contain \t at end' => [
                'module_name',
                "some_name\t",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
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
            'action_name contain \n between words' => [
                'action_name',
                "some\nname",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'action_name contain \t between words' => [
                'action_name',
                "some\tname",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'action_name contain space at start' => [
                'action_name',
                " some_name",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'action_name contain \n at start' => [
                'action_name',
                "\nsome_name",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'action_name contain \t at start' => [
                'action_name',
                "\tsome_name",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'action_name contain space at end' => [
                'action_name',
                "some_name ",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'action_name contain \t at end' => [
                'action_name',
                "some_name\t",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'entity_id blank' => [
                'entity_id',
                '',
                'Entity Id cannot be blank.',
            ],
            'entity_id max length 45' => [
                'entity_id',
                Base::lexify(str_repeat('?', 255)),
                'Entity Id should contain at most 45 characters.',
            ],
            'entity_id contain \n between words' => [
                'entity_id',
                "some\nname",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'entity_id contain \t between words' => [
                'entity_id',
                "some\tname",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'entity_id contain space at start' => [
                'entity_id',
                " some_name",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'entity_id contain \n at start' => [
                'entity_id',
                "\nsome_name",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'entity_id contain \t at start' => [
                'entity_id',
                "\tsome_name",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'entity_id contain space at end' => [
                'entity_id',
                "some_name ",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
            'entity_id contain \t at end' => [
                'entity_id',
                "some_name\t",
                'Attribute must contain only: characters, digits, underscores and hyphen.',
            ],
        ];
    }
}