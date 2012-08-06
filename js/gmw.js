/*
 * Google Maps Widget
 * (c) Web factory Ltd, 2012
 */

jQuery(function($) {
    $('.google-maps-widget a.widget-map').click(function() {
      $($(this).attr('href')).dialog('option', {} ).dialog('open');
      return false;
    });

    $('.gmw-dialog').dialog({ 'dialogClass': 'wp-dialog',
                              'modal': true,
                              'resizable': false,
                              'zIndex': 9999,
                              'minWidth': 650,
                              'minHeight': 500,
                              'hide': { effect: 'drop', direction: "down" },
                              'open': function(event, ui) { renderMap(event, ui); fixDialogClose(event, ui); },
                              'close': function(event, ui) { $('#wrap').show(); },
                              'show': 'fade',
                              'autoOpen': false,
                              'closeOnEscape': true
                              });
}); // onload

function renderMap(event, ui) {
  dialog_id = '#' + event.target.id;
  map_url = jQuery(dialog_id).attr('data-iframe-url');
  jQuery('.gmw-map', dialog_id).html('<iframe width="650" height="500" src="' + map_url + '"></iframe>');
} // renderMap

function fixDialogClose(event, ui) {
  jQuery('.ui-widget-overlay').bind('click', function(){ jQuery('#' + event.target.id).dialog('close'); });
} // fixDialogClose