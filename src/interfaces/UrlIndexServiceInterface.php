<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\interfaces;

use yii\db\Connection;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlCreateForm;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\repositories\UrlModuleRepository;

interface UrlIndexServiceInterface
{
    public function __construct(
        UrlRepositoryInterface $urlRepository,
        UrlModuleRepository $moduleRepository,
        Connection $db = null
    );

    public function addUrl(UrlCreateForm $urlCreateForm): UrlEntity;

    public function updateUrl(UrlUpdateForm $urlUpdateForm): UrlUpdateForm;

    public function removeUrl(string $url): void;
}
