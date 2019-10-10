<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\components;

use yii\base\BaseObject;
use yii\web\UrlRuleInterface;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlIndexServiceInterface;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2UrlIndex\services\UrlIndexService;

class UrlRule extends BaseObject implements UrlRuleInterface
{
    /**
     * @var UrlIndexServiceInterface
     */
    private $urlIndexService;

    public function __construct(
        UrlIndexService $urlIndexService,
        $config = []
    ) {
        parent::__construct($config);
        $this->urlIndexService = $urlIndexService;
    }

    public function parseRequest($manager, $request)
    {
        $url = $this->getUrl($request->getUrl(), $request->isGet);
        if (is_null($url)) {
            return false;
        }
        $route = ! empty($url->module_name) ? $url->module_name . '/' : '';
        $route .= $url->controller_name . '/' . $url->action_name;
        return [
            $route,
            [
                'url' => $url,
            ]
        ];
    }

    public function createUrl($manager, $route, $params)
    {
        return false;
    }

    /**
     * @param string $requestedUrl
     * @param bool $isGetParamsArePresent use yii\web\Request->isGuest
     * @return UrlUpdateForm|null
     */
    public function getUrl(string $requestedUrl, bool $isGetParamsArePresent): ?UrlUpdateForm
    {
        $url = $this->urlIndexService->getUrlByUrl($requestedUrl);
        if (is_null($url) && $isGetParamsArePresent) {
            $url = $this->urlIndexService
                ->getUrlByUrl($this->cutOutGetParamsFromUrl($requestedUrl));
        }
        return $url;
    }

    /**
     * Return url without get params (?param1=value1&param2=value2).
     * @param string $url
     * @return string
     */
    public function cutOutGetParamsFromUrl(string $url): string
    {
        $getParamsStartPosition = mb_strpos($url, '?');
        if (false !== $getParamsStartPosition) {
            $url = mb_substr($url, 0, $getParamsStartPosition);
        }
        return $url;
    }
}