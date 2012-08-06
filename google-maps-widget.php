<?php
/*
Plugin Name: Google Maps Widget
Plugin URI: http://google-maps-widget.webfactoryltd.com/
Description: Display a single-image super-fast loading Google map in a widget. A larger, full featured map is available on click in a lightbox. 
Author: Web factory Ltd
Version: 0.1
Author URI: http://www.webfactoryltd.com/
*/


if (!function_exists('add_action')) {
  die('Please don\'t open this file directly!');
}

define('GOOGLE_MAPS_WIDGET_CORE_VER', '0.1');
require_once 'gmw-widget.php';

class GMW {
   function init() {
      if (is_admin()) {
        // check if minimal required WP version is used
        self::check_wp_version(3.2);

        // aditional links in plugin description
        add_filter('plugin_action_links_' . basename(dirname(__FILE__)) . '/' . basename(__FILE__),
                   array(__CLASS__, 'plugin_action_links'));
        add_filter('plugin_row_meta', array(__CLASS__, 'plugin_meta_links'), 10, 2);
      } else {
          // enqueue frontend scripts
          add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'));
          add_action('wp_footer', array(__CLASS__, 'dialogs_markup'));
      }
   } // init


  // initialize widgets
  function widgets_init() {
    register_widget('GoogleMapsWidget');
  } // widgets_init


  // add settings link to plugins page
  function plugin_action_links($links) {
    $settings_link = '<a href="widgets.php" title="Configure Google Maps Widget">Widgets</a>';
    array_unshift($links, $settings_link);

    return $links;
  } // plugin_action_links


  // add links to plugin's description in plugins table
  function plugin_meta_links($links, $file) {
    $documentation_link = '<a target="_blank" href="' . plugin_dir_url(__FILE__) . '#" title="View Google Maps Widget documentation">Documentation</a>';
    $support_link = '<a target="_blank" href="http://wordpress.org/support/plugin/google-maps-widget" title="Problems? We\'re here to help!">Support</a>';

    if ($file == plugin_basename(__FILE__)) {
      //$links[] = $documentation_link;
      $links[] = $support_link;
    }

    return $links;
  } // plugin_meta_links


  // check if user has the minimal WP version required by the plugin
  function check_wp_version($min_version) {
    if (!version_compare(get_bloginfo('version'), $min_version,  '>=')) {
        add_action('admin_notices', array(__CLASS__, 'min_version_error'));
    }
  } // check_wp_version


  // display error message if WP version is too low
  function min_version_error() {
    echo '<div class="error"><p>Google Maps Widget <b>requires WordPress version 3.2</b> or higher to function properly. You\'re using WordPress version ' . get_bloginfo('version') . '. Please <a href="' . admin_url('update-core.php') . '">update it</a>.</p></div>';
  } // min_version_error


  // print dialogs markup in footer
  function dialogs_markup() {
       $out = '';
       $widgets = GoogleMapsWidget::$widgets;
       
       if (!$widgets) {
         return;
       }
       
       foreach ($widgets as $widget) {
         $out .= '<div class="gmw-dialog" style="display: none;" data-iframe-url="http://maps.google.co.uk/maps?hl=en&amp;ie=utf8&amp;output=embed&amp;iwloc=A&amp;iwd=1&amp;mrt=loc&amp;t=m&amp;q=' . urlencode($widget['address']) . '&amp;z=' . urlencode($widget['zoom_big']) . '" id="dialog-' . $widget['id'] . '" title="' . $widget['title'] . '">';
         $out .= '<div class="gmw-map"></div>';
         if ($widget['footer']) {
          $out .= '<div class="gmw-footer"><i>' . do_shortcode($widget['footer']) . '</i></div>';
         }
         $out .= "</div>\n";  
       } // foreach $widgets
       
       echo $out;
   } // run_scroller


  // enqueue frontend scripts if necessary
   function enqueue_scripts() {
     if (is_active_widget(false, false, 'googlemapswidget', true)) {
       wp_enqueue_style('wp-jquery-ui-dialog');
       wp_enqueue_script('jquery-ui-dialog');
       wp_enqueue_script('gmw', plugins_url('/js/gmw.js', __FILE__), array('jquery'), '1.0');
     }
    } // enqueue_scripts


    // helper function for creating dropdowns
    function create_select_options($options, $selected = null, $output = true) {
        $out = "\n";

        foreach ($options as $tmp) {
            if ($selected == $tmp['val']) {
                $out .= "<option selected=\"selected\" value=\"{$tmp['val']}\">{$tmp['label']}&nbsp;</option>\n";
            } else {
                $out .= "<option value=\"{$tmp['val']}\">{$tmp['label']}&nbsp;</option>\n";
            }
        } // foreach

        if ($output) {
            echo $out;
        } else {
            return $out;
        }
    } // create_select_options
} // class GMW


// hook everything up
add_action('init', array('GMW', 'init'));
add_action('widgets_init', array('GMW', 'widgets_init'));