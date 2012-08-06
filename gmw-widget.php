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
                                    'thumb_width' => 250,
                                    'thumb_height' => 250,
                                    'thumb_type' => 'roadmap',
                                    'lightbox_width' => 550,
                                    'lightbox_height' => 550,
                                    'lightbox_type' => 'roadmap',
                                    'footer' => '',
                                    'zoom_thumb' => '13',
                                    'zoom_big' => '14'));
    $title = $instance['title'];
    $footer = $instance['footer'];
    $address = $instance['address'];
    $thumb_width = $instance['thumb_width'];
    $thumb_height = $instance['thumb_height'];
    $thumb_type = $instance['thumb_type'];
    $lightbox_width = $instance['lightbox_width'];
    $lightbox_height = $instance['lightbox_height'];
    $lightbox_type = $instance['lightbox_type'];
    $zoom_thumb = $instance['zoom_thumb'];
    $zoom_big = $instance['zoom_big'];

    $map_types = array(array('val' => 'roadmap', 'label' => 'Road map'),
                       array('val' => 'satellite', 'label' => 'Satellite'),
                       array('val' => 'terrain', 'label' => 'Terrain'),
                       array('val' => 'hybrid', 'label' => 'Hybrid'));

    $zoom_levels = array(array('val' => '0', 'label' => '0 - entire world'));
    for ($tmp = 1; $tmp <= 20; $tmp++) {
      $zoom_levels[] = array('val' => $tmp, 'label' => $tmp);
    }
    $zoom_levels[] = array('val' => '21', 'label' => '21 - street view');

    echo '<p><label for="' . $this->get_field_id('title') . '">Title:</label><input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . esc_attr($title) . '" /></p>';
    
    echo '<p><label for="' . $this->get_field_id('address') . '">Address:</label><input class="widefat" id="' . $this->get_field_id('address') . '" name="' . $this->get_field_name('address') . '" type="text" value="' . esc_attr($address) . '" /></p>';
    
    echo '<p><label for="' . $this->get_field_id('thumb_width') . '">Thumbnail Size: </label>';
    echo '<input class="small-text" id="' . $this->get_field_id('thumb_width') . '" name="' . $this->get_field_name('thumb_width') . '" type="text" value="' . esc_attr($thumb_width) . '" /> x ';
    echo '<input class="small-text" id="' . $this->get_field_id('thumb_height') . '" name="' . $this->get_field_name('thumb_height') . '" type="text" value="' . esc_attr($thumb_height) . '" />';
    echo '</p>';

    echo '<p><label for="' . $this->get_field_id('lightbox_width') . '">Lightbox Size: </label>';
    echo '<input class="small-text" id="' . $this->get_field_id('lightbox_width') . '" name="' . $this->get_field_name('lightbox_width') . '" type="text" value="' . esc_attr($lightbox_width) . '" /> x ';
    echo '<input class="small-text" id="' . $this->get_field_id('lightbox_height') . '" name="' . $this->get_field_name('lightbox_height') . '" type="text" value="' . esc_attr($lightbox_height) . '" />';
    echo '</p>';
    
    echo '<p><label for="' . $this->get_field_id('zoom_thumb') . '">Zoom Level for Thumbnail Map: </label>';
    echo '<select id="' . $this->get_field_id('zoom_thumb') . '" name="' . $this->get_field_name('zoom_thumb') . '">';
    GMW::create_select_options($zoom_levels, $zoom_thumb);
    echo '</select></p>';
    
    echo '<p><label for="' . $this->get_field_id('zoom_big') . '">Zoom Level for Lightbox Map: </label>';
    echo '<select id="' . $this->get_field_id('zoom_big') . '" name="' . $this->get_field_name('zoom_big') . '">';
    GMW::create_select_options($zoom_levels, $zoom_big);
    echo '</select></p>';

    echo '<p><label for="' . $this->get_field_id('thumb_type') . '">Thumbnail Map Type: </label>';
    echo '<select id="' . $this->get_field_id('thumb_type') . '" name="' . $this->get_field_name('thumb_type') . '">';
    GMW::create_select_options($map_types, $thumb_type);
    echo '</select></p>';

    echo '<p><label for="' . $this->get_field_id('lightbox_type') . '">Lightbox Map Type: </label>';
    echo '<select id="' . $this->get_field_id('lightbox_type') . '" name="' . $this->get_field_name('lightbox_type') . '">';
    GMW::create_select_options($map_types, $lightbox_type);
    echo '</select></p>';
    
    echo '<p><label for="' . $this->get_field_id('footer') . '">Lightbox Footer Text:</label>';
    echo '<textarea class="widefat" rows="3" cols="20" id="' . $this->get_field_id('footer') . '" name="' . $this->get_field_name('footer') . '">'. $footer . '</textarea></p>';
  }
 
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    
    $instance['title'] = $new_instance['title'];
    $instance['address'] = $new_instance['address'];
    $instance['thumb_width'] = (int) $new_instance['thumb_width'];
    $instance['thumb_height'] = (int) $new_instance['thumb_height'];
    $instance['lightbox_width'] = (int) $new_instance['lightbox_width'];
    $instance['lightbox_height'] = (int) $new_instance['lightbox_height'];
    $instance['thumb_type'] = $new_instance['thumb_type'];
    $instance['lightbox_type'] = $new_instance['lightbox_type'];
    $instance['zoom_thumb'] = $new_instance['zoom_thumb'];
    $instance['zoom_big'] = $new_instance['zoom_big'];
    $instance['footer'] = $new_instance['footer'];
    
    return $instance;
  }
 
  function widget($args, $instance) {
    $out = $tmp = '';
    
    extract($args, EXTR_SKIP);
    self::$widgets[] = array('title' => $instance['title'],
                             'footer' => $instance['footer'],
                             'address' => $instance['address'],
                             'zoom_big' => $instance['zoom_big'],
                             'id' => $widget_id);
  
    $out .= $before_widget;
    
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
    if (!empty($title)) {
      $out .= $before_title . $title . $after_title;
    }
 
    $tmp .= '<p><a class="widget-map" href="#dialog-' . $widget_id . '" title="Click to open larger map">';
    $tmp .= '<img title="Click to open larger map" alt="Click to open larger map" src="https://maps.googleapis.com/maps/api/staticmap?center=' . 
         urlencode($instance['address']) . '&amp;zoom=' . $instance['zoom_thumb'] . 
         '&amp;size=' .$instance['thumb_width'] . 'x' . $instance['thumb_height'] . '&amp;maptype=' . $instance['thumb_type'] . '&amp;sensor=false&amp;scale=2&amp;markers=color:red%7Clabel:A%7C' .
         urlencode($instance['address']) . '"></a>';
    $tmp .= '</p>';
    $out .= apply_filters('google_maps_widget_content', $tmp);
    
    $out .= $after_widget;
    
    echo $out;
  }
} // class GoogleMapsWidget