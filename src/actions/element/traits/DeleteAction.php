<?php

namespace flipbox\spark\actions\element\traits;

use Craft;
use yii\base\Model;

trait DeleteAction
{
    /**
     * @param Model $model
     * @return bool
     */
    protected function performAction(Model $model): bool
    {
        return Craft::$app->getElements()->deleteElement($model);
    }
}
