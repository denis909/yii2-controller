<?php

namespace denis909\yii;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use denis909\yii\Assert;
use yii\base\Model;

class Controller extends \yii\web\Controller
{

    public $userComponent = 'user';

    public $modelClass;

    public $postActions = [];

    public $roles = [];

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

        Assert::notEmpty($modelClass);

        Assert::notEmpty($id);
            
        $model = $modelClass::findOne($id);
        
        if (!$model)
        {
            $this->throwNotFoundHttpException();
        }

        return $model;
    }

    public function createModel($className)
    {
        return $model = Yii::createObject($className);
    }

    public function loadModel(Model $model, array $data = [])
    {
        return $model->load($data);
    }

    public function saveModel(Model $model, bool $validate = true, array $attributes = null)
    {
        return $model->save($validate, $attributes);
    }

    public function validateModel(Model $model, $attributes = null)
    {
        return $model->validate($attributes);
    }

    public function deleteModel(Model $model)
    {
        return $model->delete();
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