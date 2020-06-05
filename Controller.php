<?php

namespace denis909\yii;

use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use Webmozart\Assert\Assert;
use denis909\yii\ModelException;

class Controller extends \yii\web\Controller
{

    public $notFoundHttpExceptionClass = NotFoundHttpException::class;

    public $modelClass;

    public $assertClass = Assert::class;

    public $modelExceptionClass = ModelException::class;

    public function findModel($id, $modelClass = null)
    {
        $assertClass = $this->assertClass;

        if (!$modelClass)
        {
            $modelClass = $this->modelClass;
        }

        $assertClass::notEmpty($modelClass);

        $assertClass::notEmpty($id);
            
        $model = $modelClass::findOne($id);
        
        if (!$model)
        {
            throw Yii::createObject($this->notFoundHttpExceptionClass, ['The requested page does not exist.']);
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

}