<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\myactivities\basic
 * @category   CategoryName
 */
namespace lispa\amos\myactivities\basic;

use lispa\amos\admin\models\UserProfile;
use lispa\amos\community\models\Community;

/**
 * Class CommunityToValidate
 * @package lispa\amos\myactivities\basic
 */
class CommunityToValidate extends \lispa\amos\community\models\search\CommunitySearch implements MyActivitiesModelsInterface
{

    public function getSearchString()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @return Community
     */
    public function getWrappedObject()
    {
        return Community::findOne($this->id);
    }

    /**
     * @return string
     */
    public function getCreatorNameSurname()
    {
        /** @var UserProfile $userProfile */
        $userProfile = UserProfile::find()->andWhere(['user_id' => $this->created_by])->one();
        if (!empty($userProfile)){
            return $userProfile->getNomeCognome();
        }
        return '';
    }

}