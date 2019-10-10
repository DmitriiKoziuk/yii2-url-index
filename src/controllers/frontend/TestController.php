<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\controllers\frontend;

use yii\web\Controller;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;

class TestController extends Controller
{
    public function actionIndex(UrlUpdateForm $url)
    {
        return $this->render('index', [
            'url' => $url,
        ]);
    }
}