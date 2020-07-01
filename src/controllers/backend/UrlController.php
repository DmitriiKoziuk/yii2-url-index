<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\controllers\backend;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlCreateForm;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlSearchForm;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlIndexServiceInterface;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\exceptions\forms\UrlUpdateFormNotValidException;

/**
 * FileController implements the CRUD actions for UrlIndexEntity model.
 */
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
        $urlSearchForm = new UrlSearchForm();
        $urlSearchForm->load(Yii::$app->request->queryParams);
        $dataProvider = new ActiveDataProvider([
            'query' => $this->urlRepository->urlSearchQueryBuilder($urlSearchForm),
        ]);

        return $this->render('index', [
            'searchModel' => $urlSearchForm,
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
        $model = $this->urlRepository->getById($id);
        if (empty($model)) {
            throw new NotFoundHttpException("Url with id '{$id}' not found.");
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * @return string|Response
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
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id)
    {
        $urlEntity = $this->urlRepository->getById($id);
        if (empty($urlEntity)) {
            throw new NotFoundHttpException("Url with id '{$id}' not found.");
        }
        $urlUpdateForm = new UrlUpdateForm();
        $this->loadDataToUrlUpdateFormFromUrlEntity($urlUpdateForm, $urlEntity);
        $this->updateUrl($urlUpdateForm);
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
        $url = $this->urlRepository->getById($id);
        if (empty($url)) {
            throw new NotFoundHttpException("Url with id '{$id}' not found.");
        }
        $this->urlIndexService->removeUrl($url['url']);
        return $this->redirect(['index']);
    }

    private function loadDataToUrlUpdateFormFromUrlEntity(UrlUpdateForm $form, UrlEntity $entity)
    {
        $form->setAttributes($entity->getAttributes(null, [
            'module_id',
            'created_at',
            'updated_at',
        ]));
        $form->setAttributes($entity->moduleEntity->getAttributes(null, [
            'id',
            'created_at',
            'updated_at',
        ]));
    }

    private function updateUrl(UrlUpdateForm $form)
    {
        if (
            Yii::$app->request->isPost &&
            $form->load(Yii::$app->request->post())
        ) {
            try {
                if (! $form->validate()) {
                    throw new UrlUpdateFormNotValidException();
                }
                $this->urlIndexService->updateUrl($form);
                return $this->redirect(['view', 'id' => $form->id]);
            } catch (UrlUpdateFormNotValidException $e) {
                Yii::info($e);
            } catch (\Throwable $e) {
                Yii::error($e);
            }
        }

        return false;
    }
}
