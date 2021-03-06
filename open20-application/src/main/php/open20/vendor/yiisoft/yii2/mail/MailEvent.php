<?php
/**
 */

namespace yii\mail;

use yii\base\Event;

/**
 * MailEvent represents the event parameter used for events triggered by [[BaseMailer]].
 *
 * By setting the [[isValid]] property, one may control whether to continue running the action.
 *
 * @since 2.0
 */
class MailEvent extends Event
{
    /**
     * @var \yii\mail\MessageInterface the mail message being send.
     */
    public $message;
    /**
     * @var bool if message was sent successfully.
     */
    public $isSuccessful;
    /**
     * @var bool whether to continue sending an email. Event handlers of
     * [[\yii\mail\BaseMailer::EVENT_BEFORE_SEND]] may set this property to decide whether
     * to continue send or not.
     */
    public $isValid = true;
}
