<?php
declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\controllers\backend;

use yii\web\Controller;

class UrlController extends Controller
{
    public function actionIndex()
    {
        return $this->renderContent('Hello url index.');
    }
}