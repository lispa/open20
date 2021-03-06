<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core\rules
 * @category   CategoryName
 */

namespace lispa\amos\core\rules;

use lispa\amos\core\record\Record;
use Yii;
use yii\rbac\Rule;

/**
 * Class ValidatorUpdateContentRule
 * @package lispa\amos\core\rules
 */
class ValidatorUpdateContentRule extends Rule
{
    /**
     * @inheritdoc
     */
    public $name = 'validatorUpdateContent';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        if (isset($params['model'])) {
            /** @var Record $model */
            $model = $params['model'];
            $modelClassName = $model->className();
            $cwhModule = Yii::$app->getModule('cwh');

            if (!$model->id) {
                $post = \Yii::$app->getRequest()->post();
                $get = \Yii::$app->getRequest()->get();
                if (isset($get['id'])) {
                    $model = $this->instanceModel($model, $get['id']);
                } elseif (isset($post['id'])) {
                    $model = $this->instanceModel($model, $post['id']);
                }
            }

            if (!isset($cwhModule) || !in_array($modelClassName, $cwhModule->modelsEnabled)) {
                return true;
            } else {
                return $this->validatorContentUpdatePermission($model);
            }
        } else {
            return false;
        }
    }

    /**
     * @param Record $model
     * @return bool
     */
    private function validatorContentUpdatePermission($model)
    {
        $cwhActiveQuery = new \lispa\amos\cwh\query\CwhActiveQuery(
            $model->className(), [
            'queryBase' => $model::find()->distinct()
        ]);
        $queryToValidateIds = $cwhActiveQuery->getQueryCwhToValidate(false)->select($model::tableName().'.id')->column();

        return (in_array($model->id, $queryToValidateIds));
    }

    /**
     * @param Record $model
     * @param int $modelId
     * @return mixed
     */
    private function instanceModel($model, $modelId)
    {
        $modelClass = $model->className();
        /** @var Record $modelClass */
        $instancedModel = $modelClass::findOne($modelId);
        if (!is_null($instancedModel)) {
            $model = $instancedModel;
        }
        return $model;
    }
}
