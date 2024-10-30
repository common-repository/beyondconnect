<?php

namespace Inc\Base;

use Inc\Beyond;

if (!defined('ABSPATH') || !class_exists('NF_Abstracts_List')) exit;

class NinjaFormsFields_List extends \NF_Abstracts_List
{
    protected $_name = 'BC_List';
    protected $_type = 'BC_List';

    protected $_nicename = 'BC List';

    protected $_section = 'common';

    protected $_templates = array('BC_List', 'listselect');

    protected $_settings = array('BC_FieldsetProperty', 'BC_SelectText');

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __('BC List', 'beyondconnect');
        $this->_settings['options']['group'] = '';

        add_filter('ninja_forms_render_options_' . $this->_type, array($this, 'filter_options'), 10, 2);
    }

    protected function load_settings($only_settings = array())
    {
        $settings = array();

        $all_settings = \Ninja_Forms::config('FieldSettings');
        $beyond_settings = include 'NinjaFormsFieldSettings.php';

        $all_settings = array_merge($all_settings, $beyond_settings);

        foreach ($only_settings as $setting) {

            if (!empty($all_settings[$setting])) {

                $settings[$setting] = $all_settings[$setting];
            }
        }

        return $settings = apply_filters('ninja_forms_field_load_settings', $settings, $this->_name, $this->get_parent_type());
    }

    public function filter_options($options, $settings)
    {
        $default_value = (empty($settings['default'])) ? '' : $settings['default'];

        $options = $this->get_options($settings); // Overwrite the default list options.
        foreach ($options as $key => $option) {
            if ($default_value != $option['value']) continue;
            $options[$key]['selected'] = 1;
        }

        return $options;
    }

    public function admin_form_element($id, $value)
    {
        $field = Ninja_Forms()->form()->get_field($id);

        $options = $this->get_options();
        $options = array_change_key_case($options, CASE_LOWER);

        $options = apply_filters('ninja_forms_render_options', $options, $field->get_settings());
        $options = apply_filters('ninja_forms_render_options_' . $this->_type, $options, $field->get_settings());

        ob_start();
        echo "<select name='fields[$id]'>";
        foreach ($options as $option) {
            $selected = strcasecmp($option['value'], $value) === 0 ? ' selected' : '';
            echo "<option value='" . $option['value'] . "'" . $selected . ">" . $option['label'] . "</option>";
        }
        echo "</select>";
        return ob_get_clean();
    }

    private function get_options($settings = array())
    {
        $settings = array_change_key_case($settings, CASE_LOWER);

        $fieldset = (empty($settings['bc_fieldsetproperty'])) ? '' : $settings['bc_fieldsetproperty'];

        $order = 0;
        $options = array();
        $options[] = array(
            'label' => (!empty($settings['bc_selecttext'])) ? $settings['bc_selecttext'] : '... ' . __('Select item', 'beyondconnect') . ' ...',
            'value' => '',
            'calc' => '',
            'selected' => 0,
            'order' => $order,
        );
        $order++;

        $querystring = '';

        switch ($fieldset) {
            case 'CoursesIDTitleDate':
                $querystring = Beyond::getODataQueryString('Kurse', '', ('kursId,kursIdTitelDatumVonDatumBis'), '', 'istAnmeldungMoeglich eq true', '', '', 'kursIdTitelDatumVonDatumBis');
                $labelfields = array('kursIdTitelDatumVonDatumBis');
                $valuefield = 'kursId';
                break;
            case 'TeacherFirstname':
                $querystring = Beyond::getODataQueryString('Lehrer', '', ('lehrerId,vorname'), '', '', '', '', 'vorname');
                $labelfields = array('vorname');
                $valuefield = 'lehrerId';
                break;
            case 'TeacherFirstnameLastname':
                $querystring = Beyond::getODataQueryString('Lehrer', '', ('lehrerId,vorname,nachname'), '', '', '', '', 'vorname, nachname');
                $labelfields = array('vorname', 'nachname');
                $valuefield = 'lehrerId';
                break;
            case 'TeacherLastnameFirstname':
                $querystring = Beyond::getODataQueryString('Lehrer', '', ('lehrerId,nachname,vorname'), '', '', '', '', 'nachname, vorname');
                $labelfields = array('nachname', 'vorname');
                $valuefield = 'lehrerId';
                break;
        }

        if (empty($querystring))
            return array();

        $json = Beyond::getValues($querystring, 'value', false);

        if (empty($json))
            return array();

        foreach ($json as $record) {
            $label = '';
            foreach ($labelfields as $labelfield) {
                $label .= $record[$labelfield] . ' ';
            }

            $options[] = array(
                'label' => $label,
                'value' => $record[$valuefield],
                'calc' => '',
                'selected' => 0,
                'order' => $order
            );

            $order++;
        }

        return $options;
    }
}
