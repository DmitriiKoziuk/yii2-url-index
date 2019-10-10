<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests;

use Yii;
use yii\di\Container;
use DmitriiKoziuk\yii2UrlIndex\tests\_fixtures\UrlsFixture;
use DmitriiKoziuk\yii2UrlIndex\components\UrlRule;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;

class UrlRuleTest extends \Codeception\Test\Unit
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
     * @env frontend
     */
    public function testUrlRuleLoaded()
    {
        $this->assertNotEmpty($this->getUrlRule());
    }

    /**
     * @env frontend
     */
    public function testCutOutGetParamsFromUrl()
    {
        $urlRule = $this->getUrlRule();
        $this->assertEquals('/url', $urlRule->cutOutGetParamsFromUrl('/url?param=value'));
    }

    /**
     * @env frontend
     * @param string $url
     * @param bool $hasGetPart
     * @dataProvider urlInIndexWithGetParamsDataProvider
     */
    public function testUrlExistInIndexWithGetParams(string $url, bool $hasGetPart)
    {
        $urlRule = $this->getUrlRule();
        $this->tester->seeRecord(
            UrlEntity::class,
            ['url' => $url]
        );

        $returnData = $urlRule->getUrl($url, $hasGetPart);
        $this->assertNotEmpty($returnData);
        $this->assertInstanceOf(UrlUpdateForm::class, $returnData);
        $this->assertEquals($url, $returnData->url);
    }

    /**
     * @env frontend
     * @param string $url
     * @param bool $hasGetPart
     * @dataProvider urlInIndexWithoutGetParamsDataProvider
     */
    public function testUrlExistInIndexWithoutGetParams(string $url, bool $hasGetPart)
    {
        $urlRule = $this->getUrlRule();
        $this->tester->seeRecord(
            UrlEntity::class,
            ['url' => $urlRule->cutOutGetParamsFromUrl($url)]
        );

        $returnData = $urlRule->getUrl($url, $hasGetPart);
        $this->assertNotEmpty($returnData);
        $this->assertInstanceOf(UrlUpdateForm::class, $returnData);
        if (! $hasGetPart) {
            $this->assertEquals($url, $returnData->url);
        }
    }

    public function urlInIndexWithoutGetParamsDataProvider()
    {
        return [
            [
                'url' => '/some-url.html',
                'hasGetPart' => false,
            ],
            [
                'url' => '/some-url.html?param',
                'hasGetPart' => true,
            ],
            [
                'url' => '/some-url.html?param=',
                'hasGetPart' => true,
            ],
            [
                'url' => '/some-url.html?param=value',
                'hasGetPart' => true,
            ],
        ];
    }

    public function urlInIndexWithGetParamsDataProvider()
    {
        return [
            [
                'url' => '/some-url.html?params',
                'hasGetPart' => true,
            ],
            [
                'url' => '/some-url.html?params=value',
                'hasGetPart' => true,
            ],
        ];
    }

    protected function getUrlRule(): ?UrlRule
    {
        $rules = Yii::$app->getUrlManager()->rules;
        foreach ($rules as $rule) {
            if ($rule instanceof UrlRule) {
                return $rule;
            }
        }
        return null;
    }
}