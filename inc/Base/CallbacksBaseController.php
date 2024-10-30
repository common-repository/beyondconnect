<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc\Base;

use Inc\Base\BaseController;

class CallbacksBaseController extends BaseController
{
    public function checkboxField($args)
    {
        $name = $args['label_for'];
        $classes = $args['class'];
        $option_name = $args['option_name'];
        $checkbox = get_option($option_name);
        $checked = isset($checkbox[$name]) ? ($checkbox[$name] ? true : false) : false;

        echo '<div class="' . $classes . '"><input type="checkbox" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="1" class="" ' . ($checked ? 'checked' : '') . '><label for="' . $name . '"><div></div></label></div>';
    }

    public function textField($args)
    {
        $name = $args['label_for'];
        $option_name = $args['option_name'];
        $required = $args['required'];
        $input = get_option($option_name);
        $value = empty($input[$name]) ? '' : $input[$name];

        echo '<input type="text" class="regular-text" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="' . $value . '" ' . ($required ? 'required' : '') . '>';
    }

    public function passwordField($args)
    {
        $name = $args['label_for'];
        $option_name = $args['option_name'];
        $required = $args['required'];
        $input = get_option($option_name);
        $value = empty($input[$name]) ? '' : $input[$name];

        echo '<input type="password" class="regular-text" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="' . $value . '" ' . ($required ? 'required' : '') . '>';
    }
}