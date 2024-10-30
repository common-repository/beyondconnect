<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc\Base;

use Inc\Beyond;
use Inc\Base\ShortcodesBaseController;

class ShortcodesCoursesController extends ShortcodesBaseController
{
    public function register()
    {
        if (!$this->activated('shortcodes_courses')) return;

        parent::register();
    }

    public function setShortcodes()
    {
        $this->codes = array(
            'beyondconnect_courses',
            'beyondconnect_courses_element',
            'beyondconnect_courses_structure',
            'beyondconnect_courses_groups',
            'beyondconnect_courses_groups_element',
            'beyondconnect_courses_dates',
            'beyondconnect_courses_dates_element',
            'beyondconnect_courses_list',
            'beyondconnect_courses_list_element',
            'beyondconnect_courses_list_collapsible',
            'beyondconnect_courses_list_popupable',
            'beyondconnect_coursdates_list',
            'beyondconnect_coursdates_list_element',
            'beyondconnect_coursdates_list_collapsible',
            'beyondconnect_coursdates_list_popupable'
        );
    }

    public function beyondconnect_courses($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        $o = $this->execDBShortcode($content, $wporg_atts, 'kurse(\'' . $wporg_atts['kursid'] . '\')');
        $o = $this->replaceFormula($o);
        return $o;
    }

    public function beyondconnect_courses_element($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        $o = $this->execElementShortcode($content, $wporg_atts, 'bc_element courses', 'title');

        $o = $this->replaceFormula($o);

        return $o;
    }

    public function beyondconnect_courses_structure($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        $select = empty($wporg_atts['select']) ? '' : $wporg_atts['select'];
        $filter = empty($wporg_atts['filter']) ? '' : $wporg_atts['filter'];
        $top = empty($wporg_atts['top']) ? '' : $wporg_atts['top'];
        $skip = empty($wporg_atts['skip']) ? '' : $wporg_atts['skip'];
        $orderby = empty($wporg_atts['orderby']) ? '' : $wporg_atts['orderby'];
        $emptytext = empty($wporg_atts['emptytext']) ? '' : $wporg_atts['emptytext'];

        $wporg_atts = Beyond::shiftODataAtts($wporg_atts, '1');

        $wporg_atts['expand1'] = 'subkursgruppen';
        $wporg_atts['select1'] = $select;
        $wporg_atts['filter1'] = $filter;
        $wporg_atts['top1'] = $top;
        $wporg_atts['skip1'] = $skip;
        $wporg_atts['orderby1'] = $orderby;
        $wporg_atts['select'] = 'gruppenId';
        $wporg_atts['emptytext'] = $emptytext;
        $wporg_atts['filter'] = null;
        $wporg_atts['top'] = null;
        $wporg_atts['skip'] = null;
        $wporg_atts['orderby'] = null;

        $o = $this->execDBShortcode($content, $wporg_atts, 'kursGruppen(\'' . $wporg_atts['gruppenid'] . '\')', 'subKursGruppen');
        $o = $this->replaceFormula($o);
        return $o;
    }

    public function beyondconnect_courses_groups($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        $o = $this->execDBShortcode($content, $wporg_atts, 'kursGruppen(\'' . $wporg_atts['gruppenid'] . '\')');
        $o = $this->replaceFormula($o);
        return $o;
    }

    public function beyondconnect_courses_groups_element($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        $o = $this->execElementShortcode($content, $wporg_atts, 'bc_element courses_groups', 'title');
        $o = $this->replaceFormula($o);
        return $o;
    }

    public function beyondconnect_courses_dates($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        $o = $this->execDBShortcode($content, $wporg_atts, 'kursDaten', 'value');
        $o = $this->replaceFormula($o);
        return $o;
    }

    public function beyondconnect_courses_dates_element($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        $o = $this->execElementShortcode($content, $wporg_atts, 'bc_element courses_dates', 'title');
        $o = $this->replaceFormula($o);
        return $o;
    }

    public function beyondconnect_courses_list($atts = [], $content = null, $tag = '')
    {
        $o = $this->execListShortcode($atts, $content, $tag, 'courses', 'kurse', 'kursid');
        $o = $this->replaceFormula($o);
        $o = do_shortcode($o);
        return $o;
    }

    public function beyondconnect_courses_list_element($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        global $bc_global;

        //$bc_global['bc_courses_list_element_array'][] = array_merge($wporg_atts, array('content' => trim(do_shortcode($content))));
        $bc_global['bc_courses_list_element_array'][] = array_merge($wporg_atts, array('content' => trim($content)));

        return '';
    }

    public function beyondconnect_courses_list_collapsible($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);
        $o = '';

        if ($wporg_atts['kursid'] != null)
            $content = str_ireplace('%KursID%', $wporg_atts['kursid'], $content);

        $o .= do_shortcode($content);

        $o = $this->replaceFormula($o);
        return $o;
    }

    public function beyondconnect_courses_list_popupable($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);
        $o = '';

        if ($wporg_atts['kursid'] != null)
            $content = str_ireplace('%KursID%', $wporg_atts['kursid'], $content);

        $o .= do_shortcode($content);

        $o = $this->replaceFormula($o);
        return $o;
    }

    public function beyondconnect_coursdates_list($atts = [], $content = null, $tag = '')
    {
        $o = $this->execListShortcode($atts, $content, $tag, 'coursdates', 'kursDaten', 'kursdatenrowguid');
        $o = $this->replaceFormula($o);
        $o = do_shortcode($o);
        return $o;
    }

    public function beyondconnect_coursdates_list_element($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        global $bc_global;
        $bc_global['bc_coursdates_list_element_array'][] = array_merge($wporg_atts, array('content' => trim($content)));
        return '';
    }

    public function beyondconnect_coursdates_list_collapsible($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);
        $o = '';

        if ($wporg_atts['kursdatenrowguid'] != null)
            $content = str_ireplace('%kursDatenRowguid%', $wporg_atts['kursdatenrowguid'], $content);

        $o .= do_shortcode($content);

        $o = $this->replaceFormula($o);
        return $o;
    }

    public function beyondconnect_coursdates_list_popupable($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);
        $o = '';

        if ($wporg_atts['kursdatenrowguid'] != null)
            $content = str_ireplace('%kursDatenRowguid%', $wporg_atts['kursdatenrowguid'], $content);

        $o .= do_shortcode($content);

        $o = $this->replaceFormula($o);
        return $o;
    }
}