<?php

namespace denis909\yii;

use Yii;
use yii\web\NotFoundHttpException;

class Controller extends \yii\web\Controller
{

    public $notFoundHttpExceptionClass = NotFoundHttpException::class;

    public function findModel($id, $modelClass = null)
    {
        if (!$modelClass)
        {
            $modelClass = $this->modelClass;
        }
            
        $model = $modelClass::findOne($id);
        
        if (!$model)
        {
            throw Yii::createObject($this->notFoundHttpExceptionClass, ['The requested page does not exist.']);
        }

        return $model;
    }

}