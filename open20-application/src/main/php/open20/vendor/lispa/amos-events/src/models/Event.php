<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\events\models
 * @category   CategoryName
 */

namespace lispa\amos\events\models;

use lispa\amos\comments\models\CommentInterface;
use lispa\amos\community\models\CommunityContextInterface;
use lispa\amos\community\models\CommunityUserMm;
use lispa\amos\core\behaviors\SoftDeleteByBehavior;
use lispa\amos\core\interfaces\ContentModelInterface;
use lispa\amos\core\interfaces\ViewModelInterface;
use lispa\amos\core\user\User;
use lispa\amos\events\AmosEvents;
use lispa\amos\events\i18n\grammar\EventGrammar;
use lispa\amos\events\widgets\icons\WidgetIconEvents;
use yii\base\Behavior;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class Event
 * This is the model class for table "event".
 *
 * @property-read string $completeAddress
 *
 * @package lispa\amos\events\models
 */
class Event extends \lispa\amos\events\models\base\Event implements ContentModelInterface, CommunityContextInterface, CommentInterface, ViewModelInterface
{
    /**
     * Constants for community roles
     */
    const EVENT_MANAGER = 'EVENT_MANAGER';
    const EVENT_PARTICIPANT = 'EVENT_PARTICIPANT';

    /**
     * @var string
     */
    public $begin_date_hour_from;

    /**
     * @var string $begin_date_hour_to
     */
    public $begin_date_hour_to;

    /**
     * @var string $end_date_hour_from
     */
    public $end_date_hour_from;

    /**
     * @var string $end_date_hour_to
     */
    public $end_date_hour_to;

    /**
     * @var $eventLogo
     */
    private $eventLogo;

    /**
     * @var bool $bypassEventLogoValidation
     */
    public $bypassEventLogoValidation = false;

    /**
     * @var $eventAttachments
     */
    public $eventAttachments;

    /**
     * @var $eventAttachmentsForItemView
     */
    public $eventAttachmentsForItemView;

    /**
     * @var $location
     */
    public $location;

    /**
     * @inheritdoc
     */
    public function representingColumn()
    {
        return [
            'title',
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->eventLogo = $this->getEventLogo();
        $this->eventAttachments = $this->getEventAttachments()->one();
        $this->eventAttachmentsForItemView = $this->getEventAttachments()->all();
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            $this->wizardScenarios()
        );
    }

    /**
     * All creation event wizard behaviors.
     * @return array
     */
    private function wizardScenarios()
    {
        return [
            self::SCENARIO_INTRODUCTION => [
                'event_type_id'
            ],
            self::SCENARIO_DESCRIPTION => [
                'title',
                'summary',
                'description',
                'eventLogo',
                'file',
                'event_location',
                'event_address',
                'event_address_house_number',
                'event_address_cap',
                'city_location_id',
                'province_location_id',
                'country_location_id',
                'begin_date_hour',
                'length',
                'length_mu_id',
                'end_date_hour',
                'eventTypeId',
                'event_commentable'
            ],
            self::SCENARIO_ORGANIZATIONALDATA => [
                'publish_in_the_calendar',
                'event_management',
                'event_membership_type_id',
                'seats_available',
                'paid_event',
                'registration_limit_date'
            ],
            self::SCENARIO_PUBLICATION => [
                'publication_date_begin',
                'publication_date_end',
            ],
            self::SCENARIO_SUMMARY => [
                'status',
            ],
            self::SCENARIO_ORG_HIDE_PUBBLICATION_DATE => [
                'publish_in_the_calendar',
                'event_management',
                'event_membership_type_id',
                'seats_available',
                'paid_event',
                'registration_limit_date'
            ]
        ];
    }

    /**
     * Getter for $this->eventLogo;
     * @return \yii\db\ActiveQuery
     */
    public function getEventLogo()
    {
        if (empty($this->eventLogo)) {
            $this->eventLogo = $this->hasOneFile('eventLogo')->one();
        }
        return $this->eventLogo;
    }


    /**
     *
     * @param type $image
     */
    public function setEventLogo($image)
    {
        $this->eventLogo = $image;
    }

    /**
     * Getter for $this->eventAttachments;
     * @return \yii\db\ActiveQuery
     */
    public function getEventAttachments()
    {
        $query = $this->hasMultipleFiles('eventAttachments');
        $query->multiple = false;
        return $query;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['eventAttachments'], 'file', 'maxFiles' => 0],
            [['eventLogo'], 'file', 'extensions' => 'jpeg, jpg, png, gif'],
            [['eventLogo'], 'required', 'when' => function ($model) {
                /** @var \lispa\amos\events\models\Event $model */
                if ($this->bypassEventLogoValidation) {
                    return false;
                }
                if (is_null($this->eventType)) {
                    return false;
                }
                return ($model->eventType->logoRequested == 1 ? true : false);
            }, 'whenClient' => "function (attribute, value) {
                return " . (!is_null($this->eventType) ? $this->eventType->logoRequested : 0) . ";
            }"],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'begin_date_hour_from' => AmosEvents::t('amosevents', 'From begin date and hour'),
            'begin_date_hour_to' => AmosEvents::t('amosevents', 'To begin date and hour'),
            'end_date_hour_from' => AmosEvents::t('amosevents', 'From end date and hour'),
            'end_date_hour_to' => AmosEvents::t('amosevents', 'To end date and hour'),
            'eventLogo' => AmosEvents::t('amosevents', 'Logo')
        ]);
    }

    /**
     * Funzione che crea gli eventi da visualizzare sulla mappa in caso di più eventi legati al singolo model
     * Andrà valorizzato il campo array a true nella configurazione della vista calendario nella index
     */
    public function getEvents()
    {
        return NULL; //da personalizzare
    }

    /**
     * Restituisce l'url per il calendario dell'attività
     */
    public function getEventUrl()
    {
        return NULL; //da personalizzare magari con Yii::$app->urlManager->createUrl([]);
    }

    /**
     * Restituisce il colore associato all'evento
     */
    public function getEventColor()
    {
        return $this->eventType->color;
    }

    /**
     * Restituisce il titolo, possono essere anche più dati, associato all'evento
     */
    public function getEventTitle()
    {
        return $this->title;
    }

    /**
     * Restituisce un'immagine se associata al model
     */
    public function getAvatarUrl($dimension = 'small')
    {
        $url = '/img/img_default.jpg';
        //funzione da implementare
        return $url;
    }

    /**
     * @inheritdoc
     */
    public function getGridViewColumns()
    {
        return [
            'title' => [
                'attribute' => 'title',
                'headerOptions' => [
                    'id' => 'title'
                ],
                'contentOptions' => [
                    'headers' => 'title'
                ]
            ],
            'description' => [
                'attribute' => 'description',
                'format' => 'html',
                'headerOptions' => [
                    'id' => 'description'
                ],
                'contentOptions' => [
                    'headers' => 'description'
                ]
            ],
            'begin_date_hour' => [
                'attribute' => 'begin_date_hour',
                'headerOptions' => [
                    'id' => 'begin'
                ],
                'contentOptions' => [
                    'headers' => 'begin'
                ]
            ],
            'end_date_hour' => [
                'attribute' => 'end_date_hour',
                'headerOptions' => [
                    'id' => 'end'
                ],
                'contentOptions' => [
                    'headers' => 'end'
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getViewUrl()
    {
        return "events/event/view";
    }

    /**
     * @inheritdoc
     */
    public function getToValidateStatus()
    {
        return self::EVENTS_WORKFLOW_STATUS_PUBLISHREQUEST;
    }

    /**
     * @inheritdoc
     */
    public function getValidatedStatus()
    {
        return self::EVENTS_WORKFLOW_STATUS_PUBLISHED;
    }

    /**
     * @inheritdoc
     */
    public function getDraftStatus()
    {
        return self::EVENTS_WORKFLOW_STATUS_DRAFT;
    }

    /**
     * @inheritdoc
     */
    public function getValidatorRole()
    {
        return 'EVENTS_VALIDATOR';
    }

    /**
     * @inheritdoc
     */
    public function getContextRoles()
    {
        $context_roles = [
            self::EVENT_MANAGER,
            self::EVENT_PARTICIPANT
        ];
        return $context_roles;
    }

    /**
     * @inheritdoc
     */
    public function getBaseRole()
    {
        return self::EVENT_PARTICIPANT;
    }

    /**
     * @inheritdoc
     */
    public function getManagerRole()
    {
        return self::EVENT_MANAGER;
    }

    /**
     * @inheritdoc
     */
    public function getRolePermissions($role)
    {
        switch ($role) {
            case self::EVENT_MANAGER:
                return ['CWH_PERMISSION_CREATE', 'CWH_PERMISSION_VALIDATE'];
                break;
            case self::EVENT_PARTICIPANT:
                return ['CWH_PERMISSION_CREATE'];
                break;
            default:
                return ['CWH_PERMISSION_CREATE'];
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public function getCommunityModel()
    {
        return $this->community;
    }

    /**
     * @inheritdoc
     */
    public function getNextRole($role)
    {
        switch ($role) {
            case self::EVENT_MANAGER :
                return self::EVENT_PARTICIPANT;
                break;
            case self::EVENT_PARTICIPANT :
                return self::EVENT_MANAGER;
                break;
            default :
                return self::EVENT_PARTICIPANT;
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public function getPluginModule()
    {
        return 'events';
    }

    /**
     * @inheritdoc
     */
    public function getPluginController()
    {
        return 'event';
    }

    /**
     * @inheritdoc
     */
    public function getRedirectAction()
    {
        return 'view';
    }

    /**
     * @inheritdoc
     */
    public function getAdditionalAssociationTargetQuery($communityId)
    {
        /** @var ActiveQuery $communityUserMms */
        $communityUserMms = CommunityUserMm::find()->andWhere(['community_id' => $communityId]);
        return User::find()->andFilterWhere(['not in', 'id', $communityUserMms->select('user_id')]);
    }

    public function getPluginWidgetClassname()
    {
        return WidgetIconEvents::className();
    }

    /**
     * This method detach SoftDeleteByBehavior from the Event model.
     * @param string $className
     */
    public function detachBehaviorByClassName($className)
    {
        $behaviors = $this->getBehaviors();
        foreach ($behaviors as $index => $behavior) {
            /** @var Behavior $behavior */
            if ($behavior->className() == $className) {
                $this->detachBehavior($index);
            }
        }
    }

    /**
     * This method detach SoftDeleteByBehavior from the Event model.
     */
    public function detachEventSoftDeleteBehavior()
    {
        $this->detachBehaviorByClassName(SoftDeleteByBehavior::className());
    }

    /**
     * @inheritdoc
     */
    public function isCommentable()
    {
        return $this->event_commentable;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription($truncate)
    {
        $ret = $this->description;

        if ($truncate) {
            $ret = $this->__shortText($this->description, 200);
        }
        return $ret;
    }

    /**
     * @return string date begin of publication
     */
    public function getPublicatedFrom()
    {
        return $this->publication_date_begin;
    }

    /**
     * @return string date end of publication
     */
    public function getPublicatedAt()
    {
        return $this->publication_date_begin;
    }

    /**
     * @return \yii\db\ActiveQuery category of content
     */
    public function getCategory()
    {
        return $this->hasOne(\lispa\amos\events\models\EventType::className(), ['id' => 'event_type_id']);
    }

    /**
     * @return string The url to view of this model
     */
    public function getFullViewUrl()
    {
        return Url::toRoute(["/" . $this->getViewUrl(), "id" => $this->id]);
    }

    /**
     * @return mixed
     */
    public function getGrammar()
    {
        return new EventGrammar();
    }

    /**
     * @return array list of statuses that for cwh is validated
     */
    public function getCwhValidationStatuses()
    {
        return [$this->getValidatedStatus()];
    }

    /**
     * @return string
     */
    public function getEventsImageUrl($size = 'original', $protected = true, $url = '/img/img_default.jpg')
    {
        $eventImage = $this->getEventLogo();
        if (!is_null($eventImage)) {
            if ($protected) {
                $url = $eventImage->getUrl($size, false, true);
            } else {
                $url = $eventImage->getWebUrl($size, false, true);
            }
        }
        return $url;
    }

    /**
     * @return string
     */
    public function getCompleteAddress()
    {
        $address = '';
        if ($this->event_address) {
            $address .= $this->event_address;
            if ($this->event_address_house_number) {
                $address .= ' ' . $this->event_address_house_number;
            }
        }
        if ($this->event_address_cap) {
            $address .= ($this->event_address ? ', ' : ' ') . $this->event_address_cap;
        }
        if (!is_null($this->cityLocation)) {
            $address .= (strlen($address) > 0 ? ' ' : '') . $this->cityLocation->nome;
        }
        return $address;
    }

    public function setPublicationScenario(){
        $moduleNews = \Yii::$app->getModule(AmosEvents::getModuleName());
        if($moduleNews->hidePubblicationDate == true){
            $this->setScenario(Event::SCENARIO_ORG_HIDE_PUBBLICATION_DATE);
        }
        else {
            $this->setScenario(Event::SCENARIO_ORGANIZATIONALDATA);
        }
    }
}
