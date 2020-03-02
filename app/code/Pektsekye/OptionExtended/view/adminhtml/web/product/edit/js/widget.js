
require([
    "jquery",    
    "Pektsekye_OptionExtended/product/edit/js/main",
    "Pektsekye_OptionExtended/product/edit/js/duplicate",
    "Pektsekye_OptionExtended/product/edit/js/dependency",
    "Pektsekye_OptionExtended/product/edit/js/parent",
    "Pektsekye_OptionExtended/product/edit/js/ids",
    "Pektsekye_OptionExtended/product/edit/js/dynamic",
    "Pektsekye_OptionExtended/product/edit/js/override",
    "Pektsekye_OptionExtended/product/edit/js/sd",
    "Pektsekye_OptionExtended/product/edit/js/uploader",
    "Pektsekye_OptionExtended/product/edit/js/optiontemplate",
    "Pektsekye_OptionExtended/product/edit/js/util",
    "jquery/ui"                                   
],function($, main, duplicate, dependency, parent, ids, dynamic, override, sd, uploader, optiontemplate, util) {
  "use strict";

  $.extend(main, duplicate, dependency, parent, ids, dynamic, override, sd, uploader, optiontemplate, util);
  
  $.widget("pektsekye.optionExtended", main); 
});  