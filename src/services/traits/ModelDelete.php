<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services\traits;

use flipbox\spark\helpers\RecordHelper;
use flipbox\spark\models\Model;
use flipbox\spark\records\Record;
use yii\base\ModelEvent;

/**
 * @package flipbox\spark\services\traits
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait ModelDelete
{

    /*******************************************
     * ABSTRACTS
     *******************************************/

    /**
     * @param int $id
     * @param string|null $toScenario
     * @return Record
     */
    abstract public function getRecordById(int $id, string $toScenario = null): Record;


    /*******************************************
     * DELETE
     *******************************************/

    /**
     * @param Model $model
     * @return bool
     * @throws \Exception
     */
    public function delete(Model $model): bool
    {

        // The event to trigger
        $event = new ModelEvent();

        // Db transaction
        $transaction = RecordHelper::beginTransaction();

        try {

            // The 'before' event
            if (!$model->beforeDelete($event)) {

                $transaction->rollBack();

                return false;
            }

            // Get record
            $record = $this->getRecordById($model->id);

            // Insert record
            if (!$record->delete()) {

                // Transfer errors to model
                $model->addErrors($record->getErrors());

                // Roll back db transaction
                $transaction->rollBack();

                return false;

            }

            // The 'after' event
            if (!$model->afterDelete($event)) {

                // Roll back db transaction
                $transaction->rollBack();

                return false;

            }

        } catch (\Exception $e) {

            // Roll back all db actions (fail)
            $transaction->rollback();

            throw $e;

        }

        $transaction->commit();

        return true;

    }

}
