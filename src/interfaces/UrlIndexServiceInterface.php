<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\interfaces;

use yii\db\Connection;
use yii\data\ActiveDataProvider;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlCreateForm;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlSearchForm;

interface UrlIndexServiceInterface
{
    public function __construct(
        UrlRepositoryInterface $urlRepository,
        Connection $db = null
    );

    public function addUrl(UrlCreateForm $urlCreateForm): UrlUpdateForm;

    public function updateUrl(UrlUpdateForm $urlUpdateForm): UrlUpdateForm;

    public function removeUrl(string $url): void;

    public function getUrlById(int $id): ?UrlUpdateForm;

    public function isUrlExist(string $url): bool;

    public function search(UrlSearchForm $urlSearchForm): ActiveDataProvider;
}