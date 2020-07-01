<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\components;

use yii\base\BaseObject;
use yii\web\UrlRuleInterface;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlIndexServiceInterface;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;

class UrlRule extends BaseObject implements UrlRuleInterface
{
    private UrlIndexServiceInterface $urlIndexService;

    private UrlRepositoryInterface $urlRepository;

    public function __construct(
        UrlIndexServiceInterface $urlIndexService,
        UrlRepositoryInterface $urlRepository,
        $config = []
    ) {
        parent::__construct($config);
        $this->urlIndexService = $urlIndexService;
        $this->urlRepository = $urlRepository;
    }

    public function parseRequest($manager, $request)
    {
        $urlEntity = $this->getUrl($request->getUrl(), $request->isGet);
        if (is_null($urlEntity)) {
            return false;
        }
        $route = ! empty($urlEntity->moduleEntity->module_name) ? $urlEntity->moduleEntity->module_name . '/' : '';
        $route .= $urlEntity->moduleEntity->controller_name . '/' . $urlEntity->moduleEntity->action_name;
        return [
            $route,
            [
                'url' => $urlEntity,
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
    public function getUrl(string $requestedUrl, bool $isGetParamsArePresent): ?UrlEntity
    {
        $urlEntity = $this->urlRepository->getByUrl($requestedUrl);
        if (is_null($urlEntity) && $isGetParamsArePresent) {
            $urlEntity = $this->urlRepository
                ->getByUrl($this->cutOutGetParamsFromUrl($requestedUrl));
        }
        return $urlEntity;
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
