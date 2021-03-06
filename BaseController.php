<?php

namespace denis909\yii;

use Yii;

use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;

abstract class BaseController extends \yii\web\Controller
{

    use ControllerTrait;

    public $userComponent = 'user';

    public $postActions = [];

    public $roles = [];

    public $access = [];

    public $modelClass;

    public function findModel($id, $modelClass = null)
    {
        if (!$modelClass)
        {
            $modelClass = $this->modelClass;
        }

        Assert::notEmpty($modelClass, __CLASS__ . '::modelClass');

        Assert::notEmpty($id);
            
        $model = $modelClass::findOne($id);
        
        if (!$model)
        {
            throw Yii::createObject($this->notFoundHttpExceptionClass, ['Page not found.']);
        }

        return $model;
    }    

    public function goBack($defaultUrl = null)
    {
        $returnUrl = Yii::$app->request->get('returnUrl');

        if ($returnUrl && Url::isRelative($returnUrl))
        {
            return $this->redirect($returnUrl);
        }

        if (!$defaultUrl)
        {
            $defaultUrl = Url::toRoute([$this->defaultAction]);
        }

        $returnUrl = Yii::$app->{$this->userComponent}->getReturnUrl($defaultUrl);

        $return = Yii::$app->getResponse()->redirect($returnUrl);
    
        Yii::$app->{$this->userComponent}->setReturnUrl(null);

        return $return;
    }

    public function behaviors()
    {
        $return = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => []
            ],
            'access' => ArrayHelper::merge(
                [
                    'class' => AccessControl::class,
                    'user' => $this->userComponent,
                    'rules' => [],
                    'only' => []
                ],
                $this->access
            )
        ];

        if ($this->roles)
        {
            $return['access']['rules'] = [
                [
                    'allow' => true,
                    'roles' => $this->roles
                ]
            ];
        }

        foreach($this->postActions as $action)
        {
            $return['verbs']['actions'][$action][] = 'POST';
        }        

        if (count($return['access']['rules']) == 0)
        {
            unset($return['access']);
        }

        if (count($return['verbs']['actions']) == 0)
        {
            unset($return['verbs']);
        }

        return $return;
    }

}