<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace api\modules\v1\controllers;

/**
 * Description of ApiLogController
 *
 * @author dmlabs
 */
class ApiLogController extends ApiController {
    public function actionCreate() {
        $model = new \common\models\ApiLog();

        $model->load(\Yii::$app->request->post());
        if (!$model->save()) {
            return $this->error(["error" => $model->getErrors()]);
        }
        
        return $this->success(["log_id" => $model->id]);
    }

}
