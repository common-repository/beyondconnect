<?php

namespace Inc\Base;

use Inc\Beyond;


if (!defined('ABSPATH') || !class_exists('NF_Abstracts_Action')) exit;

abstract class NinjaFormsBaseAction extends \NF_Abstracts_Action
{
    public $plugin_path;
    protected $_tags = array();

    public function __construct()
    {
        parent::__construct();

        $this->plugin_path = plugin_dir_path(dirname(__FILE__, 2));
    }

    public function getLinkedFieldValues($action_settings, $form_id, $data)
    {
        $results = array();

        $linkedFields = explode("\n", $action_settings['BC_LinkedFields']);
        if (!empty($linkedFields) && is_array($linkedFields)) {
            foreach ($linkedFields as $linkedfield_key => $linkedfield_pair) {
                $linkedfield_pair = trim($linkedfield_pair, '{}');
                if (!empty($linkedfield_pair)) {
                    $linkedfield = explode(':', $linkedfield_pair);
                    if (!empty($linkedfield) && is_array($linkedfield)) {
                        if (isset($linkedfield[0]) && isset($linkedfield[1]) && !empty($linkedfield[0]) && !empty($linkedfield[1])) {
                            $formfields = $data['fields'];
                            foreach ($formfields as $formfield) {

                                $formfield_id = $formfield['id'];
                                $formfield_key = $formfield['key'];
                                $formfield_value = $formfield['value'];
                                $formfield_type = $formfield['type'];

                                if (strcasecmp($formfield_type, "date") === 0) {
                                    $formfield_format = $formfield['date_format'];
                                    if ($formfield_format === "DD-MM-YYYY"
                                        || $formfield_format === "DD.MM.YYYY") {
                                        $formfield_value = date("Y-m-d", strtotime($formfield_value));
                                    }
                                }

                                if (strcasecmp($formfield_key, $linkedfield[1]) === 0) {
                                    $results[$linkedfield[0]] = $formfield_value;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $results;
    }

    public function getLinkedFieldNames($action_settings, $form_id, $data)
    {
        $results = array();

        $linkedFields = explode("\n", $action_settings['BC_LinkedFields']);
        if (!empty($linkedFields) && is_array($linkedFields)) {
            foreach ($linkedFields as $linkedfield_key => $linkedfield_pair) {
                $linkedfield_pair = trim($linkedfield_pair, '{}');
                if (!empty($linkedfield_pair)) {
                    $linkedfield = explode(':', $linkedfield_pair);
                    if (!empty($linkedfield) && is_array($linkedfield)) {
                        if (isset($linkedfield[0]) && isset($linkedfield[1]) && !empty($linkedfield[0]) && !empty($linkedfield[1])) {
                            $formfields = $data['fields'];
                            foreach ($formfields as $formfield) {

                                $formfield_id = $formfield['id'];
                                $formfield_key = $formfield['key'];
                                $formfield_value = $formfield['value'];
                                $formfield_type = $formfield['type'];

                                if (strcasecmp($formfield_key, $linkedfield[1]) === 0) {
                                    $results[$linkedfield[0]] = $formfield_id;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $results;
    }
}