<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc\Base;

use Inc\Beyond;
use Inc\Api\SettingsApi;
use Inc\Api\Callbacks\ShortcodesCallbacks;
use Inc\Base\BaseController;

/**
 *
 */
abstract class ShortcodesBaseController extends BaseController
{
    public $callbacks;

    public $subpages = array();
    public $codes = array();


    public function register()
    {
        $this->settings = new SettingsApi();

        $this->callbacks = new ShortcodesCallbacks();

        $this->setSubpages();

        $this->settings->addSubPages($this->subpages)->register();

        $this->setShortcodes();
        $this->addShortcodes();
        $this->addFilters();
    }

    abstract public function setShortcodes();

    public function addShortcodes()
    {
        foreach ($this->codes as $code) {
            add_shortcode($code, array($this, $code));
        }
    }

    public function setSubpages()
    {
        //$this->subpages = array(
        //    array(
        //        'parent_slug' => 'beyondconnect',
        //        'page_title' => __( 'Available Shortcodes', 'beyondconnect' ),
        //        'menu_title' => __( 'Available Shortcodes', 'beyondconnect' ),
        //        'capability' => 'manage_options',
        //        'menu_slug' => 'beyondconnect_shortcodes_menu',
        //        'callback' => array( $this->callbacks, 'ShortcodesPage' )
        //    )
        //);
    }

    public function addFilters()
    {
        add_filter('the_content', array($this, 'shortcode_empty_paragraph_fix'), 15);
    }

    function shortcode_empty_paragraph_fix($content)
    {
        foreach ($this->codes as $code) {

            $array = array(
                '<p>[' . $code => '[' . $code,
                '<p>[/' . $code => '[/' . $code,
                $code . ']</p>' => $code . ']',
                $code . ']<br />' => $code . ']'
            );

            $content = strtr($content, $array);
        }

        return $content;
    }

    protected function replaceQueryStringValues($source)
    {
        foreach ($_GET as $key => $value) {
            if (is_array($source)) {
                foreach ($source as $k => $v) {
                    $v = str_ireplace('{' . $key . '}', urldecode($value), $v);
                    $v = str_ireplace('{querystring_' . $key . '}', urldecode($value), $v);
                    $source[$k] = $v;
                }
            } else {
                $source = str_ireplace('{' . $key . '}', urldecode($value), $source);
                $source = str_ireplace('{querystring_' . $key . '}', urldecode($value), $source);
            }
        }
        return $source;
    }

    protected function replaceGlobalVariableValues($source)
    {
        global $bc_global;

        if (empty($bc_global['bc_shortcode']))
            return $source;

        foreach ($bc_global['bc_shortcode'] as $key => $value) {
            $source = str_ireplace('{' . $key . '}', urldecode($value), $source);
            $source = str_ireplace('{global_' . $key . '}', urldecode($value), $source);
        }

        return $source;
    }

    protected function replaceFormula($content)
    {

        while (!empty(Beyond::getStringBetween($content, '%(', ')%'))) {
            $tocalccontent = Beyond::getStringBetween($content, '%(', ')%');
            $calcedcontent = '';
            if (!empty($tocalccontent) && !strpos($tocalccontent, '%')) {
                $decodedcontent = $tocalccontent;
                $decodedcontent = str_replace('&#34;', '"', $decodedcontent);
                $decodedcontent = str_replace('&#8220;', '"', $decodedcontent);
                $decodedcontent = str_replace('&#8221;', '"', $decodedcontent);
                $decodedcontent = str_replace('&#8216;', '\'', $decodedcontent);
                $decodedcontent = str_replace('&#8217;', '\'', $decodedcontent);

                //echo "<pre><code>". "" . htmlspecialchars($tocalccontent, ENT_QUOTES) ."</code></pre>";

                eval ('$calcedcontent = ' . $decodedcontent . ';');
            }

            if (!empty($calcedcontent))
                $content = str_replace('%(' . $tocalccontent . ')%', $calcedcontent, $content);
        }

        return $content;
    }

    protected function replaceFieldValues($content, $record, $prefix)
    {
        $record = array_change_key_case((array)$record, CASE_LOWER);
        $prefix = strtolower($prefix);

        foreach ($record as $key => $value) {
            if (is_array($record[$key]))
                continue;

            $content = str_ireplace('%' . $key . ' \# 0.00%', sprintf('%01.2f', $record[$key]), $content);
            $content = str_ireplace('%' . $prefix . '_' . $key . ' \# 0.00%', sprintf('%01.2f', $record[$key]), $content);

            $date = empty($record[$key]) ? null : date_create($record[$key]);
            if ($date) {
                $content = str_ireplace('%' . $key . ' \@ dd.MM.yyyy HH:mm%', $date->format("d.m.Y H:i"), $content);
                $content = str_ireplace('%' . $prefix . '_' . $key . ' \@ dd.MM.yyyy HH:mm%', $date->format("d.m.Y H:i"), $content);

                $content = str_ireplace('%' . $key . ' \@ dd.MM.yyyy%', $date->format("d.m.Y"), $content);
                $content = str_ireplace('%' . $prefix . '_' . $key . ' \@ dd.MM.yyyy%', $date->format("d.m.Y"), $content);
            } else {
                $content = str_ireplace('%' . $key . ' \@ dd.MM.yyyy HH:mm%', '', $content);
                $content = str_ireplace('%' . $prefix . '_' . $key . ' \@ dd.MM.yyyy HH:mm%', '', $content);

                $content = str_ireplace('%' . $key . ' \@ dd.MM.yyyy%', '', $content);
                $content = str_ireplace('%' . $prefix . '_' . $key . ' \@ dd.MM.yyyy%', '', $content);
            }

            if (is_bool($record[$key])) {
                $content = str_ireplace('%' . $key . '%', ($record[$key] ? '1' : '0'), $content);
                $content = str_ireplace('%' . $prefix . '_' . $key . '%', ($record[$key] ? '1' : '0'), $content);
            }

            $content = str_ireplace('%' . $key . '%', $record[$key], $content);
            $content = str_ireplace('%' . $prefix . '_' . $key . '%', $record[$key], $content);
        }
        return $content;
    }

    protected function getShortcodeAtts($atts = [], $tag = '')
    {
        // normalize attribute keys, lowercase
        $atts = array_change_key_case((array)$atts, CASE_LOWER);

        $allowedatts = array();
        foreach ($atts as $key => $value) {
            $allowedatts[$key] = null;
        }

        $wporg_atts = shortcode_atts($allowedatts, $atts, $tag);

        $wporg_atts = $this->replaceQueryStringValues($wporg_atts);
        $wporg_atts = $this->replaceGlobalVariableValues($wporg_atts);

        $wporg_atts = array_change_key_case($wporg_atts, CASE_LOWER);

        return $wporg_atts;
    }

    protected function execDBShortcode($content = null, $wporg_atts = [], $odatarequest = '', $odatareturn = '')
    {
        $option = get_option('beyondconnect_option');

        $entityname = strtolower($odatareturn != "value" && !empty($odatareturn) ? $odatareturn : strtok($odatarequest, '('));
        $tablename = strtolower($entityname);

        $content = $this->replaceQueryStringValues($content);
        $content = $this->replaceGlobalVariableValues($content);


        global $bc_global;

        if (!empty($wporg_atts['expandedfrom'])) {
            foreach ($bc_global['bc_tbl' . $wporg_atts['expandedfrom']] as $from) {
                $from = array_change_key_case((array)$from, CASE_LOWER);

                $expandedLinkFieldName = $wporg_atts['expandedlinkfieldname'];
                $expandedLinkFieldName = strtolower($expandedLinkFieldName);

                if (strcasecmp($from[$expandedLinkFieldName], $wporg_atts['expandedlinkfieldvalue']) === 0) {
                    $bc_global['bc_tbl' . $tablename] = $from[$entityname];
                }
            }
        } else {
            $values = Beyond::getValues(Beyond::getODataString($odatarequest, $wporg_atts), $odatareturn);
            if (!empty($values)) {
                $values = array_change_key_case((array)$values, CASE_LOWER);
                $bc_global['bc_tbl' . $tablename] = $values;
            }
        }

        if (empty($bc_global['bc_tbl' . $tablename])) {
            if (empty($wporg_atts['emptytext']))
                return '';
            else
                return '<div class="bc_empty">' . $wporg_atts['emptytext'] . '</div>';
        }

        $o = '';

        foreach ($bc_global['bc_tbl' . $tablename] as $record) {
            $record = array_change_key_case((array)$record);

            if (!is_null($content)) {
                $newcontent = $content;
                $newcontent = $this->replaceFieldValues($newcontent, $record, $tablename);

                $o .= do_shortcode($newcontent);
            }
        }
        return $o;
    }

    protected function execElementShortcode($content = null, $wporg_atts = [], $classname = '', $classextension = '')
    {
        $option = get_option('beyondconnect_option');

        $content = $this->replaceQueryStringValues($content);
        $content = $this->replaceGlobalVariableValues($content);

        $content = trim(do_shortcode($content));

        $visible = empty($wporg_atts['visible']) ? 'true' : $wporg_atts['visible'];
        $visible = $this->replaceQueryStringValues($visible);
        $visible = $this->replaceGlobalVariableValues($visible);
        eval ('$valvisible = ' . $visible . ';');
        $visible = boolval($valvisible);

        $alternativeclass = empty($wporg_atts['alternativeclass']) ? 'false' : $wporg_atts['alternativeclass'];
        $alternativeclass = $this->replaceQueryStringValues($alternativeclass);
        $alternativeclass = $this->replaceGlobalVariableValues($alternativeclass);
        eval ('$valalternativeclass = ' . $alternativeclass . ';');
        $alternativeclass = boolval($valalternativeclass);

        $o = '';
        $o .= "<div class='" . $classname . (!empty($classextension) && !empty($wporg_atts[$classextension]) ? " " . esc_attr($wporg_atts[$classextension]) : "") . ($alternativeclass ? " alternative" : "") . "'>";

        if (!empty($wporg_atts['link']) && $visible) {
            $target = empty($wporg_atts['target']) ? '_self' : $wporg_atts['target'];
            $o .= '<a href="' . $wporg_atts['link'] . '" target="' . $target . '">' . $content . '</a>';
        } else {
            $o .= $visible ? $content : (!empty($wporg_atts['alternativetexttovisible']) ? $wporg_atts['alternativetexttovisible'] : "&nbsp;");
        }
        $o .= '</div>';

        return $o;
    }

    protected function execListShortcode($atts = [], $content = null, $tag = '', $elementname = '', $tablename = '', $primarykey = '')
    {
        $option = get_option('beyondconnect_option');

        // normalize attribute keys, lowercase
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        $rendermode = $wporg_atts['rendermode'];

        // variables to lowercase
        $elementname = strtolower($elementname);
        $tablename = strtolower($tablename);
        $primarykey = strtolower($primarykey);
        $rendermode = strtolower($rendermode);

        $content = $this->replaceQueryStringValues($content);
        $content = $this->replaceGlobalVariableValues($content);

        global $bc_global;
        $bc_global['bc_' . $elementname . '_list_element_array'] = array(); // clear the element array

        // execute beyondconnect_courses_list_element shortcode first to get the title and content - acts on global $single_tab_array
        $repl_content = preg_replace('/\[beyondconnect_' . $elementname . '_list_collapsible(.*?)\[\/beyondconnect_' . $elementname . '_list_collapsible\]/s', '', $content);
        $repl_content = preg_replace('/\[beyondconnect_' . $elementname . '_list_popupable(.*?)\[\/beyondconnect_' . $elementname . '_list_popupable\]/s', '', $repl_content);

        if ($tablename === 'warenkorb' && get_transient('bc_cart' . Beyond::getVisitorID()) !== false) {
            $values = get_transient('bc_cart' . Beyond::getVisitorID());
            $values = array_change_key_case($values, CASE_LOWER);
            $bc_global['bc_tbl' . $tablename] = $values;
        } else if (!empty($wporg_atts['expandedfrom'])) {
            foreach ($bc_global['bc_tbl' . $wporg_atts['expandedfrom']] as $from) {
                $from = array_change_key_case((array)$from, CASE_LOWER);

                $expandedLinkFieldName = $wporg_atts['expandedlinkfieldname'];
                $expandedLinkFieldName = strtolower($expandedLinkFieldName);

                if (strcasecmp($from[$expandedLinkFieldName], $wporg_atts['expandedlinkfieldvalue']) === 0) {
                    $bc_global['bc_tbl' . $tablename] = $from[$tablename];
                }
            }
        } else {
            $values = Beyond::getValues(Beyond::getODataString($tablename, $wporg_atts), 'value');
            if (empty($values)) {
                $bc_global['bc_tbl' . $tablename] = array();
            } else {
                $values = array_change_key_case((array)$values, CASE_LOWER);
                $bc_global['bc_tbl' . $tablename] = $values;
            }
        }

        if (empty($bc_global['bc_tbl' . $tablename])) {
            if (empty($wporg_atts['emptytext']))
                return '';
            else
                return '<div class="bc_list_empty ' . $elementname . '">' . $wporg_atts['emptytext'] . '</div>';
        } else {
            if (empty($wporg_atts['filledtext']))
                $o = '';
            else
                $o = '<div class="bc_list_filled ' . $elementname . '">' . $wporg_atts['filledtext'] . '</div>';
        }

        do_shortcode($repl_content);

        $o .= '<' . ($rendermode === 'div' ? 'div' : "table") . ' class="bc_list_table ' . $elementname . '">';
        $o .= '<' . ($rendermode === 'div' ? 'div' : "thead") . ' class="bc_list_head ' . $elementname . '">';
        $o .= '<' . ($rendermode === 'div' ? 'div' : 'tr') . ' class="bc_list_head_row ' . $elementname . '">';

        foreach ($bc_global['bc_' . $elementname . '_list_element_array'] as $element_attr_array) {
            $headtitle = empty($element_attr_array['title']) ? '' : $element_attr_array['title'];
            $headcustomclass = empty($element_attr_array['customclass']) ? '' : $element_attr_array['customclass'];

            $o .= '<' . ($rendermode === 'div' ? 'div' : "th") . ' class="bc_list_head_element ' . $elementname . (empty($headcustomclass) ? '' : (' ' . $headcustomclass)) . ' ' . esc_attr($headtitle) . '">' . $headtitle . '</' . ($rendermode === 'div' ? 'div' : "th") . '>';
        }

        $o .= '</' . ($rendermode === 'div' ? 'div' : 'tr') . '>';
        $o .= '</' . ($rendermode === 'div' ? 'div' : "thead") . '>';
        $o .= '<' . ($rendermode === 'div' ? 'div' : "tbody") . ' class="bc_list_body ' . $elementname . '">';


        $hasPopupable = false;

        foreach ($bc_global['bc_tbl' . $tablename] as $record) {
            $record = array_change_key_case((array)$record, CASE_LOWER);

            $hasCollapsible = false;

            $rowprimary = $record[$primarykey];
            $rowcontent = $content;

            $o .= '<' . ($rendermode === 'div' ? 'div' : 'tr') . ' class="bc_list_body_row ' . $elementname . '">';

            foreach ($bc_global['bc_' . $elementname . '_list_element_array'] as $element_attr_array) {
                $fieldcontent = !empty($element_attr_array['content']) ? $element_attr_array['content'] : '';
                $fieldlink = !empty($element_attr_array['link']) ? $element_attr_array['link'] : '';
                $fieldcollapsible = !empty($element_attr_array['collapsible']) ? $element_attr_array['collapsible'] : 'false';
                $fieldpopupable = !empty($element_attr_array['popupable']) ? $element_attr_array['popupable'] : 'false';
                $fieldvisible = !empty($element_attr_array['visible']) ? $element_attr_array['visible'] : 'true';
                $fieldcustomclass = !empty($element_attr_array['customclass']) ? $element_attr_array['customclass'] : '';
                $fieldalternativeclass = !empty($element_attr_array['alternativeclass']) ? $element_attr_array['alternativeclass'] : 'false';
                $fieldalternativetexttovisible = !empty($element_attr_array['alternativetexttovisible']) ? $element_attr_array['alternativetexttovisible'] : '';
                $fieldevent = !empty($element_attr_array['event']) ? $element_attr_array['event'] : '';
                $fieldimage = !empty($element_attr_array['image']) ? $element_attr_array['image'] : '';
                $fieldtitle = !empty($element_attr_array['title']) ? $element_attr_array['title'] : '';

                $fieldevent = strtolower($fieldevent);

                $rowcontent = $this->replaceFieldValues($rowcontent, $record, $tablename);
                $fieldcontent = $this->replaceFieldValues($fieldcontent, $record, $tablename);
                $fieldlink = $this->replaceFieldValues($fieldlink, $record, $tablename);
                $fieldcollapsible = $this->replaceFieldValues($fieldcollapsible, $record, $tablename);
                $fieldpopupable = $this->replaceFieldValues($fieldpopupable, $record, $tablename);
                $fieldvisible = $this->replaceFieldValues($fieldvisible, $record, $tablename);
                $fieldcustomclass = $this->replaceFieldValues($fieldcustomclass, $record, $tablename);
                $fieldalternativeclass = $this->replaceFieldValues($fieldalternativeclass, $record, $tablename);

                eval ('$valcollapsible = ' . $fieldcollapsible . ';');
                $fieldcollapsible = boolval($valcollapsible);

                eval ('$valpopupable = ' . $fieldpopupable . ';');
                $fieldpopupable = boolval($valpopupable);

                eval ('$valvisible = ' . $fieldvisible . ';');
                $fieldvisible = boolval($valvisible);

                eval ('$valalternativeclass = ' . $fieldalternativeclass . ';');
                $fieldalternativeclass = boolval($valalternativeclass);

                $o .= '<' . ($rendermode === 'div' ? 'div' : 'td');
                $o .= " class='bc_list_body_element " . $elementname . (empty($fieldcustomclass) ? '' : (' ' . $fieldcustomclass)) . ($fieldalternativeclass ? " alternative " : " ") . esc_attr($fieldtitle) . "'>";
                if ($fieldlink != NULL && $fieldvisible) {
                    $fieldtarget = empty($element_attr_array['target']) ? '_self' : $element_attr_array['target'];
                    $o .= '<a href="' . $fieldlink . '" target="' . $fieldtarget . '">' . $fieldcontent . '</a>';
                } else {
                    $o .= $fieldvisible ? $fieldcontent : (!empty($fieldalternativetexttovisible) ? $fieldalternativetexttovisible : "&nbsp;");
                }
                if ($fieldcollapsible && $fieldvisible) {
                    $hasCollapsible = true;

                    if ($fieldevent === 'mouseover') {
                        $o .= "&nbsp;<img id=\"bc_list_collapsible_image_" . $rowprimary . "\" src=\"" . $fieldimage . "\" class='bc_list_collapsible_image " . $elementname . "' alt='&darr;' onmouseover=\"bcCollapsibleRow_Toggle('bc_list_collapsible_image " . $elementname . "', this, '" . $rowprimary . "');\" onmouseout=\"bcCollapsibleRow_Toggle('bc_list_collapsible_image " . $elementname . "', this, '" . $rowprimary . "');\" />&nbsp;";
                    } else if ($fieldevent === 'click') {
                        $o .= "&nbsp;<img id=\"bc_list_collapsible_image_" . $rowprimary . "\" src=\"" . $fieldimage . "\" class='bc_list_collapsible_image " . $elementname . "' alt='&darr;' onclick=\"bcCollapsibleRow_Toggle('bc_list_collapsible_image " . $elementname . "', this, '" . $rowprimary . "'); return false;\" />&nbsp;";
                    }
                } else if ($fieldpopupable && $fieldvisible) {
                    $hasPopupable = true;

                    if ($fieldevent === 'mouseover') {
                        $o .= "&nbsp;<img id=\"bc_list_popupable_image_" . $rowprimary . "\" src=\"" . $fieldimage . "\" class='bc_list_popupable_image " . $elementname . "' alt='&darr;' onmouseover=\"bcPopup_Toggle('bc_list_popupable_image " . $elementname . "', this, '" . $rowprimary . "');\" onmouseout=\"bcPopup_Toggle('bc_list_popupable_image " . $elementname . "', this, '" . $rowprimary . "');\" />&nbsp;";
                    } else if ($fieldevent === 'click') {
                        $o .= "&nbsp;<img id=\"bc_list_popupable_image_" . $rowprimary . "\" src=\"" . $fieldimage . "\" class='bc_list_popupable_image " . $elementname . "' alt='&darr;' onclick=\"bcPopup_Toggle('bc_list_popupable_image " . $elementname . "', this, '" . $rowprimary . "'); return false;\" />&nbsp;";
                    } else {
                        $o .= "&nbsp;<a href=\"#bc_list_popup_" . $rowprimary . "\" class='bc_list_popupable_anchor " . $elementname . "'><img id=\"bc_list_popupable_image_" . $rowprimary . "\" src=\"" . $fieldimage . "\" class='bc_list_popupable_image " . $elementname . "' alt='&darr;' /></a>&nbsp;";
                    }
                }
                
                
		            //Collapsible and Rendermode div
		            if ($fieldcollapsible && $fieldvisible && $rendermode === 'div') {
		            		//$o .= "<div>fst</div>";
		                $cols = sizeof($bc_global['bc_' . $elementname . '_list_element_array']);
		
		                $o .= '<' . ($rendermode === 'div' ? "div id=\"bc_list_collapsible_row_" . $rowprimary . "\" style=\"display:none;\"" : "tr id=\"bc_list_collapsible_row_" . $rowprimary . "\" style=\"display:none;\"") . ' class="bc_list_collapsible_row ' . $elementname . '">';
		                $o .= '<' . ($rendermode === 'div' ? 'div' : "td colspan='" . $cols . "'") . ' class="bc_list_collapsible_element ' . $elementname . '">';
		
		                $repl_content = preg_replace('/\[beyondconnect_' . $elementname . '_list_element(.*?)\[\/beyondconnect_' . $elementname . '_list_element\]/s', '', $rowcontent);
		                $repl_content = preg_replace('/\[beyondconnect_' . $elementname . '_list_popupable(.*?)\[\/beyondconnect_' . $elementname . '_list_popupable\]/s', '', $repl_content);
		
		                $o .= do_shortcode($repl_content);
		
		                $o .= '</' . ($rendermode === 'div' ? 'div' : 'td') . '>';
		                $o .= '</' . ($rendermode === 'div' ? 'div' : 'tr') . '>';
		            }
                
                
                $o .= '</' . ($rendermode === 'div' ? 'div' : 'td') . '>';
            }
            $o .= '</' . ($rendermode === 'div' ? 'div' : 'tr') . '>';

            //Collapsible and Rendermode table
            if ($hasCollapsible && $rendermode !== 'div') {
                $cols = sizeof($bc_global['bc_' . $elementname . '_list_element_array']);

                $o .= '<' . ($rendermode === 'div' ? "div id=\"bc_list_collapsible_row_" . $rowprimary . "\" style=\"display:none;\"" : "tr id=\"bc_list_collapsible_row_" . $rowprimary . "\" style=\"display:none;\"") . ' class="bc_list_collapsible_row ' . $elementname . '">';
                $o .= '<' . ($rendermode === 'div' ? 'div' : "td colspan='" . $cols . "'") . ' class="bc_list_collapsible_element ' . $elementname . '">';

                $repl_content = preg_replace('/\[beyondconnect_' . $elementname . '_list_element(.*?)\[\/beyondconnect_' . $elementname . '_list_element\]/s', '', $rowcontent);
                $repl_content = preg_replace('/\[beyondconnect_' . $elementname . '_list_popupable(.*?)\[\/beyondconnect_' . $elementname . '_list_popupable\]/s', '', $repl_content);

                $o .= do_shortcode($repl_content);

                $o .= '</' . ($rendermode === 'div' ? 'div' : 'td') . '>';
                $o .= '</' . ($rendermode === 'div' ? 'div' : 'tr') . '>';
            }
        }

        $o .= '</' . ($rendermode === 'div' ? 'div' : 'tbody') . '>';
        $o .= '<' . ($rendermode === 'div' ? 'div' : 'tfoot') . ' class="bc_list_foot ' . $elementname . '">';
        $o .= '</' . ($rendermode === 'div' ? 'div' : 'tfoot') . '>';
        $o .= '</' . ($rendermode === 'div' ? 'div' : 'table') . '>';

        //Popupable
        if ($hasPopupable) {
            foreach ($bc_global['bc_tbl' . $tablename] as $record) {
                $record = array_change_key_case($record);

                $rowprimary = $record[$primarykey];

                $rowcontent = $content;
                $rowcontent = $this->replaceFieldValues($rowcontent, $record, $tablename);

                $o .= "<div id=\"bc_list_popup_" . $rowprimary . "\" class=\"bc_list_popup " . $elementname . "\">";
                $o .= "<div>";
                $o .= "<a href='#bc_list_popup_close' class='bc_list_popup_close " . $elementname . "'>X</a>";

                $rowcontent = preg_replace('/\[beyondconnect_' . $elementname . '_list_element(.*?)\[\/beyondconnect_' . $elementname . '_list_element\]/s', '', $rowcontent);
                $rowcontent = preg_replace('/\[beyondconnect_' . $elementname . '_list_collapsible(.*?)\[\/beyondconnect_' . $elementname . '_list_collapsible\]/s', '', $rowcontent);

                $o .= do_shortcode($rowcontent);

                $o .= "</div>";
                $o .= "</div>";
            }
        }
        return $o;
    }
}