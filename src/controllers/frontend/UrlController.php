<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\controllers\frontend;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlIndexServiceInterface;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlRepositoryInterface;

class UrlController extends Controller
{
    private UrlIndexServiceInterface $urlIndexService;
    private UrlRepositoryInterface $urlRepository;

    public function __construct(
        $id,
        $module,
        UrlIndexServiceInterface $urlIndexService,
        UrlRepositoryInterface $urlRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->urlIndexService = $urlIndexService;
        $this->urlRepository = $urlRepository;
    }

    public function actionRedirect(UrlUpdateForm $url)
    {
        $redirectTo = $this->urlRepository->getById($url->redirect_to_url);
        if (is_null($redirectTo)) {
            throw new NotFoundHttpException();
        }
        return $this->redirect($redirectTo->url, $url->entity_id);
    }

    public function actionTest(UrlUpdateForm $url)
    {
        return $this->render('test', [
            'url' => $url,
        ]);
    }
}
