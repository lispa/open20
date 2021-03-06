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
use lispa\amos\community\exceptions\CommunityException;
use lispa\amos\core\record\Record;
use lispa\amos\core\user\User;
use lispa\amos\cwh\models\CwhAuthAssignment;
use lispa\amos\cwh\models\CwhPubblicazioniCwhNodiEditoriMm;
use lispa\amos\cwh\models\CwhPubblicazioniCwhNodiValidatoriMm;
use lispa\amos\cwh\utility\CwhUtil;
use lispa\amos\notificationmanager\record\NotifyRecord;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Class Community
 *
 * This is the base-model class for table "community".
 *
 * @property integer $id
 * @property string $status
 * @property string $name
 * @property string $description
 * @property integer $hits
 * @property string $logo_id
 * @property string $cover_image_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 * @property integer $community_type_id
 * @property integer $validated_once
 * @property integer $visible_on_edit
 * @property string $context
 * @property integer $parent_id
 *
 * @property \lispa\amos\community\models\CommunityType $communityType
 * @property \lispa\amos\core\user\User $createdByUser
 * @property \lispa\amos\core\user\User $updatedByUser
 * @property \lispa\amos\core\user\User[] $communityUsers
 * @property \lispa\amos\community\models\CommunityUserMm[] $communityUserMms
 * @property \lispa\amos\community\models\Community[] $subcommunities
 *
 * @package lispa\amos\community\models\base
 */
class Community extends NotifyRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'community';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $requiredArray = AmosCommunity::getInstance()->communityRequiredFields;

        return [
            [$requiredArray, 'required'],
            [['description'], 'string' /*, 'max' => 500*/],
            [['context'], 'string'],
            [['logo_id', 'cover_image_id'], 'number'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['created_by', 'updated_by', 'deleted_by', 'community_type_id', 'validated_once', 'visible_on_edit', 'parent_id', 'hits'], 'integer'],
            [['status', 'name'], 'string', 'max' => 255],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'id' => AmosCommunity::t('amoscommunity', 'ID'),
            'status' => AmosCommunity::t('amoscommunity', 'Status'),
            'name' => AmosCommunity::t('amoscommunity', '#name'),
            'description' => AmosCommunity::t('amoscommunity', 'Description'),
            'logo_id' => AmosCommunity::t('amoscommunity', 'Logo'),
            'cover_image_id' => AmosCommunity::t('amoscommunity', 'Cover image'),
            'created_at' => AmosCommunity::t('amoscommunity', 'Created at'),
            'updated_at' => AmosCommunity::t('amoscommunity', 'Updated at'),
            'deleted_at' => AmosCommunity::t('amoscommunity', 'Deleted at'),
            'created_by' => AmosCommunity::t('amoscommunity', 'Created by'),
            'updated_by' => AmosCommunity::t('amoscommunity', 'Updated by'),
            'deleted_by' => AmosCommunity::t('amoscommunity', 'Deleted by'),
            
            'communityType' => AmosCommunity::t('amoscommunity', 'Access type'),
            'community_type_id' => AmosCommunity::t('amoscommunity', 'Community type id'),
            
            'validated_once' => AmosCommunity::t('amoscommunity', 'Validated at least once'),
            'visible_on_edit' => AmosCommunity::t('amoscommunity', 'Visible while editing'),
            'context' => AmosCommunity::t('amoscommunity', 'Context'),
            'parent_id' => AmosCommunity::t('amoscommunity', 'Parent id'),
        ]);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommunityUserMms()
    {
        return $this->hasMany(\lispa\amos\community\models\CommunityUserMm::className(), ['community_id' => 'id'])->inverseOf('community')
            ->from([CommunityUserMm::tableName() => CommunityUserMm::tableName()])
            ->innerJoin(User::tableName() . ' usr1', CommunityUserMm::tableName() . '.user_id = usr1.id and usr1.deleted_at IS NULL')
            ->innerJoin('user_profile', 'usr1.id = user_profile.user_id')
            ->andWhere(['user_profile.attivo' => 1]);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommunityType()
    {
        return $this->hasOne(\lispa\amos\community\models\CommunityType::className(), ['id' => 'community_type_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommunityUsers()
    {
        return $this->hasMany(\lispa\amos\core\user\User::className(), ['id' => 'user_id'])->via('communityUserMms');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedByUser()
    {
        return $this->hasOne(\lispa\amos\core\user\User::className(), ['id' => 'created_by']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedByUser()
    {
        return $this->hasOne(\lispa\amos\core\user\User::className(), ['id' => 'updated_by']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubcommunities()
    {
        return $this->hasMany(\lispa\amos\community\models\Community::className(), ['parent_id' => 'id'])->andWhere(['context' => self::className()]);
    }
    
    /**
     * @return string the name of the community type
     */
    public function getCommunityTypeName()
    {
        return $this->communityType->name;
    }

    /**
     * Before deleting the community, deletion of related records:
     * - subcommunities if present (and releted records)
     * - contents published for the community only (news, documents, ..)
     * - associations community-users
     * - CwhAuthAssignment permission for the community
     *
     * @return bool
     * @throws CommunityException
     */
    public function beforeDelete()
    {
        set_time_limit(3600);
        try {
            foreach ($this->subcommunities as $subcommunity) {
                $subcommunity->delete();
            }
        } catch (Exception $exception) {
            throw new CommunityException(AmosCommunity::t('amoscommunity','#delete_community_delete_subcommunities_error'));
        }

        $cwhConfigId = \lispa\amos\community\models\Community::getCwhConfigId();

        try {
            CwhUtil::deleteNetworkContents($cwhConfigId, $this->id);
        } catch(Exception $exception){
            throw new CommunityException(AmosCommunity::t('amoscommunity', '#delete_community_delete_contents_error'));
        }

        try {
            foreach ($this->communityUserMms as $communityUserMm){
                $communityUserMm->delete();
            }

            $cwhPermissions = CwhAuthAssignment::find()->andWhere([
                'cwh_config_id' => $cwhConfigId,
                'cwh_network_id' => $this->id
            ])->all();

            /** @var CwhAuthAssignment $cwhPermission */
            foreach ($cwhPermissions as $cwhPermission) {
                $cwhPermission->delete();
            }
        } catch(Exception $exception){
            throw new CommunityException(AmosCommunity::t('amoscommunity', '#delete_community_delete_members_permission_error'));
        }

        header_remove('Set-Cookie');
        return parent::beforeDelete();
    }
}
