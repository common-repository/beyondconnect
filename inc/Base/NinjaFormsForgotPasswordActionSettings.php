<?php if (!defined('ABSPATH')) exit;

return apply_filters('ninja_forms_action_forgotpassword_settings', array(

    'BC_RedirectTo' => array(
        'name' => 'BC_RedirectTo',
        'type' => 'textbox',
        'label' => __('Redirect to', 'beyondconnect'),
        'width' => 'full',
        'group' => 'primary',
        'value' => '',
        'placeholder' => __('Set form to redirect to.', 'beyondconnect'),
        'help' => __('Set form to redirect to.', 'beyondconnect'),
        'use_merge_tags' => true
    ),
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
