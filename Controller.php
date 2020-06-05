<?php

namespace denis909\yii;

use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use Webmozart\Assert\Assert;
use denis909\yii\ModelException;

class Controller extends \yii\web\Controller
{

    public $modelClass;

    public $modelExceptionClass = ModelException::class;

    public $notFoundHttpExceptionClass = NotFoundHttpException::class;

    public $forbiddenHttpExceptionClass = ForbiddenHttpException::class;

    public function findModel($id, $modelClass = null)
    {
        if (!$modelClass)
        {
            $modelClass = $this->modelClass;
        }

        Assert::notEmpty($modelClass);

        Assert::notEmpty($id);
            
        $model = $modelClass::findOne($id);
        
        if (!$model)
        {
            $this->throwNewFoundHttpException();
        }

        return $model;
    }

    public function redirectBack($default = null)
    {
        $returnUrl = Yii::$app->request->get('returnUrl');

        if (!$returnUrl || !Url::isRelative($returnUrl))
        {
            if ($default)
            {
                $returnUrl = $default;
            }
            else
            {
                $returnUrl = [$this->defaultAction];
            }
        }

        return $this->redirect($returnUrl);
    }

    public function throwModelException($model)
    {
        throw Yii::createObject($this->modelExceptionClass, [$model]);
    }

    public function throwNotFoundHttpException($message = 'The requested page does not exist.')
    {
        throw Yii::createObject($this->notFoundHttpExceptionClass, [$message]);
    }

    public function throwForbiddenHttpException($message = 'Access denied.')
    {
        throw Yii::createObject($this->forbiddenHttpExceptionClass, [$message]);
    }    

}