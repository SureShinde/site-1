
define([
    "jquery",    
    "Pektsekye_OptionExtended/js/main",
    "Pektsekye_OptionExtended/js/dependent",
    "Pektsekye_OptionExtended/js/images",
    "jquery/ui"                                   
],function($, main, dependent, images) {
  "use strict";

  $.extend(main, dependent, images);
  
  $.widget("pektsekye.optionExtended", main); 
});  