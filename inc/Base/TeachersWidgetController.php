<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc\Base;

use Inc\Base\BaseController;
use Inc\Api\Widgets\TeachersWidget;

/**
 *
 */
class TeachersWidgetController extends BaseController
{
    public function register()
    {
        if (!$this->activated('teachers_widget')) return;

        $teachers_widget = new TeachersWidget();
        $teachers_widget->register();
    }
}