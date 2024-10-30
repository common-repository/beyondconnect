<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc\Api\Callbacks;

use Inc\Base\CallbacksBaseController;

class DashboardCallbacks extends CallbacksBaseController
{
    public function DashboardPage()
    {
        return require_once("$this->plugin_path/templates/dashboard.php");
    }
}