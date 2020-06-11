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

    public $userComponent = 'user';

    public $modelClass;

    public $notFoundMessage = 'Page not found.';

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
            throw new NotFoundHttpException($this->notFoundMessage);
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

    /**
     * {@inheritdoc}
     */
    public function goBack($defaultUrl = null)
    {
        return Yii::$app->getResponse()->redirect(Yii::$app->{$this->userComponent}->getReturnUrl($defaultUrl));
    }    

}