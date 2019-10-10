<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\controllers\frontend;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2UrlIndex\services\UrlIndexService;

class UrlController extends Controller
{
    /**
     * @var UrlIndexService
     */
    private $urlIndexService;

    public function __construct(
        $id,
        $module,
        UrlIndexService $urlIndexService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->urlIndexService = $urlIndexService;
    }

    public function actionRedirect(UrlUpdateForm $url)
    {
        $redirectTo = $this->urlIndexService->getUrlById($url->redirect_to_url);
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