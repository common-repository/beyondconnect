<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc\Api\Callbacks;

use Inc\Base\CallbacksBaseController;

class ShortcodesCallbacks extends CallbacksBaseController
{
    public function ShortcodesPage()
    {
        return require_once("$this->plugin_path/templates/shortcodes.php");
    }
}