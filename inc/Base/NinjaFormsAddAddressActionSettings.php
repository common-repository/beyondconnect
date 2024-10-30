<?php if (!defined('ABSPATH')) exit;

return apply_filters('ninja_forms_action_addaddress_settings', array(

    'BC_LinkedFields' => array(
        'name' => 'BC_LinkedFields',
        'type' => 'textarea',
        'label' => __('Linked Fields', 'beyondconnect'),
        'width' => 'full',
        'group' => 'primary',
        'value' => '',
        'placeholder' => __('Choose linked fields.', 'beyondconnect'),
        'help' => __('Linking property to connect with BeyondConnect fieldset. E.g. { name: john doe }.', 'beyondconnect'),
        'use_merge_tags' => true
    ),
));
