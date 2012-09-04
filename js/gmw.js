/*
 * Google Maps Widget
 * (c) Web factory Ltd, 2012
 */

jQuery(function($) {
    $('.google-maps-widget a.widget-map').click(function() {
      dialog = $($(this).attr('href'));
      map_width = dialog.attr('data-map-width');
      map_height = dialog.attr('data-map-height');
      map_url = dialog.attr('data-iframe-url');
      map_title = dialog.attr('title');
      
      var content = $(dialog.html());
      content.filter('.gmw-map').html('<iframe width="' + map_width + 'px" height="' + map_height + 'px" src="' + map_url + '"></iframe>');

      $.fancybox( {'type': 'html', 'content': content, 'title': map_title, 'autoSize': true, 'minWidth': map_width, 'minHeight': map_height } );
      
      return false;
    });
}); // onload