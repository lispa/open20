<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community
 * @category   CategoryName
 */

namespace lispa\amos\community\widgets;

use lispa\amos\admin\widgets\UserCardWidget;
use lispa\amos\community\AmosCommunity;
use lispa\amos\community\models\Community;
use lispa\amos\community\models\CommunityUserMm;
use lispa\amos\community\utilities\CommunityUtil;
use lispa\amos\core\forms\editors\Select;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\utilities\JsUtility;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\bootstrap\Modal;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\widgets\PjaxAsset;

/**
 * Class CommunityMembersWidget
 * @package lispa\amos\community\widgets
 */
class CommunityMembersWidget extends Widget
{
    /**
     * @var Community $model
     */
    public $model = null;
    
    /**
     * (eg. ['PARTICIPANT'] - thw widget will show only member with role participant)
     * @var array Array of roles to show
     */
    public $showRoles = null;
    
    /**
     * @var bool $showAdditionalAssociateButton Set to true if another 'invite user' button is required
     */
    public $showAdditionalAssociateButton = false;

    /**
     * @var array $additionalColumns Additional Columns
     */
    public $additionalColumns = [];
    
    /**
     * @var bool $viewEmail
     */
    public $viewEmail = false;
    
    /**
     * @var bool $checkManagerRole
     */
    public $checkManagerRole = true;
    
    /**
     * @var string $addPermission
     */
    public $addPermission = 'COMMUNITY_UPDATE';
    
    /**
     * @var string $manageAttributesPermission
     */
    public $manageAttributesPermission = 'COMMUNITY_UPDATE';
    
    /**
     * @var bool $forceActionColumns
     */
    public $forceActionColumns = false;
    
    /**
     * @var string $actionColumnsTemplate
     */
    public $actionColumnsTemplate = '';
    
    /**
     * @var bool $viewM2MWidgetGenericSearch
     */
    public $viewM2MWidgetGenericSearch = false;
    
    /**
     * @var array $targetUrlParams
     */
    public $targetUrlParams = null;

    /**
     * @var string $gridId
     */
    public $gridId = 'community-members-grid';
    
    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!$this->model) {
            throw new InvalidConfigException($this->throwErrorMessage('model'));
        }
    }
    
    protected function throwErrorMessage($field)
    {
        return AmosCommunity::t('amoscommunity', 'Wrong widget configuration: missing field {field}', [
            'field' => $field
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        
        $customInvitationForm = AmosCommunity::instance()->customInvitationForm;
        $inviteUserOfcommunityParent = AmosCommunity::instance()->inviteUserOfcommunityParent;


        $gridId = $this->gridId . (!empty($this->showRoles) ? '-'.implode('-', $this->showRoles) : '');
        $model = $this->model;
        $params = [];
        $params['showRoles'] = $this->showRoles;
        $params['showAdditionalAssociateButton'] = $this->showAdditionalAssociateButton;
        $params['additionalColumns'] = $this->additionalColumns;
        $params['viewEmail'] = $this->viewEmail;
        $params['checkManagerRole'] = $this->checkManagerRole;
        $params['addPermission'] = $this->addPermission;
        $params['manageAttributesPermission'] = $this->manageAttributesPermission;
        $params['forceActionColumns'] = $this->forceActionColumns;
        $params['actionColumnsTemplate'] = $this->actionColumnsTemplate;
        $params['viewM2MWidgetGenericSearch'] = $this->viewM2MWidgetGenericSearch;
        $params['targetUrlParams'] = $this->targetUrlParams;
        
        $url = \Yii::$app->urlManager->createUrl([
            '/community/community/community-members',
            'id' => $model->id,
            'classname' => $model->className(),
            'params' => $params
        ]);
        $searchPostName = 'searchMemberName'.(!empty($this->showRoles) ? implode('', $this->showRoles) : '');

        $js = JsUtility::getSearchM2mFirstGridJs($gridId, $url, $searchPostName);
        PjaxAsset::register($this->getView());
        $this->getView()->registerJs($js, View::POS_LOAD);
        $itemsMittente = [
            'Photo' => [
                'headerOptions' => [
                    'id' => AmosCommunity::t('amoscommunity', 'Photo'),
                ],
                'contentOptions' => [
                    'headers' => AmosCommunity::t('amoscommunity', 'Photo'),
                ],
                'label' => AmosCommunity::t('amoscommunity', 'Photo'),
                'format' => 'raw',
                'value' => function ($model) {
                    /** @var \lispa\amos\admin\models\UserProfile $userProfile */
                    $userProfile = $model->user->getProfile();
                    return UserCardWidget::widget(['model' => $userProfile]);
                }
            ],
            'name' => [
                'attribute' => 'user.userProfile.surnameName',
                'label' => AmosCommunity::t('amoscommunity', 'Name'),
                'headerOptions' => [
                    'id' => AmosCommunity::t('amoscommunity', 'name'),
                ],
                'contentOptions' => [
                    'headers' => AmosCommunity::t('amoscommunity', 'name'),
                ],
                'value' => function($model){
                    return Html::a($model->user->userProfile->surnameName, ['/admin/user-profile/view', 'id' => $model->user->userProfile->id ], [
                        'title' => AmosCommunity::t('amoscommunity', 'Apri il profilo di {nome_profilo}', ['nome_profilo' => $model->user->userProfile->surnameName])
                    ]);
                },
                'format' => 'html'
            ],
            'status' => [
                'attribute' => 'status',
                'label' => AmosCommunity::t('amoscommunity', 'Status'),
                'headerOptions' => [
                    'id' => AmosCommunity::t('amoscommunity', 'Status'),
                ],
                'contentOptions' => [
                    'headers' => AmosCommunity::t('amoscommunity', 'Status'),
                ],
                'value' => function($model){
                    return $model->getPersonalizedStatus();
                }
            ],
            'role' => [
                'attribute' => 'role',
                'label' => AmosCommunity::t('amoscommunity', 'Role'),
                'headerOptions' => [
                    'id' => AmosCommunity::t('amoscommunity', 'Role'),
                ],
                'contentOptions' => [
                    'headers' => AmosCommunity::t('amoscommunity', 'Role'),
                ],
                'value' => function($model){
                    return AmosCommunity::t('amoscommunity', $model->role);
                }
            ],
        ];
        if ($this->viewEmail) {
            $itemsMittente['email'] = [
                'label' => AmosCommunity::t('amoscommunity', 'Email'),
                'headerOptions' => [
                    'id' => AmosCommunity::t('amoscommunity', 'email'),
                ],
                'contentOptions' => [
                    'headers' => AmosCommunity::t('amoscommunity', 'email'),
                ],
                'value' => function ($model) {
                    /** @var CommunityUserMm $model */
                    return $model->user->email;
                }
            ];
        }
        $isSubCommunity = !empty($model->getCommunityModel()->parent_id);

        //Merge additional solumns
        $itemsMittente = ArrayHelper::merge($itemsMittente, $this->additionalColumns);
        
        $actionColumnsTemplate = '';
        if ($this->checkManager()) {
            $actionColumnsTemplate = '{acceptUser}{rejectUser}{relationAttributeManage}{deleteRelation}';
        }
        if ($this->forceActionColumns) {
            $actionColumnsTemplate = $this->actionColumnsTemplate;
        }

        $associateBtnDisabled = false;
        if($model instanceof Community && $model->status != Community::COMMUNITY_WORKFLOW_STATUS_VALIDATED && !$model->validated_once){
            $associateBtnDisabled = true;
        }


        $query = !empty($this->showRoles)
            ? $model->getCommunityModel()->getCommunityUserMms()->andWhere(['role' => $this->showRoles])
            : $model->getCommunityModel()->getCommunityUserMms();

        $query->innerJoin('user_profile up', 'community_user_mm.user_id = up.user_id')
            ->andWhere(['up.attivo' => 1]);

        if(isset($_POST[$searchPostName])) {
            $searchName = $_POST[$searchPostName];
            if (!empty($searchName)) {
                $query->andWhere('community_user_mm.deleted_at IS NULL')
                    ->andWhere(['or',
                        ['like', 'user_profile.nome',$searchName],
                        ['like', 'user_profile.cognome', $searchName],
                        ['like', "CONCAT( user_profile.nome , ' ', user_profile.cognome )", $searchName],
                        ['like', "CONCAT( user_profile.cognome , ' ', user_profile.nome )", $searchName]
                    ]);
            }
        }

        $contextObject = $model;
        $community = $model->getCommunityModel();
        $roles = $contextObject->getContextRoles();
        $rolesArray = [];
        foreach ($roles as $role) {
            $rolesArray[$role] = AmosCommunity::t('amoscommunity', $role);
        }

        $insass  = ($inviteUserOfcommunityParent && !$isSubCommunity  && $customInvitationForm) || (!$inviteUserOfcommunityParent && $customInvitationForm );
        $widget = \lispa\amos\core\forms\editors\m2mWidget\M2MWidget::widget([
            'model' => $model->getCommunityModel(),
            'modelId' => $model->getCommunityModel()->id,
            'modelData' => $query,
            'overrideModelDataArr' => true,
            'forceListRender' => true,
            'targetUrlParams' => $this->targetUrlParams,
            'gridId' => $gridId,
            'firstGridSearch' => true,
            'isModal' => ($model instanceof Community ),
            'createAdditionalAssociateButtonsEnabled' => $this->showAdditionalAssociateButton,
            'disableCreateButton' => true,
            'disableAssociaButton' => !$this->checkManager(),
            'btnAssociaLabel' => AmosCommunity::t('amoscommunity', 'Invite users'),
            'btnAssociaClass' => 'btn btn-primary' . ($associateBtnDisabled ? ' disabled' : ''),
            'btnAdditionalAssociateLabel' => AmosCommunity::t('amoscommunity', 'Invite internal users'),
            'actionColumnsTemplate' => $actionColumnsTemplate,
            'deleteRelationTargetIdField' => 'user_id',
            'targetUrl' => $insass  ? '/community/community/insass-m2m' : '/community/community/associa-m2m',
            'additionalTargetUrl' => '/community/community/additional-associate-m2m',
            'createNewTargetUrl' => '/admin/user-profile/create',
            'moduleClassName' => AmosCommunity::className(),
            'targetUrlController' => 'community',
            'postName' => 'Community',
            'postKey' => 'user',
            'permissions' => [
                'add' => $this->addPermission,
                'manageAttributes' => $this->manageAttributesPermission //UpdateCommunitiesManagerRule::className()//$model->getCommunityModel()->isCommunityManager()
            ],
            'actionColumnsButtons' => [
                'confirmManager' => function ($url, $model) {
                    /** @var CommunityUserMm $model */
                    $status = $model->status;
                    $createUrlParams = [
                        '/community/community/confirm-manager',
                        'communityId' => $model->community_id,
                        'userId' => $model->user_id,
                        'managerRole' => $this->model->getManagerRole()
                        ];
                    $btn = '';
                    if ($status == CommunityUserMm::STATUS_MANAGER_TO_CONFIRM) {
                        $btn = Html::a(
                            AmosIcons::show('check-circle', ['class' => 'btn btn-tool-secondary']),
                            Yii::$app->urlManager->createUrl($createUrlParams), ['title' => AmosCommunity::t('amoscommunity', 'Confirm manager')]);
                    }
                    return $btn;
                },
                'acceptUser' => function ($url, $model) {
                    /** @var CommunityUserMm $model */
                    $status = $model->status;
                    $createUrlParams = ['/community/community/accept-user', 'communityId' => $model->community_id, 'userId' => $model->user_id];
                    $btn = '';
                    if ($status == CommunityUserMm::STATUS_WAITING_OK_COMMUNITY_MANAGER) {
                        $btn = Html::a(
                            AmosCommunity::t('amoscommunity', 'Accept user'),
                            Yii::$app->urlManager->createUrl($createUrlParams), ['class' => 'btn btn-primary', 'style' => 'font-size: 0.8em']);
                    }
                    return $btn;
                },
                'rejectUser' => function ($url, $model) {
                    /** @var CommunityUserMm $model */
                    $btn = '';
                    $createUrlParams = ['/community/community/reject-user', 'communityId' => $model->community_id, 'userId' => $model->user_id];
                    if ($model->status == CommunityUserMm::STATUS_WAITING_OK_COMMUNITY_MANAGER) {
                        $btn = Html::a(
                            AmosCommunity::t('amoscommunity', 'Reject user'),
                            Yii::$app->urlManager->createUrl($createUrlParams), ['class' => 'btn btn-primary', 'style' => 'font-size: 0.8em']);
                    }
                    return $btn;
                },
                'relationAttributeManage' => function ($url, $model) use ($rolesArray, $community, $contextObject) {
                    $btn = '';
                    $loggedUser = Yii::$app->getUser();
//                    $createUrlParamsRole = ['/community/community/manage-m2m-attributes', 'id' => $model->community_id, 'targetId' => $model->id];
                    $url = Yii::$app->urlManager->createUrl($createUrlParamsRole = ['/community/community/change-user-role', 'communityId' => $model->community_id, 'userId' => $model->user_id]);
                    // ADMIN can't delete himself from a community
                    if ( !\Yii::$app->user->can('ADMIN') || ($model->user_id != \Yii::$app->user->id && \Yii::$app->user->can('ADMIN'))) {
                        if (\Yii::$app->user->can($this->manageAttributesPermission)) {
                            if (!is_null($model->role) && ($model->status != CommunityUserMm::STATUS_WAITING_OK_USER)) {
                                // If an user is community creator, it will be not possible to change his role in participant, unless logged user is admin
                                if (($community->created_by != $model->user_id) || $loggedUser->can("ADMIN")) {
                                    $modalId = 'change-user-role-modal-' . $model->user_id;
                                    $selectId = 'community_user_mm-role-' . $model->user_id;
                                    Modal::begin([
                                        'header' => AmosCommunity::t('amoscommunity', 'Manage role and permission'),
                                        'id' => $modalId,
                                    ]);

                                    echo Html::tag('div', Select::widget([
                                        'auto_fill' => true,
                                        'hideSearch' => true,
                                        'theme' => 'bootstrap',
                                        'data' => $rolesArray,
                                        'model' => $model,
                                        'attribute' => 'role',
                                        'value' => isset($rolesArray[$model->role]) ? AmosCommunity::t('amoscommunity',
                                            $rolesArray[$model->role]) : $rolesArray[$contextObject->getBaseRole()],
                                        'options' => [
//                                    'prompt' => AmosCommunity::t('amoscommunity', 'Select') . '...',
                                            'disabled' => false,
                                            'id' => $selectId
                                        ],
                                        'pluginOptions' => [
                                            'allowClear' => false,
                                        ]
                                    ]), ['class' => 'm-15-0']);

                                    echo Html::tag('div',
                                        Html::a(AmosCommunity::t('amoscommunity', 'Cancel'),
                                            null,
                                            ['class' => 'btn btn-secondary', 'data-dismiss' => 'modal'])
                                        . Html::a(AmosCommunity::t('amoscommunity', 'Save'),
                                            null,
                                            [
                                                'class' => 'btn btn-primary',
                                                'data-dismiss' => 'modal',
                                                'onclick' => "
                                    
                                    $.ajax({
                                        url : '$url', 
                                        type: 'POST',
                                        async: true,
                                        data: { 
                                            role: $('#$selectId').val()
                                        },
                                        success: function(response) {
                                           $('#$modalId').modal('hide');
                                           $('#reset-search-btn-$this->gridId').click();
                                       }
                                    }).done(function(){
                                        $('#$modalId').modal('hide');
                                    });
                                return false;
                            "
                                            ]),
                                        ['class' => 'pull-right m-15-0']
                                    );
//                        echo $this->render('@vendor/lispa/amos-community/src/views/community/change-user-role', ['model' => $model]);
                                    Modal::end();

                                    $btn = Html::a(
                                        AmosCommunity::t('amoscommunity', 'Change role'),
                                        null, [
                                        'class' => 'btn btn-primary font08',
                                        'title' => AmosCommunity::t('amoscommunity', 'Change role'),
                                        'data-toggle' => 'modal',
                                        'data-target' => '#' . $modalId,
                                        'onclick' => 'checkSelect2Init("' . $modalId . '", "' . $selectId . '");'
                                    ]);


//                        $btn = Html::a(
//                            AmosCommunity::t('amoscommunity', 'Change role'),
//                            Yii::$app->urlManager->createUrl($createUrlParamsRole), ['class' => 'btn btn-primary font08']);
                                }
                            }
                        }
                    }

                    return $btn;
                },
                'deleteRelation' => function ($url, $model) {
                    $url = '/community/community/elimina-m2m';
                    $community = \lispa\amos\community\models\Community::findOne($model->community_id);
                    $targetId = $model->user_id;
                    $urlDelete = Yii::$app->urlManager->createUrl([
                        $url,
                        'id' => $community->id,
                        'targetId' => $targetId
                    ]);
                    // ADMIN can't delete himself from a community
                    if( !\Yii::$app->user->can('ADMIN') || ($model->user_id != \Yii::$app->user->id && \Yii::$app->user->can('ADMIN'))) {
                        $loggedUser = Yii::$app->getUser();
                        if ($loggedUser->can('COMMUNITY_UPDATE', ['model' => $this->model])) {
                            $btnDelete = Html::a(
                                AmosIcons::show('close', ['class' => 'btn-delete-relation']),
                                $urlDelete,
                                ['title' => AmosCommunity::t('amoscommunity', 'Delete'),
                                    'data-confirm' => Yii::t('amoscommunity', 'Are you sure to remove this user?'),
                                ]
                            );
                            if (($community->created_by == $model->user_id) && !$loggedUser->can("ADMIN")) {
                                $btnDelete = '';
                            }
                        } else {
                            $btnDelete = '';
                        }
                    }
                    else {
                        $btnDelete = '';
                    }
                    return $btnDelete;
                },
            ],
            'itemsMittente' => $itemsMittente,
        ]);

        $message = $associateBtnDisabled ? AmosCommunity::t('amoscommunity', '#invite_users_disabled_msg') : '';
        return $message . "<div id='".$gridId."' data-pjax-container='".$gridId."-pjax' data-pjax-timeout=\"1000\">".$widget."</div>";
    }
    
    private function checkManager()
    {
        if (!$this->checkManagerRole) {
            return true;
        }
        $communityUtil = new CommunityUtil();
        return $communityUtil->isManagerLoggedUser($this->model);
    }
}
