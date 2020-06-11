<?php

namespace denis909\yii;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use Webmozart\Assert\Assert;

class Controller extends \yii\web\Controller
{

    public $userComponent = 'user';

    public $modelClass;

    public $postActions = [];

    public $roles = [];

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

    public function goBack($defaultUrl = null)
    {
        return Yii::$app->getResponse()->redirect(Yii::$app->{$this->userComponent}->getReturnUrl($defaultUrl));
    }

    public function behaviors()
    {
        $return = [];

        if ($this->roles)
        {
            $return['access']['rules'] = [
                [
                    'allow' => true,
                    'roles' => $this->roles
                ]
            ];
        }

        if ($this->postActions)
        {
            foreach($this->postActions as $action)
            {
                $return['verbs']['actions'][$action][] = 'post';
            }
        }

        if (array_key_exists('verbs', $return))
        {
            $return['verbs']['class'] = VerbFilter::class;
        }

        if (array_key_exists('access', $return))
        {
            $return['access']['class'] = AccessControl::class;

            $return['access']['user'] = $this->userComponent;
        }

        return $return;
    }

}