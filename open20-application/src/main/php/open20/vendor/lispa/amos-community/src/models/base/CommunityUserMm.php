<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community
 * @category   CategoryName
 */

namespace lispa\amos\community\models\base;

use lispa\amos\community\AmosCommunity;

/**
 * Class CommunityUserMm
 *
 * This is the base-model class for table "community_user_mm".
 *
 * @property integer $id
 * @property integer $community_id
 * @property integer $user_id
 * @property string $status
 * @property string $role
 * @property integer $access_to_community
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 * @property \lispa\amos\community\models\Community $community
 * @property \lispa\amos\core\user\User $user
 *
 * @package backend\modules\corsi\models\base
 */
class CommunityUserMm extends \lispa\amos\core\record\Record {

    /**
     * constants for role and status of community members
     */
    const ROLE_COMMUNITY_MANAGER = 'COMMUNITY_MANAGER';
    const ROLE_PARTICIPANT = 'PARTICIPANT';
    const ROLE_READER = 'READER';
    const ROLE_AUTHOR = 'AUTHOR';
    const ROLE_EDITOR = 'EDITOR';
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_WAITING_OK_COMMUNITY_MANAGER = 'REQUEST_SENT';
    const STATUS_WAITING_OK_USER = 'INVITED';
    const STATUS_INVITE_IN_PROGRESS = 'INVITING';
    const STATUS_MANAGER_TO_CONFIRM = 'MANAGER_TO_CONFIRM';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'community_user_mm';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['community_id', 'user_id', 'status', 'role'], 'required'],
            [['access_to_community','community_id', 'user_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['status', 'role'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['community_id'], 'exist', 'skipOnError' => true, 'targetClass' => \lispa\amos\community\models\Community::className(), 'targetAttribute' => ['community_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \lispa\amos\core\user\User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => AmosCommunity::t('amoscommunity', 'ID'),
            'community_id' => AmosCommunity::t('amoscommunity', 'Community ID'),
            'user_id' => AmosCommunity::t('amoscommunity', 'User ID'),
            'status' => AmosCommunity::t('amoscommunity', 'Status'),
            'role' => AmosCommunity::t('amoscommunity', 'Role'),
            'created_at' => AmosCommunity::t('amoscommunity', 'Created at'),
            'updated_at' => AmosCommunity::t('amoscommunity', 'Updated at'),
            'deleted_at' => AmosCommunity::t('amoscommunity', 'Deleted at'),
            'created_by' => AmosCommunity::t('amoscommunity', 'Created by'),
            'updated_by' => AmosCommunity::t('amoscommunity', 'Updated by'),
            'deleted_by' => AmosCommunity::t('amoscommunity', 'Deleted by'),
        ];
    }

    /**
     * Return all states
     * @return array States of the community user
     */
    public static function getUserStates() {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_REJECTED,
            self::STATUS_WAITING_OK_COMMUNITY_MANAGER,
            self::STATUS_WAITING_OK_USER,
            self::STATUS_INVITE_IN_PROGRESS,
            self::STATUS_MANAGER_TO_CONFIRM
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommunity() {
        return $this->hasOne(\lispa\amos\community\models\Community::className(), ['id' => 'community_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(\lispa\amos\core\user\User::className(), ['id' => 'user_id']);
    }

}
