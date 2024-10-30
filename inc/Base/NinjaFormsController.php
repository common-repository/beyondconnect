<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc\Base;

use Inc\Base\BaseController;
use Inc\Beyond;

/**
 *
 */
class NinjaFormsController extends BaseController
{
    public function __construct()
    {
    }

    public function register()
    {
        $this->addFilters();
    }

    public function addFilters()
    {
        add_filter('ninja_forms_register_fields', array($this, 'register_fields'));
        add_filter('ninja_forms_register_actions', array($this, 'register_actions'));
        add_filter('ninja_forms_render_default_value', array($this, 'render_default_value'), 10, 3);
    }

    public function register_actions($actions)
    {
        $actions['BC_Login'] = new NinjaFormsActions_Login();
        $actions['BC_ForgotPassword'] = new NinjaFormsActions_ForgotPassword();
        $actions['BC_AddParent'] = new NinjaFormsActions_AddParent();
        $actions['BC_AddAddress'] = new NinjaFormsActions_AddAddress();
        $actions['BC_AddAddressEmailNewsletter'] = new NinjaFormsActions_AddAddressEmailNewsletter();
        $actions['BC_EditAddress'] = new NinjaFormsActions_EditAddress();
        $actions['BC_AddRegistration'] = new NinjaFormsActions_AddRegistration();


        return $actions;
    }

    public function register_fields($fields)
    {
        $fields['BC_List'] = new NinjaFormsFields_List();

        return $fields;
    }

    public function render_default_value($default_value, $field_type, $field_settings)
    {
        //Assign global variables to form fields
        global $bc_global;

        $default_value = $this->render_shortcodes_default_values($default_value, $field_type, $field_settings);
        $default_value = $this->render_user_default_values($default_value, $field_type, $field_settings);

        return $default_value;
    }

    protected function render_shortcodes_default_values($default_value, $field_type, $field_settings)
    {
        global $bc_global;

        $field_key = $field_settings['key'];
        $field_strip = str_replace('beyondconnect_globals_', '', $field_key, $count);

        if ($count === 0)
            return $default_value;

        if (empty($bc_global['bc_shortcode']))
            return $default_value;

        foreach ($bc_global['bc_shortcode'] as $key => $value) {
            if ($key === $field_strip && $count === 1)
                $default_value = $value;
        }

        if (!empty($bc_global['bc_shortcode'][$field_strip]) && $count === 1)
            $default_value = $bc_global['bc_shortcode'][$field_strip];

        return $default_value;
    }

    private function render_user_default_values($default_value, $field_type, $field_settings)
    {
        $field_key = $field_settings['key'];
        $field_strip = str_replace('beyondconnect_user_', '', $field_key, $count);

        if ($count === 0)
            return $default_value;

        if (!isset(wp_get_current_user()->user_login) || !Beyond::isGuid(wp_get_current_user()->user_login))
            return $default_value;

        $addresses = Beyond::getValues('adressen(' . wp_get_current_user()->user_login . ')', '', false);

        if (empty($addresses))
            return $default_value;

        foreach ($addresses as $address) {
            $address = array_change_key_case($address, CASE_LOWER);

            if ($count === 1 && !empty($address[$field_strip]))
                $default_value = $address[$field_strip];
        }
        return $default_value;
    }
}