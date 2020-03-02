var optionExtended = {};

require([
    "jquery",
    "jquery/ui"
], function ($) {

  $.extend(optionExtended, {


    onTypeChange : function() {
      var type = $('#type').val();
      switch(type){
          case 'field':
          case 'area':
              $('#price').closest('div.field').show();
              $('#price_type').closest('div.field').show();
              $('#sku').closest('div.field').show();
              $('#max_characters').closest('div.field').show();          
              $('#file_extension').val('');            
              $('#file_extension').closest('div.field').hide();
              $('#image_size_x').val('');            
              $('#image_size_x').closest('div.field').hide();
              $('#image_size_y').val('');            
              $('#image_size_y').closest('div.field').hide();
              $('#layout')[0].selectedIndex = 0;            
              $('#layout').closest('div.field').hide();
              $('#popup')[0].checked = false;            
              $('#popup').closest('div.field').hide();
              this.hideSd();               
              break;
          case 'file':
              $('#price').closest('div.field').show();
              $('#price_type').closest('div.field').show();
              $('#sku').closest('div.field').show();          
              $('#max_characters').val('');                     
              $('#max_characters').closest('div.field').hide();
              $('#file_extension').closest('div.field').show();
              $('#image_size_x').closest('div.field').show();
              $('#image_size_y').closest('div.field').show();              
              $('#layout')[0].selectedIndex = 0;            
              $('#layout').closest('div.field').hide();
              $('#popup')[0].checked = false;            
              $('#popup').closest('div.field').hide();
              this.hideSd();                 
              break;
          case 'drop_down':
          case 'radio':        
          case 'checkbox':
          case 'multiple':
              $('#price').val('');        
              $('#price').closest('div.field').hide();
              $('#price_type')[0].selectedIndex = 0;            
              $('#price_type').closest('div.field').hide();
              $('#sku').val('');               
              $('#sku').closest('div.field').hide();
              $('#max_characters').val('');                     
              $('#max_characters').closest('div.field').hide();
              $('#file_extension').val('');            
              $('#file_extension').closest('div.field').hide();
              $('#image_size_x').val('');            
              $('#image_size_x').closest('div.field').hide();
              $('#image_size_y').val('');            
              $('#image_size_y').closest('div.field').hide();
                                    
              var layout = $('#layout');
              this.reloadLayoutSelect(layout, type);           
              layout.closest('div.field').show();
                                        
              this.changePopup(layout.val());            
              $('#popup').closest('div.field').show();
              
              this.switchSd(type);                                                                                                                                                                 
              break;
          case 'date':
          case 'date_time':
          case 'time':        
              $('#price').closest('div.field').show();            
              $('#price_type').closest('div.field').show();        
              $('#sku').closest('div.field').show(); 
              $('#max_characters').val('');                     
              $('#max_characters').closest('div.field').hide();
              $('#file_extension').val('');            
              $('#file_extension').closest('div.field').hide();
              $('#image_size_x').val('');            
              $('#image_size_x').closest('div.field').hide();
              $('#image_size_y').val('');            
              $('#image_size_y').closest('div.field').hide();
              $('#layout')[0].selectedIndex = 0;            
              $('#layout').closest('div.field').hide();
              $('#popup')[0].checked = false;            
              $('#popup').closest('div.field').hide();
              this.hideSd();             
              break;              
          default:
              $('#price').val('');        
              $('#price').closest('div.field').hide();
              $('#price_type')[0].selectedIndex = 0;            
              $('#price_type').closest('div.field').hide();
              $('#sku').val('');               
              $('#sku').closest('div.field').hide();
              $('#max_characters').val('');                     
              $('#max_characters').closest('div.field').hide();
              $('#file_extension').val('');            
              $('#file_extension').closest('div.field').hide();
              $('#image_size_x').val('');            
              $('#image_size_x').closest('div.field').hide();
              $('#image_size_y').val('');            
              $('#image_size_y').closest('div.field').hide();
              $('#layout')[0].selectedIndex = 0;            
              $('#layout').closest('div.field').hide();
              $('#popup')[0].checked = false;            
              $('#popup').closest('div.field').hide();
              this.hideSd();                           
      }

    },


    changePopup : function(layout) {
      var popupCheckbox = $('#popup')[0];
      if (layout == 'swap'){
        popupCheckbox.checked = false;
        popupCheckbox.disabled = true;
      } else if(popupCheckbox.disabled){
        popupCheckbox.disabled = false;			
      }	
    },    

    
    reloadLayoutSelect : function(layoutSelect, type){
      layoutSelect = layoutSelect[0];
    
      var layout = layoutSelect.value;  
      layoutSelect.options.length = 1;
      switch (type){
        case "radio":
          layoutSelect.options[1] = new Option(this.beforeOption, "before");								
          layoutSelect.options[2] = new Option(this.belowOption, "below");						
          layoutSelect.options[3] = new Option(this.mainImage, "swap");					
          layoutSelect.options[4] = new Option(this.grid, "grid");
          layoutSelect.options[5] = new Option(this.gridcompact, "gridcompact");          
          layoutSelect.options[6] = new Option(this.list, "list");
          switch (layout){     
            case 'before'      :layoutSelect.selectedIndex = 1;break;
            case 'below'       :layoutSelect.selectedIndex = 2;break;
            case 'swap'        :layoutSelect.selectedIndex = 3;break;
            case 'grid'        :layoutSelect.selectedIndex = 4;break;    
            case 'gridcompact' :layoutSelect.selectedIndex = 5;break;             
            case 'list'        :layoutSelect.selectedIndex = 6  
          }			  			  				
          break;							
        case "checkbox":							
          layoutSelect.options[1] = new Option(this.belowOption, "below");					
          layoutSelect.options[2] = new Option(this.grid, "grid");
          layoutSelect.options[3] = new Option(this.gridcompact, "gridcompact");           
          layoutSelect.options[4] = new Option(this.list, "list");
          switch (layout){       
            case 'below'       :layoutSelect.selectedIndex = 1;break;
            case 'grid'        :layoutSelect.selectedIndex = 2;break; 
            case 'gridcompact' :layoutSelect.selectedIndex = 3;break;                
            case 'list'        :layoutSelect.selectedIndex = 4  
          }				  				  		  							
          break;				
        case "drop_down":		
          layoutSelect.options[1] = new Option(this.beforeOption, "before");								
          layoutSelect.options[2] = new Option(this.belowOption, "below");					
          layoutSelect.options[3] = new Option(this.mainImage, "swap");					
          layoutSelect.options[4] = new Option(this.colorPicker, "picker");		
          layoutSelect.options[5] = new Option(this.colorPickerSwap, "pickerswap");	
          switch (layout){       
            case 'before'     :layoutSelect.selectedIndex = 1;break;
            case 'below'      :layoutSelect.selectedIndex = 2;break;
            case 'swap'       :layoutSelect.selectedIndex = 3;break;
            case 'picker'     :layoutSelect.selectedIndex = 4;break;    
            case 'pickerswap' :layoutSelect.selectedIndex = 5  
          }				  			  		  			  			
          break;							
        case "multiple":									
          layoutSelect.options[1] = new Option(this.belowOption, "below");
          if (layout == 'below') 
            layoutSelect.selectedIndex = 1;					  				  			
      }		  	      
    },

    hideSd : function() {

        var sd = $('#sd');
        var sdM = $('#sd_multiple');
              
        sd[0].selectedIndex = 0;
        var l = sdM[0].options.length;
        while (l--)
          sdM[0].options[l].selected = false;
        
        sd.closest('div.field').hide();          
        sdM.closest('div.field').hide();              

    },
  
    switchSd : function(type) {
      var sd = $('#sd');
      var sdM = $('#sd_multiple');       
      if (type == 'radio' || type == 'drop_down'){
        var l = sdM[0].options.length;
        while (l--)
          sdM[0].options[l].selected = false;        	
        sdM.closest('div.field').hide(); 
        sd.closest('div.field').show(); 			    				  			  			
      } else {    		
        sd[0].selectedIndex = 0;        	      														
        sd.closest('div.field').hide();            
        sdM.closest('div.field').show();    			  				  			
      }	
    }
    	 
  });


  $(document).ready(function() {
    optionExtended.onTypeChange();
  });


});


