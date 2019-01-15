<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\migrations
 * @category   CategoryName
 */

use lispa\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

/**
 * Class m170719_122922_permissions_community
 */
class m180118_114222_permissions_community_read_rule extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => \lispa\amos\community\rules\ReadCommunityRule::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di aggiornare il proprio profilo della community',
                'ruleName' => \lispa\amos\community\rules\ReadCommunityRule::className(),
                'parent' => ['COMMUNITY_READER','COMMUNITY_MEMBER','COMMUNITY_CREATOR']
            ],
            [
                'name' => 'COMMUNITY_READ',
                'update' => true,
                'newValues' => [
                    'addParents' => [\lispa\amos\community\rules\ReadCommunityRule::className()],
                    'removeParents' => ['COMMUNITY_READER', 'COMMUNITY_MEMBER','COMMUNITY_CREATOR']
                ]
            ]
        ];
    }
}
