<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc\Api\Widgets;

use Inc\Beyond;

use WP_Widget;

/**
 *
 */
class TeachersWidget extends WP_Widget
{
    public $widget_ID;

    public $widget_name;

    public $widget_options = array();

    public $control_options = array();

    function __construct()
    {

        $this->widget_ID = 'beyondconnect_teachers_widget';
        $this->widget_name = 'BeyondConnect Teachers Widget';

        $this->widget_options = array(
            'classname' => $this->widget_ID,
            'description' => $this->widget_name,
            'customize_selective_refresh' => true,
        );

        $this->control_options = array(
            'width' => 400,
            'height' => 350,
        );
    }

    public function register()
    {
        parent::__construct($this->widget_ID, $this->widget_name, $this->widget_options, $this->control_options);

        add_action('widgets_init', array($this, 'widgetsInit'));
    }

    public function widgetsInit()
    {
        register_widget($this);
    }

    public function widget($args, $instance)
    {
        $option = get_option('beyondconnect_option');

        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        echo '<ul>';

        $querystring = Beyond::getODataQueryString(
            'Lehrer',
            '',
            ('lehrerId,' . $instance['field']),
            '',
            (empty($instance['filter']) ? '' : $instance['filter']),
            (empty($instance['top']) ? '' : $instance['top']),
            (empty($instance['skip']) ? '' : $instance['skip']),
            (empty($instance['orderby']) ? '' : $instance['orderby']));
        $json_lehrer = Beyond::getValues($querystring, 'value', false);

        if (empty($json_lehrer))
            return '';

        foreach ($json_lehrer as $lehrer) {
            $lehrer = array_change_key_case($lehrer);

            echo '<li>';
            if (!empty($instance['field'])) {
                if (empty($instance['link'])) {
                    echo $lehrer[$instance['field']];
                } else {
                    echo '<a href="' . $instance['link'] . $lehrer['lehrerid'] . '">' . $lehrer[strtolower($instance['field'])] . '</a>';
                }
            }
            echo '</li>';
        }

        echo '</ul>';
        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $title = empty($instance['title']) ? '' : $instance['title'];
        $field = empty($instance['field']) ? '' : $instance['field'];
        $link = empty($instance['link']) ? '' : $instance['link'];
        $filter = empty($instance['filter']) ? '' : $instance['filter'];
        $top = empty($instance['top']) ? '' : $instance['top'];
        $skip = empty($instance['skip']) ? '' : $instance['skip'];
        $orderby = empty($instance['orderby']) ? '' : $instance['orderby'];
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_attr_e(__('Title', 'beyondconnect') . ':', 'beyondconnect'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('field')); ?>">
                <?php esc_attr_e(__('Field to Display', 'beyondconnect') . ':', 'beyondconnect'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('field')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('field')); ?>" type="text"
                   value="<?php echo esc_attr($field); ?>"/>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('link')); ?>">
                <?php esc_attr_e(__('Link of Field', 'beyondconnect') . ':', 'beyondconnect'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('link')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('link')); ?>" type="text"
                   value="<?php echo esc_attr($link); ?>"/>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('filter')); ?>">
                <?php esc_attr_e(__('Filter', 'beyondconnect') . ':', 'beyondconnect'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('filter')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('filter')); ?>" type="text"
                   value="<?php echo esc_attr($filter); ?>"/>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('top')); ?>">
                <?php esc_attr_e(__('Top', 'beyondconnect') . ':', 'beyondconnect'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('top')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('top')); ?>" type="text"
                   value="<?php echo esc_attr($top); ?>"/>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('skip')); ?>">
                <?php esc_attr_e(__('Skip', 'beyondconnect') . ':', 'beyondconnect'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('skip')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('skip')); ?>" type="text"
                   value="<?php echo esc_attr($skip); ?>"/>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('orderby')); ?>">
                <?php esc_attr_e(__('Order by', 'beyondconnect') . ':', 'beyondconnect'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('orderby')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('orderby')); ?>" type="text"
                   value="<?php echo esc_attr($orderby); ?>"/>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['field'] = sanitize_text_field($new_instance['field']);
        $instance['link'] = sanitize_text_field($new_instance['link']);
        $instance['filter'] = sanitize_text_field($new_instance['filter']);
        $instance['top'] = sanitize_text_field($new_instance['top']);
        $instance['skip'] = sanitize_text_field($new_instance['skip']);
        $instance['orderby'] = sanitize_text_field($new_instance['orderby']);

        return $instance;
    }
}