<?php
/*
 * Google Maps Widget
 * (c) Web factory Ltd, 2012
 */
 
class GoogleMapsWidget extends WP_Widget {
  static $widgets = array();
  
  function GoogleMapsWidget() {
    $widget_ops = array('classname' => 'google-maps-widget', 'description' => 'Displays a map image thumbnail with a larger map available in a lightbox.');
    $control_ops = array('width' => 400, 'height' => 350);
    $this->WP_Widget('GoogleMapsWidget', 'Google Maps Widget', $widget_ops, $control_ops);
  }
 
  function form($instance) {
    $instance = wp_parse_args((array) $instance,
                              array('title' => 'Map',
                                    'address' => 'New York, USA',
                                    'thumb_pin_color' => 'red',
                                    'thumb_width' => 250,
                                    'thumb_height' => 250,
                                    'thumb_type' => 'roadmap',
                                    'thumb_zoom' => '13',
                                    'lightbox_width' => 550,
                                    'lightbox_height' => 550,
                                    'lightbox_type' => 'roadmap',
                                    'lightbox_zoom' => '14',
                                    'footer' => ''));
    $title = $instance['title'];
    $footer = $instance['footer'];
    $address = $instance['address'];
    $thumb_pin_color = $instance['thumb_pin_color'];
    $thumb_width = $instance['thumb_width'];
    $thumb_height = $instance['thumb_height'];
    $thumb_type = $instance['thumb_type'];
    $thumb_zoom = $instance['thumb_zoom'];
    $lightbox_width = $instance['lightbox_width'];
    $lightbox_height = $instance['lightbox_height'];
    $lightbox_type = $instance['lightbox_type'];
    $lightbox_zoom = $instance['lightbox_zoom'];

    $map_types_thumb = array(array('val' => 'roadmap', 'label' => 'Road map'),
                             array('val' => 'satellite', 'label' => 'Satellite'),
                             array('val' => 'terrain', 'label' => 'Terrain'),
                             array('val' => 'hybrid', 'label' => 'Hybrid'));

    $map_types_lightbox = array(array('val' => 'm', 'label' => 'Road map'),
                                array('val' => 'k', 'label' => 'Satellite'),
                                array('val' => 'p', 'label' => 'Terrain'),
                                array('val' => 'h', 'label' => 'Hybrid'));

    $pin_colors = array(array('val' => 'black', 'label' => 'Black'),
                        array('val' => 'brown', 'label' => 'Brown'),
                        array('val' => 'green', 'label' => 'Green'),
                        array('val' => 'purple', 'label' => 'Purple'),
                        array('val' => 'yellow', 'label' => 'Yellow'),
                        array('val' => 'blue', 'label' => 'Blue'),
                        array('val' => 'gray', 'label' => 'Gray'),
                        array('val' => 'orange', 'label' => 'Orange'),
                        array('val' => 'red', 'label' => 'Red'),
                        array('val' => 'white', 'label' => 'White'));

    $zoom_levels = array(array('val' => '0', 'label' => '0 - entire world'));
    for ($tmp = 1; $tmp <= 20; $tmp++) {
      $zoom_levels[] = array('val' => $tmp, 'label' => $tmp);
    }
    $zoom_levels[] = array('val' => '21', 'label' => '21 - street view');

    echo '<p><label for="' . $this->get_field_id('title') . '">Title:</label><input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . esc_attr($title) . '" /></p>';
    
    echo '<p><label for="' . $this->get_field_id('address') . '">Address:</label><input class="widefat" id="' . $this->get_field_id('address') . '" name="' . $this->get_field_name('address') . '" type="text" value="' . esc_attr($address) . '" /></p>';

    echo '<p><label for="' . $this->get_field_id('thumb_pin_color') . '">Thumbnail Map Pin Color: </label>';
    echo '<select id="' . $this->get_field_id('thumb_pin_color') . '" name="' . $this->get_field_name('thumb_pin_color') . '">';
    GMW::create_select_options($pin_colors, $thumb_pin_color);
    echo '</select></p>';

    echo '<p><label for="' . $this->get_field_id('thumb_width') . '">Thumbnail Map Size: </label>';
    echo '<input class="small-text" id="' . $this->get_field_id('thumb_width') . '" name="' . $this->get_field_name('thumb_width') . '" type="text" value="' . esc_attr($thumb_width) . '" /> x ';
    echo '<input class="small-text" id="' . $this->get_field_id('thumb_height') . '" name="' . $this->get_field_name('thumb_height') . '" type="text" value="' . esc_attr($thumb_height) . '" />';
    echo '</p>';

    echo '<p><label for="' . $this->get_field_id('lightbox_width') . '">Lightbox Map Size: </label>';
    echo '<input class="small-text" id="' . $this->get_field_id('lightbox_width') . '" name="' . $this->get_field_name('lightbox_width') . '" type="text" value="' . esc_attr($lightbox_width) . '" /> x ';
    echo '<input class="small-text" id="' . $this->get_field_id('lightbox_height') . '" name="' . $this->get_field_name('lightbox_height') . '" type="text" value="' . esc_attr($lightbox_height) . '" />';
    echo '</p>';
    
    echo '<p><label for="' . $this->get_field_id('thumb_zoom') . '">Zoom Level for Thumbnail Map: </label>';
    echo '<select id="' . $this->get_field_id('thumb_zoom') . '" name="' . $this->get_field_name('thumb_zoom') . '">';
    GMW::create_select_options($zoom_levels, $thumb_zoom);
    echo '</select></p>';
    
    echo '<p><label for="' . $this->get_field_id('lightbox_zoom') . '">Zoom Level for Lightbox Map: </label>';
    echo '<select id="' . $this->get_field_id('lightbox_zoom') . '" name="' . $this->get_field_name('lightbox_zoom') . '">';
    GMW::create_select_options($zoom_levels, $lightbox_zoom);
    echo '</select></p>';

    echo '<p><label for="' . $this->get_field_id('thumb_type') . '">Thumbnail Map Type: </label>';
    echo '<select id="' . $this->get_field_id('thumb_type') . '" name="' . $this->get_field_name('thumb_type') . '">';
    GMW::create_select_options($map_types_thumb, $thumb_type);
    echo '</select></p>';

    echo '<p><label for="' . $this->get_field_id('lightbox_type') . '">Lightbox Map Type: </label>';
    echo '<select id="' . $this->get_field_id('lightbox_type') . '" name="' . $this->get_field_name('lightbox_type') . '">';
    GMW::create_select_options($map_types_lightbox, $lightbox_type);
    echo '</select></p>';
    
    echo '<p><label for="' . $this->get_field_id('footer') . '">Lightbox Footer Text:</label>';
    echo '<textarea class="widefat" rows="3" cols="20" id="' . $this->get_field_id('footer') . '" name="' . $this->get_field_name('footer') . '">'. $footer . '</textarea></p>';
  }
 
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    
    $instance['title'] = $new_instance['title'];
    $instance['address'] = $new_instance['address'];
    $instance['thumb_pin_color'] = $new_instance['thumb_pin_color'];
    $instance['thumb_width'] = (int) $new_instance['thumb_width'];
    $instance['thumb_height'] = (int) $new_instance['thumb_height'];
    $instance['lightbox_width'] = (int) $new_instance['lightbox_width'];
    $instance['lightbox_height'] = (int) $new_instance['lightbox_height'];
    $instance['thumb_type'] = $new_instance['thumb_type'];
    $instance['lightbox_type'] = $new_instance['lightbox_type'];
    $instance['thumb_zoom'] = $new_instance['thumb_zoom'];
    $instance['lightbox_zoom'] = $new_instance['lightbox_zoom'];
    $instance['footer'] = $new_instance['footer'];
    
    return $instance;
  }
 
  function widget($args, $instance) {
    $out = $tmp = '';
    
    extract($args, EXTR_SKIP);
    self::$widgets[] = array('title' => $instance['title'],
                             'footer' => $instance['footer'],
                             'address' => $instance['address'],
                             'lightbox_zoom' => $instance['lightbox_zoom'],
                             'lightbox_type' => $instance['lightbox_type'],
                             'id' => $widget_id);
  
    $out .= $before_widget;
    
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
    if (!empty($title)) {
      $out .= $before_title . $title . $after_title;
    }
 
    $tmp .= '<p><a class="widget-map" href="#dialog-' . $widget_id . '" title="Click to open larger map">';
    $tmp .= '<img title="Click to open larger map" alt="Click to open larger map" src="https://maps.googleapis.com/maps/api/staticmap?center=' . 
         urlencode($instance['address']) . '&amp;zoom=' . $instance['thumb_zoom'] .
         '&amp;size=' .$instance['thumb_width'] . 'x' . $instance['thumb_height'] . '&amp;maptype=' . $instance['thumb_type'] . '&amp;sensor=false&amp;scale=2&amp;markers=color:' . $instance['thumb_pin_color'] . '%7Clabel:A%7C' .
         urlencode($instance['address']) . '"></a>';
    $tmp .= '</p>';
    $out .= apply_filters('google_maps_widget_content', $tmp);
    
    $out .= $after_widget;
    
    echo $out;
  }
} // class GoogleMapsWidget