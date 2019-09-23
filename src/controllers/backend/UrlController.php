<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\controllers\backend;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntitySearch;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlCreateForm;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2UrlIndex\services\UrlService;

/**
 * FileController implements the CRUD actions for UrlIndexEntity model.
 */
class UrlController extends Controller
{
    /**
     * @var UrlService
     */
    private $urlService;

    public function __construct($id, $module, UrlService $urlService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->urlService = $urlService;
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
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return string|\yii\web\Response
     * @throws \DmitriiKoziuk\yii2Base\exceptions\DataNotValidException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\ExternalComponentException
     * @throws \DmitriiKoziuk\yii2Base\exceptions\InvalidFormException
     */
    public function actionCreate()
    {
        $createForm = new UrlCreateForm();

        if ($createForm->load(Yii::$app->request->post())) {
            $form = $this->urlService->createUrl($createForm);
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
    public function actionUpdate($id)
    {
        $model = new UrlUpdateForm();
        $entity = $this->findModel($id);
        $model->setAttributes($entity->getAttributes());

        if ($model->load(Yii::$app->request->post())) {
            $model = $this->urlService->updateUrl($model);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UrlIndexEntity model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UrlIndexEntity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UrlEntity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UrlEntity::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
