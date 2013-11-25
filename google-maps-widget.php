<?php
/*
Plugin Name: Google Maps Widget
Plugin URI: http://www.googlemapswidget.com/
Description: Display a single-image super-fast loading Google map in a widget. A larger, full featured map is available on click in a lightbox.
Author: Web factory Ltd
Version: 1.15
Author URI: http://www.webfactoryltd.com/

  Copyright 2013  Web factory Ltd  (email : info@webfactoryltd.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


if (!function_exists('add_action')) {
  die('Please don\'t open this file directly!');
}


define('GMW_VER', '1.15');
require_once 'gmw-widget.php';


class GMW {
  // hook everything up
   static function init() {
      if (is_admin()) {
        // check if minimal required WP version is used
        self::check_wp_version(3.3);

        // aditional links in plugin description
        add_filter('plugin_action_links_' . basename(dirname(__FILE__)) . '/' . basename(__FILE__),
                   array(__CLASS__, 'plugin_action_links'));
        add_filter('plugin_row_meta', array(__CLASS__, 'plugin_meta_links'), 10, 2);

        // enqueue admin scripts
        add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_enqueue_scripts'));
      } else {
        // enqueue frontend scripts
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'));
        add_action('wp_footer', array(__CLASS__, 'dialogs_markup'));
      }
  } // init


  // textdomain has to be loaded earlier
  static function plugins_loaded() {
    load_plugin_textdomain('google-maps-widget', false, basename(dirname(__FILE__)) . '/lang');
  } // plugins_loaded


  // initialize widgets
  static function widgets_init() {
    register_widget('GoogleMapsWidget');
  } // widgets_init


  // add settings link to plugins page
  static function plugin_action_links($links) {
    $settings_link = '<a href="' . admin_url('widgets.php') . '" title="' . __('Configure Google Maps Widget', 'google-maps-widget') . '">' . __('Widgets', 'google-maps-widget') . '</a>';
    array_unshift($links, $settings_link);

    return $links;
  } // plugin_action_links


  // add links to plugin's description in plugins table
  static function plugin_meta_links($links, $file) {
    $documentation_link = '<a target="_blank" href="' . plugin_dir_url(__FILE__) . '#" title="' . __('View Google Maps Widget documentation', 'google-maps-widget') . '">'. __('Documentation', 'google-maps-widget') . '</a>';
    $support_link = '<a target="_blank" href="http://wordpress.org/support/plugin/google-maps-widget" title="' . __('Problems? We are here to help!', 'google-maps-widget') . '">' . __('Support', 'google-maps-widget') . '</a>';

    if ($file == plugin_basename(__FILE__)) {
      //$links[] = $documentation_link;
      $links[] = $support_link;
    }

    return $links;
  } // plugin_meta_links


  // check if user has the minimal WP version required by the plugin
  static function check_wp_version($min_version) {
    if (!version_compare(get_bloginfo('version'), $min_version,  '>=')) {
        add_action('admin_notices', array(__CLASS__, 'min_version_error'));
    }
  } // check_wp_version


  // display error message if WP version is too low
  static function min_version_error() {
    echo '<div class="error"><p>' . sprintf('Google Maps Widget <b>requires WordPress version 3.3</b> or higher to function properly. You are using WordPress version %s. Please <a href="%s">update it</a>.', get_bloginfo('version'), admin_url('update-core.php')) . '</p></div>';
  } // min_version_error


  // print dialogs markup in footer
  static function dialogs_markup() {
       $out = '';
       $widgets = GoogleMapsWidget::$widgets;

       if (!$widgets) {
         wp_dequeue_script('gmw');
         wp_dequeue_script('gmw-fancybox');
         return;
       }

       foreach ($widgets as $widget) {
         if ($widget['bubble']) {
           $iwloc = 'addr';
         } else {
           $iwloc = 'near';
         }
         if ($widget['ll']) {
           $ll = '&amp;ll=' . $widget['ll'];
         } else {
           $ll = '';
         }

         $lang = substr(@$_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
         if (!$lang) {
           $lang = 'en';
         }

         $map_url = '//maps.google.com/maps?hl=' . $lang . '&amp;ie=utf8&amp;output=embed&amp;iwloc=' . $iwloc . '&amp;iwd=1&amp;mrt=loc&amp;t=' . $widget['type'] . '&amp;q=' . urlencode(remove_accents($widget['address'])) . '&amp;z=' . urlencode($widget['zoom']) . $ll;

         $out .= '<div class="gmw-dialog" style="display: none;" data-map-height="' . $widget['height'] . '" data-map-width="' . $widget['width'] . '" data-map-skin="' . $widget['skin'] . '" data-map-iframe-url="' . $map_url . '" id="gmw-dialog-' . $widget['id'] . '" title="' . esc_attr($widget['title']) . '">';
         if ($widget['header']) {
          $out .= '<div class="gmw-header"><i>' . do_shortcode($widget['header']) . '</i></div>';
         }
         $out .= '<div class="gmw-map"></div>';
         if ($widget['footer']) {
          $out .= '<div class="gmw-footer"><i>' . do_shortcode($widget['footer']) . '</i></div>';
         }
         $out .= "</div>\n";
       } // foreach $widgets

       echo $out;
   } // run_scroller


   // enqueue frontend scripts if necessary
   static function enqueue_scripts() {
     if (is_active_widget(false, false, 'googlemapswidget', true)) {
       wp_enqueue_style('gmw', plugins_url('/css/gmw.css', __FILE__), array(), GMW_VER);
       wp_enqueue_script('gmw-fancybox', plugins_url('/js/jquery.fancybox.pack.js', __FILE__), array('jquery'), GMW_VER, true);
       wp_enqueue_script('gmw', plugins_url('/js/gmw.js', __FILE__), array('jquery'), GMW_VER, true);
     }
    } // enqueue_scripts


    // enqueue CSS and JS scripts on widgets page
    static function admin_enqueue_scripts() {
      if (self::is_plugin_admin_page()) {
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('gmw-cookie', plugins_url('js/jquery.cookie.js', __FILE__), array('jquery'), GMW_VER, true);
        wp_enqueue_script('gmw-admin', plugins_url('js/gmw-admin.js', __FILE__), array('jquery'), GMW_VER, true);
        wp_enqueue_style('gmw-admin', plugins_url('css/gmw-admin.css', __FILE__), array(), GMW_VER);
      } // if
    } // admin_enqueue_scripts


    // check if plugin's admin page is shown
    static function is_plugin_admin_page() {
      $current_screen = get_current_screen();

      if ($current_screen->id == 'widgets') {
        return true;
      } else {
        return false;
      }
    } // is_plugin_admin_page


    // helper function for creating dropdowns
    static function create_select_options($options, $selected = null, $output = true) {
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


  static function get_coordinates($address, $force_refresh = false) {
    $address_hash = md5('gmw' . $address);

    if ($force_refresh || ($coordinates = get_transient($address_hash)) === false) {
      $url = 'http://maps.googleapis.com/maps/api/geocode/xml?address=' . urlencode($address) . '&sensor=false';

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $xml = curl_exec($ch);
      $ch_info = curl_getinfo($ch);
      curl_close($ch);

      if ($ch_info['http_code'] == 200) {
        $data = new SimpleXMLElement($xml);
        if ($data->status == 'OK') {
          $cache_value['lat']     = (string) $data->result->geometry->location->lat;
          $cache_value['lng']     = (string) $data->result->geometry->location->lng;
          $cache_value['address'] = (string) $data->result->formatted_address;

          // cache coordinates for 3 months
          set_transient($address_hash, $cache_value, 3600*24*30*3);
          $data = $cache_value;
        } elseif (!$data->status) {
          return false;
        } else {
          return false;
        }
      } else {
         return false;
      }
    } else {
       // data is cached, get it
       $data = get_transient($address_hash);
    }

    return $data;
  } // get_coordinates
} // class GMW


// hook everything up
add_action('init', array('GMW', 'init'));
add_action('plugins_loaded', array('GMW', 'plugins_loaded'));
add_action('widgets_init', array('GMW', 'widgets_init'));