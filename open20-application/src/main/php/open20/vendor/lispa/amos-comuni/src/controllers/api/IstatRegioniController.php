<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\comuni
 * @category   CategoryName
 */

namespace lispa\amos\comuni\controllers\api;

/**
 * This is the class for REST controller "IstatRegioniController".
 */

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class IstatRegioniController extends \yii\rest\ActiveController
{
    public $modelClass = 'lispa\amos\comuni\models\IstatRegioni';
}
