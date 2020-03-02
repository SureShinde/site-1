
define([
    "jquery"
],function ($) {   

  return {
 
    deleteImage : function(selectId){
      $('#ox_delete_image_'+selectId).val(1);
      $('#ox_image_input_'+selectId).val('');
			$('#ox_image_saved_as_'+selectId).val('');            
      $('#ox_image_placeholder_'+selectId).hide();
      $('#ox_delete_image_button_'+selectId).hide();
      $('#ox_image_uploader_'+selectId).show();    
    }	    
	
  }; 
    
});


