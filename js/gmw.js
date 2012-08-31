/*
 * Google Maps Widget
 * (c) Web factory Ltd, 2012
 */

jQuery(function($) {
    $('.google-maps-widget a.widget-map').click(function() {
      dialog = $($(this).attr('href'));
      map_width = dialog.attr('data-map-width');
      map_height = dialog.attr('data-map-height');
      
      dialog.dialog('option', {'width': map_width, 'minHeight': map_height}).dialog('open');
      
      return false;
    });

    $('.gmw-dialog').dialog({ 'dialogClass': 'wp-dialog',
                              'modal': true,
                              'resizable': false,
                              'zIndex': 9999,
                              'hide': 'fade',
                              'open': function(event, ui) { renderMap(event, ui); fixDialogClose(event, ui); },
                              'show': 'fade',
                              'autoOpen': false,
                              'closeOnEscape': true
                             });
}); // onload

function renderMap(event, ui) {
  dialog_id = '#' + event.target.id;
  map_url = jQuery(dialog_id).attr('data-iframe-url');
  map_width = jQuery(dialog_id).attr('data-map-width');
  map_height = jQuery(dialog_id).attr('data-map-height');
  
  jQuery('.gmw-map', dialog_id).html('<iframe width="' + map_width + '" height="' + map_height + '" src="' + map_url + '"></iframe>');
} // renderMap

function fixDialogClose(event, ui) {
  jQuery('.ui-widget-overlay').on('click', function(){ jQuery('.gmw-dialog').dialog('close'); });
} // fixDialogClose