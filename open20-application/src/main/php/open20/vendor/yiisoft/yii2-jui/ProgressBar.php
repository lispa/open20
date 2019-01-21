<?php
/**
 */

namespace yii\jui;

use yii\helpers\Html;

/**
 * ProgressBar renders an progressbar jQuery UI widget.
 *
 * For example:
 *
 * ```php
 * echo ProgressBar::widget([
 *     'clientOptions' => [
 *         'value' => 75,
 *     ],
 * ]);
 * ```
 *
 * The following example will show the content enclosed between the [[begin()]]
 * and [[end()]] calls within the widget container:
 *
 * ~~~php
 * ProgressBar::begin([
 *     'clientOptions' => ['value' => 75],
 * ]);
 *
 * echo '<div class="progress-label">Loading...</div>';
 *
 * ProgressBar::end();
 * ~~~
 * @since 2.0
 */
class ProgressBar extends Widget
{
    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
        echo Html::beginTag('div', $this->options) . "\n";
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        echo Html::endTag('div') . "\n";
        $this->registerWidget('progressbar');
    }
}