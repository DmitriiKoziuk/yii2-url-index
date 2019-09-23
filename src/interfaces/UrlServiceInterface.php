<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\interfaces;

use yii\db\Connection;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlCreateForm;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;

interface UrlServiceInterface
{
    public function __construct(
        UrlRepositoryInterface $urlRepository,
        Connection $db = null
    );

    public function createUrl(UrlCreateForm $urlCreateForm): UrlUpdateForm;

    public function updateUrl(UrlUpdateForm $urlUpdateForm): UrlUpdateForm;

    public function deleteUrl(string $url): void;

    public function getUrlById(int $id): ?UrlUpdateForm;
}