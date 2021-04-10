<?php

namespace denis909\yii;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

trait ControllerTrait
{

    public $notFoundExceptionClass = NotFoundHttpException::class;

    public $forbiddenExceptionClass = ForbiddenHttpException::class;

    public function throwNotFoundException(string $name = 'Page not found.')
    {
        throw Yii::createObject($this->notFoundExceptionClass, [$name]);
    }

    public function throwForbiddenException(string $name = 'Access denied.')
    {
        throw Yii::createObject($this->forbiddenExceptionClass, [$name]);
    }

}