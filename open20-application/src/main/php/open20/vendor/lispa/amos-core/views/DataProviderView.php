<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core\views
 * @category   CategoryName
 */

namespace lispa\amos\core\views;

use lispa\amos\core\controllers\CrudController;
use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

/**
 * Class DataProviderView
 * @package backend\components\views
 * decorator for every view in amos
 */
class DataProviderView extends Widget
{
    public $view;
    public $currentView;
    public $viewListClass     = 'lispa\amos\core\views\ListView';
    public $viewGridClass     = 'lispa\amos\core\views\AmosGridView';
    public $viewIconClass     = 'lispa\amos\core\views\IconView';
    public $viewMapClass      = 'lispa\amos\core\views\MapView';
    public $viewCalendarClass = 'lispa\amos\core\views\CalendarView';
    public $viewGanttClass    = 'lispa\amos\core\views\GanttView';
    public $dataProvider;
    public $gridView;
    public $listView;
    public $iconView;
    public $mapView;
    public $calendarView;
    public $ganttView         = [];

    /**
     * @var array $exportConfig Configurations to export data.
     */
    public $exportConfig;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $controller                    = \Yii::$app->controller;
        $appControllerIsCrudController = ($controller instanceof CrudController);

        if (isset($this->gridView)) {
            if ($appControllerIsCrudController) {
                /** @var CrudController $controller */
                $columns = $this->gridView['columns'];
                if (isset($this->gridView['exportColumns'])) {
                    $columns = $this->gridView['exportColumns'];
                }
                $controller->setGridViewColumns($columns);
            }
        }

        if (
            $appControllerIsCrudController &&
            isset($this->exportConfig) &&
            is_array($this->exportConfig) &&
            isset($this->exportConfig['exportEnabled']) &&
            is_bool($this->exportConfig['exportEnabled']) &&
            ($this->exportConfig['exportEnabled'] === true)
        ) {
            /** @var CrudController $controller */
            $columns = [];
            if (isset($this->exportConfig['exportColumns']) && is_array($this->exportConfig['exportColumns'])) {
                $columns = $this->exportConfig['exportColumns'];
            } elseif (isset($this->gridView['columns']) && is_array($this->gridView['columns'])) {
                $columns = $this->gridView['columns'];
            }
            $controller->setGridViewColumns($columns);
            $queryParams                 = Yii::$app->request->getQueryParams();
            $queryParams['download']     = true;
            $queryParams['exportConfig'] = $this->exportConfig;
            Yii::$app->request->setQueryParams($queryParams);
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $viewClass  = $this->{'view'.ucfirst(strtolower($this->currentView['name'])).'Class'};
        $viewParams = $this->{strtolower($this->currentView['name']).'View'};

        $view = ArrayHelper::merge([
                'class' => $viewClass,
                'dataProvider' => $this->getDataProvider(),
                ], $viewParams);

        $this->view = \Yii::createObject($view);

        return $this->view->run();
    }

    /**
     * @return mixed
     */
    public function getCurrentView()
    {
        return $this->currentView;
    }

    /**
     * @param mixed $currentView
     */
    public function setCurrentView($currentView)
    {
        $this->currentView = $currentView;
    }

    /**
     * @return string
     */
    public function getViewListClass()
    {
        return $this->viewListClass;
    }

    /**
     * @param string $viewListClass
     */
    public function setViewListClass($viewListClass)
    {
        $this->viewListClass = $viewListClass;
    }

    /**
     * @return string
     */
    public function getViewGridClass()
    {
        return $this->viewGridClass;
    }

    /**
     * @param string $viewGridClass
     */
    public function setViewGridClass($viewGridClass)
    {
        $this->viewGridClass = $viewGridClass;
    }

    /**
     * @return string
     */
    public function getViewIconClass()
    {
        return $this->viewIconClass;
    }

    /**
     * @param string $viewIconClass
     */
    public function setViewIconClass($viewIconClass)
    {
        $this->viewIconClass = $viewIconClass;
    }

    /**
     * @return mixed
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
    }

    /**
     * @param mixed $dataProvider
     */
    public function setDataProvider($dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * @return mixed
     */
    public function getGridView()
    {
        return $this->gridView;
    }

    /**
     * @param mixed $gridView
     */
    public function setGridView($gridView)
    {
        $this->gridView = $gridView;
    }

    /**
     * @return mixed
     */
    public function getListView()
    {
        return $this->listView;
    }

    /**
     * @param mixed $listView
     */
    public function setListView($listView)
    {
        $this->listView = $listView;
    }

    /**
     * @return mixed
     */
    public function getIconView()
    {
        return $this->iconView;
    }

    /**
     * @param mixed $iconView
     */
    public function setIconView($iconView)
    {
        $this->iconView = $iconView;
    }
}