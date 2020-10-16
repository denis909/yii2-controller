<?php

namespace denis909\yii;

use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;

class Controller extends \yii\web\Controller
{

    public $userComponent = 'user';

    public $postActions = [];

    public $roles = [];

    public $access = [];

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