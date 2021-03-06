<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\organizzazioni
 * @category   CategoryName
 */

namespace lispa\amos\organizzazioni\controllers;

use lispa\amos\core\forms\editors\m2mWidget\controllers\M2MWidgetControllerTrait;
use lispa\amos\organizzazioni\models\Profilo;
use lispa\amos\organizzazioni\models\ProfiloUserMm;
use lispa\amos\organizzazioni\Module;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use Yii;
use yii\helpers\Url;
use lispa\amos\core\user\User;
use lispa\amos\core\forms\editors\m2mWidget\M2MEventsEnum;
use lispa\amos\admin\models\UserProfile;

/**
 * This is the class for controller "ProfiloController".
 */
class ProfiloController extends base\ProfiloController
{

    /**
     * M2MWidgetControllerTrait
     */
    use M2MWidgetControllerTrait;


    /**
     *
     */
    public function init()
    {
        parent::init();

        $this->setMmTableName(ProfiloUserMm::className());
        $this->setStartObjClassName(Profilo::className());
        $this->setMmStartKey('profilo_id');
        $this->setTargetObjClassName(UserProfile::className());
        $this->setMmTargetKey('user_id');
        $this->setRedirectAction('update');
        $this->setModuleClassName(Module::className());
        $this->setCustomQuery(true);
        $this->on(M2MEventsEnum::EVENT_AFTER_DELETE_M2M, [$this, 'afterDeleteM2m']);
        $this->on(M2MEventsEnum::EVENT_BEFORE_CANCEL_ASSOCIATE_M2M, [$this, 'beforeCancelAssociateM2m']);

    }


    /**
     * @param $event
     */
    public function afterDeleteM2m($event)
    {
        $this->setRedirectArray([Url::previous()]);
    }

    /**
     * @param $event
     */
    public function beforeCancelAssociateM2m($event)
    {
        $urlPrevious = Url::previous();
        $id = Yii::$app->request->get('id');

        if (strstr($urlPrevious, 'associate-organization-m2m')) {
            $this->setRedirectArray('/admin/user-profile/update?id=' . $id);        }
        if (strstr($urlPrevious, 'associate-project-m2m')) {
            $this->setRedirectArray('/project_management/projects/update?id=' . $id . '#tab-organizations');
        }
    }


    /**
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [

                    [
                        'allow' => true,
                        'actions' => [
                            'associate-organization-m2m',
                            'elimina-m2m',
                            'annulla-m2m',
                            'user-network',
                            'join-organization'
                        ],
                        'roles' => ['PROFILO_READ']
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'associate-organizations-to-project-m2m',
                            'associate-organizations-to-project-task-m2m',
                        ],
                        'roles' => ['@']
                    ],
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post', 'get']
                ]
            ]
        ]);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function actionAssociateOrganizationsToProjectM2m(){
        if(!empty(\Yii::$app->getModule('project_management'))) {
            $projectId = Yii::$app->request->get('id');
            Url::remember();

            $this->setMmTableName(\lispa\amos\projectmanagement\models\ProjectsJoinedOrganizationsMm::className());
            $this->setStartObjClassName(\lispa\amos\projectmanagement\models\Projects::className());
            $this->setMmStartKey('projects_id');
            $this->setTargetObjClassName(Profilo::className());
            $this->setMmTargetKey('organization_id');
            $this->setRedirectAction('/project_management/projects/update');
            $this->setOptions(['#' => 'tab-organizations']);
            $this->setTargetUrl('associa_organizations_to_project_m2m');
            $this->setCustomQuery(true);
            $this->setModuleClassName(Module::className());
            $this->setRedirectArray('/project_management/projects/update?id=' . $projectId . '#tab-organizations');
            return $this->actionAssociaM2m($projectId);
        }
        else {
            throw new Exception(Module::t('organizations', 'The module project is not enabled'));
        }
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function actionAssociateOrganizationsToProjectTaskM2m(){
        if(!empty(\Yii::$app->getModule('project_management'))) {
            $projectTaskId = Yii::$app->request->get('id');
            Url::remember();

            $this->setMmTableName(\lispa\amos\projectmanagement\models\ProjectsTasksJoinedOrganizationsMm::className());
            $this->setStartObjClassName(\lispa\amos\projectmanagement\models\ProjectsTasks::className());
            $this->setMmStartKey('projects_tasks_id');
            $this->setTargetObjClassName(Profilo::className());
            $this->setMmTargetKey('organization_id');
            $this->setRedirectAction('/project_management/projects-tasks/update');
            $this->setOptions(['#' => 'tab-organizations']);
            $this->setTargetUrl('associa_organizations_to_project_task_m2m');
            $this->setCustomQuery(true);
            $this->setModuleClassName(Module::className());
            $this->setRedirectArray('/project_management/projects-tasks/update?id=' . $projectTaskId . '#tab-organizations');
            return $this->actionAssociaM2m($projectTaskId);
        }
        else {
            throw new Exception(Module::t('organizations', 'The module project is not enabled'));
        }
    }


    public function actionAssociateOrganizationM2m()
    {
        $userId = Yii::$app->request->get('id');
        Url::remember();

        $this->setMmTableName(ProfiloUserMm::className());
        $this->setStartObjClassName(User::className());
        $this->setMmStartKey('user_id');
        $this->setTargetObjClassName(Profilo::className());
        $this->setMmTargetKey('profilo_id');
        $this->setRedirectAction('update');
        $this->setTargetUrl('associate-organization-m2m');
        $this->setCustomQuery(true);
        $userProfileId = User::findOne($userId)->getProfile()->id;
        $this->setRedirectArray('/admin/user-profile/update?id=' . $userProfileId . '#tab-network');
        return $this->actionAssociaM2m($userId);

    }


    /**
     * @param $organizationId
     * @param bool $accept
     * @param null $redirectAction
     * @return \yii\web\Response
     */
    public function actionJoinOrganization($organizationId, $accept = false, $redirectAction = null)
    {
        $defaultAction = 'index';

        if (empty($redirectAction)) {
            $urlPrevious = Url::previous();
            $redirectAction = $urlPrevious;
        }
        if (!$organizationId) {
            Yii::$app->getSession()->addFlash('danger', Module::tHtml('amosorganizzazioni',
                "It is not possible to subscribe the user. Missing parameter organization."));
            return $this->redirect($defaultAction);
        }

        $userPofile = UserProfile::findOne(['id' => Yii::$app->user->id]);

        $ok = false;
        $message = '';
        $nomeCognome = '';
        $organizationName = '';
        $userId = Yii::$app->getUser()->getId();
        /** @var User $user */
        $user = User::findOne($userId);
        /** @var UserProfile $userProfile */
        /** @var UserProfile $userProfile */
        $userProfile = $user->getProfile();
        if (!is_null($userProfile)) {
            $nomeCognome = " '" . $userProfile->nomeCognome . "' ";
        }

        $organization = Profilo::findOne($organizationId);
        if (!is_null($organization)) {
            $organizationName = " '" . $organization->name . "'";
        }
        $userorganization = ProfiloUserMm::findOne(['profilo_id' => $organizationId, 'user_id' => $userId]);
        // Verify if user already in organization user relation table
        if (is_null($userorganization)){
            // Iscrivo l'utente alla organization
            $userorganization = new ProfiloUserMm();
            $userorganization->profilo_id = $organizationId;
            $userorganization->user_id = $userId;
            $ok = $userorganization->save(false);
            $message = Module::tHtml('amosorganizzazioni',
                    "You are now linked to the organization") . $organizationName;
        }

        if ($ok) {
            Yii::$app->getSession()->addFlash('success', $message);
            if (isset($redirectAction)) {
                return $this->redirect($redirectAction);
            } else {
                return $this->redirect($defaultAction);
            }
        } else {
            Yii::$app->getSession()->addFlash('danger', Module::tHtml('amosorganizzazioni',
                    "Error occured while subscribing the user") . $nomeCognome . Module::tHtml('amosorganizzazioni',
                    "to organization") . $organizationName);
            return $this->redirect($defaultAction);
        }
    }

    /**
     * @param $userId
     * @param bool $isUpdate
     * @return string
     */
    public function actionUserNetwork($userId, $isUpdate = false)
    {

        if (\Yii::$app->request->isAjax) {
            $this->setUpLayout(false);

            return $this->render('user-network', [
                'userId' => $userId,
                'isUpdate' => $isUpdate
            ]);
        }
    }

}
