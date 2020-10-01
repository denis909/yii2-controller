<?php

namespace denis909\yii;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;

class Controller extends \yii\web\Controller
{

    public $userComponent = 'user';

    public $postActions = [];

    public $roles = [];

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
        }

        $return['access']['user'] = $this->userComponent;

        return $return;
    }

}