/*
 * Google Maps Widget
 * (c) Web factory Ltd, 2012
 */

jQuery(function($) {
  $('.gmw-tabs').tabs();
  
  $('div[id*="googlemapswidget"]').ajaxSuccess(function(event, request, option) {
    $('.gmw-tabs').tabs();
  });
}); // onload