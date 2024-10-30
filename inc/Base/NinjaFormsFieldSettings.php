<?php

namespace Inc\Base;

return apply_filters('ninja_forms_field_settings', array(

    'BC_FieldsetProperty' => array(
        'name' => 'BC_FieldsetProperty',
        'type' => 'select',
        'label' => __('BC Fieldset Property', 'beyondconnect'),
        'width' => 'full',
        'group' => 'primary',
        'value' => '',
        'options' => array(
            array(
                'label' => __('Courses - ID Title Date', 'beyondconnect'),
                'value' => 'CoursesIDTitleDate',
            ),
            array(
                'label' => __('Teacher - Firstname', 'beyondconnect'),
                'value' => 'TeacherFirstname',
            ),
            array(
                'label' => __('Teacher - Firstname Lastname', 'beyondconnect'),
                'value' => 'TeacherFirstnameLastname',
            ),
            array(
                'label' => __('Teacher - Lastname Firstname', 'beyondconnect'),
                'value' => 'TeacherLastnameFirstname',
            ),
        ),
        'help' => __('Select the property to connect with BeyondConnect fieldset.', 'beyondconnect'),
    ),
    'BC_SelectText' => array(
        'name' => 'BC_SelectText',
        'type' => 'textbox',
        'label' => __('BC Select Text', 'beyondconnect'),
        'width' => 'full',
        'group' => 'primary',
        'value' => '... ' . __('Select item', 'beyondconnect') . ' ...',
        'help' => __('Type the text for ... select item ...', 'beyondconnect'),
    ),

));

