<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\controllers\backend;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntitySearch;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlCreateForm;
use DmitriiKoziuk\yii2UrlIndex\services\UrlIndexService;

/**
 * FileController implements the CRUD actions for UrlIndexEntity model.
 */
class UrlController extends Controller
{
    /**
     * @var UrlIndexService
     */
    private $urlIndexService;

    public function __construct($id, $module, UrlIndexService $urlIndexService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->urlIndexService = $urlIndexService;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['GET', 'POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all UrlIndexEntity models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UrlEntitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UrlIndexEntity model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id)
    {
        $model = $this->urlIndexService->getUrlById($id);
        if (empty($model)) {
            throw new NotFoundHttpException("Url with id '{$id}' not found.");
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * @return string|\yii\web\Response
     * @throws \DmitriiKoziuk\yii2Base\exceptions\DataNotValidException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\ExternalComponentException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\InvalidFormException
     * @throws \DmitriiKoziuk\yii2UrlIndex\exceptions\UrlAlreadyExistException
     */
    public function actionCreate()
    {
        $createForm = new UrlCreateForm();

        if ($createForm->load(Yii::$app->request->post())) {
            $form = $this->urlIndexService->addUrl($createForm);
            return $this->redirect(['view', 'id' => $form->id]);
        }

        return $this->render('create', [
            'model' => $createForm,
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\DataNotValidException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\ExternalComponentException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\InvalidFormException
     */
    public function actionUpdate(int $id)
    {
        $urlUpdateForm = $this->urlIndexService->getUrlById($id);
        if (empty($urlUpdateForm)) {
            throw new NotFoundHttpException("Url with id '{$id}' not found.");
        }
        if ($urlUpdateForm->load(Yii::$app->request->post())) {
            $urlUpdateForm = $this->urlIndexService->updateUrl($urlUpdateForm);
            return $this->redirect(['view', 'id' => $urlUpdateForm->id]);
        }
        return $this->render('update', [
            'model' => $urlUpdateForm,
        ]);
    }

    /**
     * Deletes an existing UrlIndexEntity model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(int $id)
    {
        $url = $this->urlIndexService->getUrlById($id);
        if (empty($url)) {
            throw new NotFoundHttpException("Url with id '{$id}' not found.");
        }
        $this->urlIndexService->removeUrl($url['url']);
        return $this->redirect(['index']);
    }
}
