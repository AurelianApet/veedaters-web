<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Product;
use common\models\PhotosMap;
use common\models\ProductSearch;
use common\models\Review;


/**
 * ManageFoodController implements the CRUD actions for Shop model.
 */
class ManageImageController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Shop models.
     * @return mixed
     */
    public function actionIndex()
    {
        
        $data_shop_approval['ProductSearch'] = ['isactive' => Product::$IN_ACTIVE];
        $data_shop_active['ProductSearch'] = ['isactive' => Product::$IS_ACTIVE];
        $data_shop_deactive['ProductSearch'] = ['isactive' => Product::$IN_ACTIVE];
        
        
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $dataProviderApproval = $searchModel->search($data_shop_approval);        
        $dataProviderActive = $searchModel->search($data_shop_active);
        $dataProviderDeactive = $searchModel->search($data_shop_deactive);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProviderApproval' => $dataProviderApproval,
            'dataProviderActive' => $dataProviderActive,
            'dataProviderDeactive' => $dataProviderDeactive,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Shop model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    { 
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Shop model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Shop();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->shop_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Shop model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->product_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
    
    
    public function actionProductUpdate($id)
    { 
        $model = $this->findModel($id);
        $model->validate();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return json_encode(['status' => $model->isactive,'msg' => 'Status changed successfully!']);
        } else {
            return json_encode(['status' => false,'msg' => 'Status not changed']);
        }
        
    }
    
    /**
     * Deletes an existing Shop model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    { 
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    
    public function actionDeleteProduct($id)
    { 
        $productReview = Review::find()->andWhere(['product_id' => $id])->one();
        if(!empty($productReview))
        {
            $productReview->delete();
        }
        
        $product_photos = PhotosMap::find()->andWhere(['item_id' => $id,'relationship' => REL_PRODUCT_PICTURE])->all();
        if(!empty($product_photos)){$product_photos->delete();}
        
        $product = Product::find()->andWhere(['product_id' => $id])->one();        
        if(!empty($product)){$product->delete();}                
        
        
        
        return $this->redirect(['index']);
    }
    
    
    
    public function actionDeliveryEligibility()
    {
        if(Yii::$app->request->post()['product_id'])
        {
            $model = Product::find()->andWhere(['product_id' => Yii::$app->request->post()['product_id']])->one();
            if(!empty($model))
            { 
                $model->product_shipping_status = Yii::$app->request->post()['status'];
                $model->save();
            }
            return json_encode(['status' => true,'msg' => 'Status changed']);
        }
        
    }
    
    
    public function actionDeliveryOption()
    {
        if(Yii::$app->request->post()['product_id'])
        {
            $model = Product::find()->andWhere(['product_id' => Yii::$app->request->post()['product_id']])->one();
            if(!empty($model))
            { 
                $model->product_delivery = Yii::$app->request->post()['status'];
                $model->save();
            }
            return json_encode(['status' => true,'msg' => 'Status changed']);
        }
        
    }
    
    public function actionDeliveryCharge()
    {
        if(Yii::$app->request->post()['town_id'])
        {
            $model = \common\models\Town::find()->andWhere(['town_id' => Yii::$app->request->post()['town_id']])->one();
            if(!empty($model))
            { 
                $model->delivery_charges = Yii::$app->request->post()['delivery_charges'];
                $model->save();
            }
            return json_encode(['status' => true,'msg' => 'Status changed']);
        }
        
    }
    
    

    /**
     * Finds the Shop model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Shop the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
