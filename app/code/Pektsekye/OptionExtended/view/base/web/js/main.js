
var optionExtended;

define([
    "jquery",
    "jquery/ui"
],function($) {
  "use strict";
  
  return {        
 
              
    dependecyIsSet : false, 

  
    _create : function(){
  
      $.extend(this, this.options);  
      
      this.initImages();
     
      this.load('firstLoad');
    
      this.setDependency();
      this.dependecyIsSet = true;  
        
      if (!this.isEditOrderPage){
        $(".ox-image").click(function() {
          var src = $(this).attr("ox-data-popup");
          if (src){  
            $.oxcolorbox({
              maxWidth  : '95%', 
              maxHeight : '95%',
              photo     : true, 
              href      : src
            });
          } 
        }); 

        $('.ox-tooltip').tooltipster({contentAsHTML:true, theme: 'tooltipster-shadow'});      
        $('.ox-tooltip-icon').tooltipster({contentAsHTML:true, interactive:true, theme: 'tooltipster-shadow', maxWidth: 250});
      }      
     
      this.preloadSwapImages(this.oIds, this.valsByOption);

    },
  
  
    load : function(process){
    
      this.loadedOptionElement = [];
      
      var widget = this;
      $(this.getOptionSelector()).each(function(key, elements) {
        var element = $(elements);
        var optionIdStartIndex, optionIdEndIndex;
        if (element.is(":file")) {
            optionIdStartIndex = element.attr('name').indexOf('_') + 1;
            optionIdEndIndex = element.attr('name').lastIndexOf('_');
        } else {
            optionIdStartIndex = element.attr('name').indexOf('[') + 1;
            optionIdEndIndex = element.attr('name').indexOf(']');
        }
        var optionId = parseInt(element.attr('name').substring(optionIdStartIndex, optionIdEndIndex), 10);

        widget.saveElements(element, optionId);    
        widget.observeElements(element, optionId);
        
        if (process == 'firstLoad' || process == 'htmlReloading'){//add images when the page loads the first time or when the OptionExtended block HTML is reloaded with AJAX on edit order page in backend
          widget.loadImages(element, optionId);         
          widget.loadDependency(element, optionId);
        }
        
        widget.loadedOptionElement[optionId] = 1;
      });    
    },


    reloadElements : function(config){

      if ($('#ox_html_loaded').val() == 0){ // HTML of the edit order popup is reloaded   
        this.oIds = [];
        this.oldV = [];  
        this.oldO = [];
        this.childrenVals = [];
        this.indByValue = [];
        this.valsByOption = [];
        this.optionByValue = [];
        this.univValsByOption = [];
        this.childOIdsByO = [];
        this.previousIds = [];
        this.childrenByOption = [];
        
        this.config = config; 
 
        this.load('htmlReloading');
        
        this.setDependency();       
        this.selectDefault();		
      } else { 
        this.load('observedElementsReloading');      
      }
    },  


    saveElements : function(element, optionId){

      if (!this.loadedOptionElement[optionId]){//save just first input of radio, checkbox and date options.
        if (!this.oldO[optionId])
          this.oldO[optionId] = {}; 
        this.oldO[optionId].dd = this.getDdElement(element);
      }
      
      if (element[0].type == 'radio' || element[0].type == 'checkbox') {            
        if (!this.loadedOptionElement[optionId])
          this.oldO[optionId].firstelement = element;         
        
        var value = element.val();
        if (value){
          var valueId = parseInt(value);
          if (!this.oldV[valueId])
            this.oldV[valueId] = {};          
          this.oldV[valueId].element = element;
        }           
      } else {
        if (!this.loadedOptionElement[optionId])    
          this.oldO[optionId].element = element;      
      }
      
      
    },
    
    
    observeElements : function(element, optionId){
      if (element[0].type == 'radio') {      
          element.change($.proxy(this.observeRadio, this, optionId, element.val()));                           
      } else if(element[0].type == 'checkbox') {
          element.change($.proxy(this.observeCheckbox, this, element, optionId, element.val()));                     
      } else if(element[0].type == 'select-one' && !element.hasClass('datetime-picker')) {
          element.change($.proxy(this.observeSelectOne, this, element, optionId));         
      } else if(element[0].type == 'select-multiple') {  
          element.change($.proxy(this.observeSelectMultiple, this, element, optionId));         
      }      
    },
      
  
    onDataReady : function(){
    
      var media = $('[data-role="media-galleryyyyy"]');
      if (media.length){
        var widget = this;
        this._on(media, {
            imageupdated: function () {
              if (!widget.defaultSelected){
                widget.defaultSelected = 1;
                widget.selectDefault();
                widget.defaultSelected = 2;
              }
            }
        });               
      } else {
        this.selectDefault();
      }
      this.preloadPopupImages(this.oIds, this.valsByOption);    
    },
      
      
    selectDefault : function(fromOptionId){
      var i,oId,id,element,group,checkedIds,vId,ll;
      var prevOIds = {};
      var l = this.oIds.length; 
      for (i=0;i<l;i++){
      
        oId = this.oIds[i];
        
        prevOIds[oId] = 1;
        
        if (fromOptionId){
          if (oId == fromOptionId)
            fromOptionId = null;
          continue;
        }     
                
        if (this.childOIdsByO[oId]){
          var wrongOrder = false;
          ll = this.childOIdsByO[oId].length;    
          while (ll--){
            id = this.childOIdsByO[oId][ll];
            if (prevOIds[id]){
              wrongOrder = true;// child option is placed before parent
              break;
            }
          }
          if (wrongOrder){
            continue;
          }         
        }
        
        if (this.oldO[oId].visible){

          if (this.oldO[oId].element){
            element = this.oldO[oId].element;
            group = 'select';         
          } else {
            group = '';       
          }
        
          checkedIds = this.config[0][oId][3];
          ll = this.valsByOption[oId].length;    
          while (ll--){
            vId = this.valsByOption[oId][ll];
            if (this.oldV[vId].visible && checkedIds.indexOf(vId) != -1){
              if (group == 'select'){ 
                if (element[0].type == 'select-one')
                  element[0].selectedIndex = this.indByValue[vId];   
                else
                  element[0].options[this.indByValue[vId]].selected = true;                       
              } else {
                element = this.oldV[vId].element;
                element[0].checked = true;
                element.trigger('change', ["selectingDefault"]);                                 
              }
            }   
          } 
        
          if (group == 'select')
            element.trigger('change', ["selectingDefault"]); 
        }
      } 
      
      this.resetZoom();
        
    },
    
    
    getOptionSelector : function(){
      return this.isEditOrderPage ? '#product_composite_configure_form_fields .product-custom-option' : '.product-custom-option';
    },  


    getDdElement : function(element){
      if (this.isEditOrderPage){
        return element[0].type == 'radio' || element[0].type == 'checkbox' ? element.parents('.field').eq(1) : element.closest('.field');
      } else {
        return element[0].type == 'radio' || element[0].type == 'checkbox' ? element.closest('.options-list').closest('.field') : element.closest('.field');
      }    
    }    
    
              
  };


});
















