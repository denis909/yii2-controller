<?php

namespace denis909\yii;

use Yii;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;

class Controller extends \yii\web\Controller
{

    public function throwNotFoundHttpException($message = 'Page not found.', $class = NotFoundHttpException::class)
    {
        throw Yii::createObject($class, [$message]);
    }

    public function findModel($id, $modelClass = null)
    {
        if (!$modelClass)
        {
            $modelClass = $this->modelClass;
        }
            
        $model = $modelClass::findOne($id);
        
        if (!$model)
        {
            $this->throwNotFoundHttpException();
        }

        return $model;
    }

    public function redirectBack($defaultReturnUrl = null)
    {
        $returnUrl = Yii::$app->request->get('returnUrl');

        if (!$returnUrl || !Url::isRelative($returnUrl))
        {
            if ($defaultReturnUrl)
            {
                $returnUrl = $defaultReturnUrl;
            }
            else
            {
                $returnUrl = [$this->defaultAction];
            }
        }

        return $this->redirect($returnUrl);
    }

}