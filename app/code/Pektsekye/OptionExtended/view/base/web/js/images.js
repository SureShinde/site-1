
define([
    'jquery',
    'mage/template',    
    'jquery/ui',
    'Pektsekye_OptionExtended/js/jquery.oxcolorbox-min',
    'Pektsekye_OptionExtended/js/jquery.tooltipster.min'
], function ($, mageTemplate) {  

  return {


    v : [], 
    o : [], 
    imageLayerIds : [], 
    templatePattern : /(^|.|\r|\n)({{(\w+)}})/, 



    initImages : function(){
        
      this.mainImage = $(this.mainImageSelector);   
      if (this.mainImage.length == 0){
        var imgBox = $('.product.photo.main');
        if (imgBox.length){
          var img = imgBox.find('img');
          if (img.length)
            this.mainImage = img;
        }      
      }   
      if (this.mainImage.length == 0)
        this.mainImage =  [{src:null}];
      
      this.mainImageSrc = this.mainImage[0].src;
      
      this.fotoramaGalleryDiv = $('[data-gallery-role="gallery"]');
      
      var thumbBorder = this.fotoramaGalleryDiv.find('.fotorama__thumb-border');
      this.fotoramaGalleryDiv.on('fotorama:show', function (e, fotorama, extra) {               
        if (extra.user){
          thumbBorder.show();
          if (fotorama.ocImageAdded){
            fotorama.pop();
            fotorama.ocImageAdded = false;
          }            
        } else {
          thumbBorder.hide();
        }  
      });       
 
 
      var optionsContainer = $('product-options-wrapper');
      if (optionsContainer){
        optionsContainer.onclick = function(){}; // to make checkbox label tag work on iPhone 
      }   
   
      this._on({
          "click .ox-picker-image": $.proxy(this.reloadSelect, this),
          "click .ox-act-as-label": $.proxy(this.actAsLabel, this)
      });       
           
    },  



    loadImages : function(element, optionId){
      if (!this.loadedOptionElement[optionId]){
        this.o[optionId] = {};
        this.isNewOption = true;        
        this.dd = this.oldO[optionId].dd;
        this.prepareOption(optionId, element);
      }
      if (element[0].type == 'radio' || element[0].type == 'checkbox') {  
        if (element.val()){
          var valueId = parseInt(element.val());  
          this.v[valueId] = {};     
        }
        this.prepareValue(optionId, element, element.val());  
      } else if ((element[0].type == 'select-one' && !element.hasClass('datetime-picker')) || element[0].type == 'select-multiple') { 
        var options = element[0].options;
        for (var i = 0, len = options.length; i < len; ++i){
          if (options[i].value){
            var valueId = parseInt(options[i].value);
            this.v[valueId] = {};     
          }
          this.prepareValue(optionId, element, options[i].value);       
        } 
      }
    },

  
  
  
    prepareOption : function(optionId, element){
    
      switch (this.config[0][optionId][1]){
        case 'above' : 
          if (element[0].type == 'radio' || element[0].type == 'select-one') {
            this.dd.addClass('ox-above');
            var control = this.dd.find('.control');
            control.before($('<div>', {'class': 'spacer'}).html('&nbsp;'));
            control.before(this.makeImage(optionId, null, 'one'));                  
            control.before($('<div>', {'id' : 'ox_description_' + optionId, 'style' : 'display:none;', 'class' : 'ox-descr'}));                           
            control.before($('<div>', {'class': 'spacer'}).html('&nbsp;'));            
          } else {
            this.dd.addClass('ox-above-checkbox');          
          }
        break;
        case 'before' :   
          if (element[0].type == 'select-one'){          
            this.dd.addClass('ox-before-select');
            element.wrap($('<div>', {'class': 'ox-table'})); 
            element.wrap($('<div>', {'class': 'ox-table-cell'}));             
            element.closest('.ox-table-cell').before(this.makeImage(optionId, null, null));
            $('#ox_image_' + optionId).wrap($('<div>', {'class': 'ox-table-cell-img'}));                   
            element.after($('<img>', {'src' : this.infoIcon, 'class' : 'ox-tooltip-icon', 'title' : '', id:'ox_description_'+ optionId, 'style' : 'display:none;'}));                            
          } else if(element[0].type == 'radio'){       
            this.dd.addClass('ox-before-radio');  
            this.dd.find('.options-list').wrap($('<div>', {'class': 'ox-table'}));
            this.dd.find('.options-list').wrap($('<div>', {'class': 'ox-table-cell'}));                         
            this.dd.find('.options-list').closest('.ox-table-cell').before(this.makeImage(optionId, null, null)); 
            $('#ox_image_' + optionId).wrap($('<div>', {'class': 'ox-table-cell-img'}));                      
          } 
        break;
        case 'below' :  
          if (element[0].type == 'radio' || element[0].type == 'select-one') {  
            this.dd.addClass('ox-below');                
            this.dd.append(this.makeImage(optionId, null, 'one'));                  
            this.dd.append($('<div>', {'id' : 'ox_description_' + optionId, 'style' : 'display:none;', 'class' : 'ox-descr'}));
            this.dd.find('img').before($('<div>', {'class': 'spacer'}).html('&nbsp;'));                      
            this.dd.append($('<div>', {'class': 'spacer'}).html('&nbsp;'));
          } else {
            this.dd.addClass('ox-below-checkbox');
            if (element[0].type == 'select-multiple')              
              element.after($('<div>', {'class': 'spacer'}).html('&nbsp;'));         
          }
        break;
        case 'swap' :     
          if (element[0].type == 'select-one'){          
            this.dd.addClass('ox-swap-select');     
            element.after($('<div>', {'id' : 'ox_description_' + optionId, 'style' : 'display:none;', 'class' : 'ox-descr'}));               
          } else if(element[0].type == 'radio'){       
            this.dd.addClass('ox-swap-radio');            
          } 
        break;
        case 'pickerswap' :       
        case 'picker' :         
          this.dd.addClass('ox-picker');      
          element.after($('<div>', {'id' : 'ox_description_' + optionId, 'style' : 'display:none;', 'class' : 'ox-descr'}));                        
        break;
        case 'grid' : 
        case 'gridcompact' :        
          this.dd.addClass(this.config[0][optionId][1] == 'grid' ? 'ox-grid' : 'ox-gridcompact');          
          var ul = this.dd.find('.options-list');
          ul.prepend($('<div>', {'class': 'spacer'}).html('&nbsp;'));          
          ul.append($('<div>', {'class': 'spacer'}).html('&nbsp;'));         
        break;
        case 'list' :         
          this.dd.addClass('ox-list');        
        break;  
      } 
    
      this.dd.append($('<div>', {'class': 'ox-note'}).html(this.config[0][optionId][0]));
    },
  
  
  
  
    prepareValue : function(optionId, element, value){
    
      var valueId = value ? parseInt(value) : null;
    
      if (value)
        var imageUrl = this.thumbnailDirUrl + this.config[1][valueId][0];
    
      switch (this.config[0][optionId][1]){
      
        case 'above' : 
      
          if (value){
            if (this.config[1][valueId][0]){
              if (element[0].type == 'radio' || element[0].type == 'select-one'){     
                this.v[valueId].thumbnail = new Image();
                this.v[valueId].thumbnail.src = imageUrl;
              } else {
                if (this.isNewOption){
                  this.dd.prepend(this.makeImage(optionId, valueId, null));                
                  this.isNewOption = false;               
                } else {  
                  previousImage.after(this.makeImage(optionId, valueId, null));                            
                }
                previousImage = $('#ox_v_image_' + valueId);
                if (element[0].type == 'select-multiple')
                  this.v[valueId].selected = false;                       
              }
            }
            if (!this.isEditOrderPage && this.config[1][valueId][1] && element[0].type == 'checkbox'){                        
              element.closest('.field').find('.label').append($('<img>', {'src' : this.infoIcon, 'class' : 'ox-tooltip-icon', 'title' : this.config[1][valueId][1]}));            
            }           
          } 
        
        break;
        case 'before' :   
      
          if (value){
            if (this.config[1][valueId][0]){      
              this.v[valueId].thumbnail = new Image();
              this.v[valueId].thumbnail.src = imageUrl;
            }
            if (!this.isEditOrderPage && this.config[1][valueId][1] && element[0].type == 'radio'){
              element.closest('.field').find('.label').append($('<img>', {'src' : this.infoIcon, 'class' : 'ox-tooltip-icon', 'title' : this.config[1][valueId][1]}));                                        
            } 
          } 
        
        break;
        case 'below' :  
      
          if (value){
            if (this.config[1][valueId][0]){
              if (element[0].type == 'radio' || element[0].type == 'select-one'){     
                this.v[valueId].thumbnail = new Image();
                this.v[valueId].thumbnail.src = imageUrl;
              } else {
                if (this.isNewOption){
                  this.dd.find('.ox-note').before(this.makeImage(optionId, valueId, null));             
                  this.isNewOption = false;               
                } else {  
                  previousImage.after(this.makeImage(optionId, valueId, null));                            
                }
                previousImage = $('#ox_v_image_' + valueId);
                if (element[0].type == 'select-multiple')
                  this.v[valueId].selected = false;                     
              }
            }
            if (!this.isEditOrderPage && this.config[1][valueId][1] && element[0].type == 'checkbox'){
              element.closest('.field').find('.label').append($('<img>', {'src' : this.infoIcon, 'class' : 'ox-tooltip-icon', 'title' : this.config[1][valueId][1]}));               
            }         
          } 
        
        break;
        case 'swap' : 
      
          if (value){
            if (this.config[1][valueId][0]){      
              this.v[valueId].thumbnail = new Image();
              this.v[valueId].thumbnail.src = imageUrl;
            }
            if (!this.isEditOrderPage && this.config[1][valueId][1] && element[0].type == 'radio'){
              element.closest('.field').find('.label').append($('<img>', {'src' : this.infoIcon, 'class' : 'ox-tooltip-icon', 'title' : this.config[1][valueId][1]}));                                    
            } 
          } 
        
        break;
        case 'pickerswap' :
      
          if (value && this.config[1][valueId][0]){ 
              this.v[valueId].thumbnail = new Image();
              this.v[valueId].thumbnail.src = imageUrl;
          }         
          
        case 'picker' : 
      
          if (value && this.config[1][valueId][0]) {
            if (this.isNewOption){
              var control = this.dd.find('.control');
              control.before($('<div>', {'class': 'spacer'}).html('&nbsp;'));
              control.before(this.makeImage(optionId, valueId, null));              
              this.isNewOption = false;               
            } else {  
              previousImage.after(this.makeImage(optionId, valueId, null));                            
            }
            previousImage = $('#ox_v_image_' + valueId);
          }   
        
        break;
        case 'grid' :            
          element.before(this.makeImage(optionId, valueId, null));
          if (!this.isEditOrderPage && value && this.config[1][valueId][1])
            element.after($('<img>', {'src' : this.infoIcon, 'class' : 'ox-tooltip-icon', 'title' : this.config[1][valueId][1]}));                 
                      
        break;
        case 'gridcompact' : 
          element.before(this.makeImage(optionId, valueId, null));     
          if (value){
            var img = $('#ox_v_image_' + valueId);
            img.after($('<img>', {'src' : this.checkIcon, 'class' : 'ox-check-icon'}));        
            if (this.config[1][valueId][1])
              img[0].title = this.config[1][valueId][1];          
          }          
        break;
        case 'list' :   
     
          var li = element.closest('.field');

          li.prepend(this.makeImage(optionId, valueId, null));   
          
          var content  = li.find('label').wrap("<span class='content'></span>");
        
          content.closest('span.content').append(element); 
        
          if (value){             
            var description = $('<div>', {'class':'ox-descr'}).html(this.config[1][valueId][1]);
            var price = content.find('span.price-notice');
            if (price.length)
              price.before(description); 
            else
              content.append(description);           
          } else {
            li.addClass('none');          
          } 
          
          li.append($('<div>', {'class':'spacer'}).html("&nbsp;")); 
        break;  
      } 
    },
  


    observeRadioImage : function(optionId, valueId){
      if (this.config[0][optionId][1] == 'above' || this.config[0][optionId][1] == 'below'){  
        this.reloadDescription(optionId, valueId);
      } 
      this.reloadImage(optionId, valueId, 'radio', null); 
      this.o[optionId].value = valueId;   
    },
  
    observeCheckboxImage : function(element, optionId, valueId){   
      this.reloadImage(optionId, valueId, 'checkbox', element[0].checked); 
    },
  
    observeSelectOneImage : function(element, optionId){
      var valueId = element.val();
      if (this.config[0][optionId][1] == 'pickerswap'){
        this.reloadPickerImage(optionId, valueId);    
        this.reloadImage(optionId, valueId, 'select-one', null);            
      } else if (this.config[0][optionId][1] == 'picker'){
        this.reloadPickerImage(optionId, valueId);
      } else {
        this.reloadImage(optionId, valueId, 'select-one', null);      
      }
    
      if (this.config[0][optionId][1] == 'before')
        this.reloadDescriptionIcon(optionId, valueId);        
      else
        this.reloadDescription(optionId, valueId);        
    
      this.o[optionId].value = valueId;       
    },
  
    observeSelectMultipleImage : function(element, optionId){
        var options = element[0].options;    
        var l = options.length;
        while (l--){     
          if (this.config[1][options[l].value][0] && this.v[options[l].value].selected !== options[l].selected){                  
            this.reloadImage(optionId, options[l].value, 'select-multiple', options[l].selected);           
            this.v[options[l].value].selected = options[l].selected;  
          } 
        } 
    },  
  
  
  
  
    reloadImage : function(optionId, valueId, type, checked){
      if (type == 'radio' || type == 'select-one') {
        if (valueId && this.config[1][valueId][0]){   
          this.showImage(optionId, valueId, type);
        } else {
          if (valueId && this.config[0][optionId][1] == 'before'){
            this.setPlaceholder(optionId);        
          } else {
            this.hideImage(optionId, valueId, type);
          }
        }
      } else {
        if (checked && valueId && this.config[1][valueId][0])   
          this.showImage(optionId, valueId, type);
        else
          this.hideImage(optionId, valueId, type);      
      }
      
      if (this.config[0][optionId][1] == 'gridcompact'){
        var vId,img;
        var l = this.valsByOption[optionId].length;
        while (l--){
          vId = this.valsByOption[optionId][l];
          img = $('#ox_v_image_'+vId);	
          if (this.oldV[vId].element[0].checked){
            img.addClass('ox-selected');
          } else {
            img.removeClass('ox-selected');
          } 
        }
      }      
    },
  
    showImage : function(optionId, valueId, type){
      if (this.config[0][optionId][1] != 'grid' && this.config[0][optionId][1] != 'gridcompact' && this.config[0][optionId][1] != 'list'){
        if (type == 'radio' || type == 'select-one') {
          if (this.config[0][optionId][1] == 'swap' || this.config[0][optionId][1] == 'pickerswap'){
         //   var mainImage = $('.product-image-photo');//, [data-role="zoom-image"]
         //   if (mainImage.length){ //no product image
         //     mainImage[0].src = this.v[valueId].image.src;
         //   } else {  
             // var smallImage = this.thumbnailDirUrl + this.config[1][valueId][0];
              this.mainMediumImage = this.imageDirUrl + this.config[1][valueId][4];//this.v[valueId].image.src;
              this.mainLargeImage = this.imageDirUrl + this.config[1][valueId][4];     
         //   }     
            if (this.imageLayerIds.indexOf(optionId) == -1)
              this.imageLayerIds.push(optionId);
          } else {
            var image = $('#ox_image_' + optionId);
            if (!this.isEditOrderPage && this.config[0][optionId][2]){      
              image[0].style.cursor = 'pointer';
              image[0].title = this.imageTitle;
              image.attr('ox-data-popup', this.imageDirUrl + this.config[1][valueId][4]);
            }
            image[0].src = this.v[valueId].thumbnail.src;
            image.show();          
          }     
        } else {
          $('#ox_v_image_' + valueId).show();
        }
      }
    },  
  
    hideImage : function(optionId, valueId, type){
      if (this.config[0][optionId][1] != 'grid' && this.config[0][optionId][1] != 'gridcompact' && this.config[0][optionId][1] != 'list'){
        if (type == 'radio' || type == 'select-one') {
          if (this.config[0][optionId][1] == 'swap' || this.config[0][optionId][1] == 'pickerswap'){  
            this.without(this.imageLayerIds, optionId);
            var lastOId = this.imageLayerIds[this.imageLayerIds.length-1];
            
            var mediumImage = null;
            var largeImage = null;
            if (lastOId){
              var vId = this.o[lastOId].value;
              mediumImage = this.v[vId].image.src;
              largeImage = this.imageDirUrl + this.config[1][vId][4];
            } 
             
            this.mainMediumImage = mediumImage;
            this.mainLargeImage = largeImage;
            
          } else if (this.config[0][optionId][1] == 'before'){
            var image = $('#ox_image_' + optionId);
            if (image.length){
              if (this.config[0][optionId][2] && image[0].style.cursor == 'pointer'){      
                image[0].style.cursor = null;
                image[0].title = '';
                image.removeAttr('ox-data-popup');
              }         
              image[0].src = this.spacer;  
            }          
          } else {
            var image = $('#ox_image_' + optionId);
            if (image.length){
              image[0].src = this.spacer;
              image.hide();
            }
          }             
        } else if(this.config[1][valueId][0]){
          $('#ox_v_image_' + valueId).hide();      
        }
      }
    },
  
    setPlaceholder : function(optionId){
        var image = $('#ox_image_' + optionId);    
        if (this.config[0][optionId][2] && image[0].style.cursor == 'pointer'){      
          image[0].style.cursor = null;
          image[0].title = '';
          image.removeAttr('ox-data-popup');
        } 
        image[0].src = this.placeholderUrl;    
        image.show();
    },
  
  
  
  
    reloadDescription : function(optionId, valueId){  
      if (valueId && this.config[1][valueId][1])
        this.showDescription(optionId, valueId);
      else
        this.hideDescription(optionId);
    },
  
    showDescription : function(optionId, valueId){  
      var description = $('#ox_description_' + optionId);
      description.html(this.config[1][valueId][1]);
      description.show();     
    },
  
    hideDescription : function(optionId){ 
      var description = $('#ox_description_' + optionId);
      if (description.length)    
        description.hide();   
    },
  
  
  
  
    reloadDescriptionIcon : function(optionId, valueId){  
      if (valueId && this.config[1][valueId][1])
        this.showDescriptionIcon(optionId, valueId);
      else
        this.hideDescriptionIcon(optionId);
    },


    showDescriptionIcon : function(optionId, valueId){
      if (!this.isEditOrderPage)
        $('#ox_description_' + optionId).tooltipster('content', this.config[1][valueId][1]).show();  
    },
  
  
    hideDescriptionIcon : function(optionId){
      $('#ox_description_' + optionId).hide();
    },



    reloadPickerImage : function(optionId, valueId){
      if (valueId && this.config[1][valueId][0])
        this.highlightPickerImage(valueId);
      if (this.o[optionId].value && this.o[optionId].value != valueId && this.config[1][this.o[optionId].value][0])
        this.unhighlightPickerImage(this.o[optionId].value);    
    },
  
    highlightPickerImage : function(valueId){
      $('#ox_v_image_' + valueId).addClass('ox-selected');
    },
  
    unhighlightPickerImage : function(valueId){
      if (this.config[1][valueId][0]) 
        $('#ox_v_image_' + valueId).removeClass('ox-selected');
    },
  
    showPickerImage : function(optionId, valueId){
      if ((this.config[0][optionId][1] == 'picker' || this.config[0][optionId][1] == 'pickerswap') && this.config[1][valueId][0])   
        $('#ox_v_image_' + valueId).show();
    },  
  
    hidePickerImage : function(optionId, valueId){
      if ((this.config[0][optionId][1] == 'picker' || this.config[0][optionId][1] == 'pickerswap') && this.config[1][valueId][0]){      
        $('#ox_v_image_' + valueId).hide();
      }
    },  
  
  
  
    reloadSelect : function(e){
      
      var select = $(e.target).closest('.ox-picker').find('select');
      var valueId = e.target.id.replace(/\D+/, '');
            
      for (var i=0; i < select[0].options.length; i++) {
         if (select[0].options[i].value == valueId) {
            select[0].options[i].selected = true;
            break;
         }
      } 
      select.trigger('change');
    },
  
  
    preloadSwapImages : function(optionIds, valsByOption){
      this.toload = 0;  
      this.loaded = 0;
      this.ss = '';
      var l = optionIds.length; 
      for (var i=0;i<l;i++){
        if (this.config[0][optionIds[i]][1] == 'swap' || this.config[0][optionIds[i]][1] == 'pickerswap'){
          var ll = valsByOption[optionIds[i]].length;
          while (ll--){
            if (this.config[1][valsByOption[optionIds[i]][ll]][0]){
              this.v[valsByOption[optionIds[i]][ll]].image = new Image();
              this.v[valsByOption[optionIds[i]][ll]].image.src = this.imageDirUrl + this.config[1][valsByOption[optionIds[i]][ll]][4];  
              var widget = this;
              this.v[valsByOption[optionIds[i]][ll]].image.onload = function(){
                widget.loaded++;
                if (widget.loaded == widget.toload)
                  widget.onDataReady();
              };
              this.toload++;
            }   
          } 
        }
      } 
      if (this.toload == 0)
        this.onDataReady(); 
    },

  
    preloadPopupImages : function(optionIds, valsByOption){
      var l = optionIds.length; 
      for (var i=0;i<l;i++){
        if (this.config[0][optionIds[i]][2]){   
          var ll = valsByOption[optionIds[i]].length;
          while (ll--){
            if (this.config[1][valsByOption[optionIds[i]][ll]][0]){
              this.v[valsByOption[optionIds[i]][ll]].image = new Image();
              this.v[valsByOption[optionIds[i]][ll]].image.src = this.imageDirUrl + this.config[1][valsByOption[optionIds[i]][ll]][4];  
            }   
          } 
        }
      }   
    },  
  
    resetImage : function(optionId, valueId, type){
      if (this.config[0][optionId][1] == 'pickerswap'){ 
        this.unhighlightPickerImage(valueId);  
        this.hideImage(optionId, valueId, type);  
        this.hideDescription(optionId);
      } else if (this.config[0][optionId][1] == 'picker'){
        this.unhighlightPickerImage(valueId);
        this.hideDescription(optionId);
      } else {
        this.hideImage(optionId, valueId, type);  
        if ((this.config[0][optionId][1] == 'above' || this.config[0][optionId][1] == 'below') && (type == 'select-one' || type == 'radio')){
          this.hideDescription(optionId); 
        } else if (this.config[0][optionId][1] == 'before' && type == 'select-one' ){       
          this.hideDescriptionIcon(optionId);     
        }
        if (type == 'select-multiple')
          this.v[valueId].selected = false;           
      } 
      if (this.config[0][optionId][1] == 'gridcompact'){
        $('#ox_v_image_' + valueId).removeClass('ox-selected');
      }	
    },  
  
  
    resetZoom : function(){
      if (this.mainLargeImage == this.lastMainLargeImage || this.mainMediumImage == this.lastMainMediumImage)
        return;

      var galleryPlaceholderDiv = $('.gallery-placeholder');
      if (galleryPlaceholderDiv.length){

        var fotoramaGalleryDiv = $('[data-gallery-role="gallery"]');
      
        if (fotoramaGalleryDiv.length == 0){
          if (!this.galleryPlaceholderObserved){
            galleryPlaceholderDiv.on('gallery:loaded', $.proxy(this.resetZoom, this));
            this.galleryPlaceholderObserved = 1;
          }
 
          return;
        }

        var fotoramaLoaded = fotoramaGalleryDiv.find('.fotorama__loaded').length > 0;
        if (!fotoramaLoaded){
          if (!this.fotoramaLoadObserved){
            this.onFotoramaLoad = $.proxy(this.resetZoom, this);
            fotoramaGalleryDiv.on('fotorama:load', this.onFotoramaLoad);
            this.fotoramaLoadObserved = 1;
          }

          return;
                  
        } else if (this.fotoramaLoadObserved){
          fotoramaGalleryDiv.off('fotorama:load', this.onFotoramaLoad);
          this.fotoramaLoadObserved = 0;
        }     

        if (!this.fotoramaControlsObserved){
          var thumbBorder = fotoramaGalleryDiv.find('.fotorama__thumb-border');
          fotoramaGalleryDiv.on('fotorama:show', $.proxy(function (e, fotorama, extra) {               
            if (extra.user){// if customer uses fotorama controls then remove extra thumbnail 
              if (fotorama.activeIndex != this.fotoramaLastIndex){ 
                thumbBorder.show();
                if (this.ocImageAdded){
                  fotorama.pop();
                  this.ocImageAdded = false;
                  this.lastMainMediumImage = null;
                  this.lastMainLargeImage = null;
                }
              }            
            } else {
              thumbBorder.hide();
            }  
          }, this));
          this.fotoramaControlsObserved = 1;      
        }
      
        var fotorama = fotoramaGalleryDiv.data("fotorama");
        if (!fotorama)
          return;

        if (this.mainMediumImage){
          if (this.mainMediumImage == this.mainLargeImage)
            this.mainLargeImage += '?v=1'; // to trigger img.onload code
          if (!this.ocImageAdded){  
            fotorama.push({img: this.mainMediumImage, full: this.mainLargeImage});
            this.ocImageAdded = true;
          } else {
            fotorama.splice(-1, 1, {img: this.mainMediumImage, full: this.mainLargeImage});           
          }
          fotorama.data[fotorama.size - 1].$navThumbFrame.hide();       
          fotorama.show('>>');   
          this.fotoramaLastIndex = fotorama.activeIndex;                           
        } else {
          fotorama.show(0);       
        }
        
        this.lastMainMediumImage = this.mainMediumImage;
        this.lastMainLargeImage = this.mainLargeImage;
      
      }   
    },
  
  
    makeImage : function(optionId, valueId, type){
      var element,className,onclick,src,popupSrc,style,title,id; 
      switch (this.config[0][optionId][1]){
        case 'above' :
        case 'below' :      
          id = type == 'one' ? 'ox_image_' + optionId : 'ox_v_image_' + valueId;                
          className  = 'ox-image';
          style = 'display:none;';        
          if (valueId && this.config[1][valueId][0]){
            src  = this.thumbnailDirUrl + this.config[1][valueId][0];
            if (!this.isEditOrderPage && this.config[0][optionId][2]){
              style += 'cursor:pointer;'; 
              title = this.imageTitle;
              popupSrc = this.imageDirUrl + this.config[1][valueId][4];                    
            }
          } else if (valueId){
            src = this.placeholderUrl;          
          } else {
            src = this.spacer;            
          }         
        break;        
        case 'grid' :
        case 'gridcompact' :        
        case 'list' :
          className = 'ox-image';
          if (valueId && this.config[1][valueId][0]){
            src  = this.thumbnailDirUrl + this.config[1][valueId][0];
            if (!this.isEditOrderPage && this.config[0][optionId][2]){
              style = 'cursor:pointer;';  
              title = this.imageTitle;
              popupSrc = this.imageDirUrl + this.config[1][valueId][4];                    
            }
          } else if (valueId){
            src = this.placeholderUrl;          
          } else {
            src = this.spacer;            
          }     
          if (!this.config[0][optionId][2] || this.config[0][optionId][1] != 'list')
            className +=' ox-act-as-label'; 
           
          if (valueId)   
            id = 'ox_v_image_' + valueId; 
                     
          if (valueId && this.config[0][optionId][1] == 'gridcompact'){           
            if (this.config[1][valueId][1])  
              className +=' ox-tooltip-icon';         
          }                   
        break;
        case 'before' :       
          id = 'ox_image_' + optionId;
          className  = 'ox-image';        
          src  = !valueId ? this.spacer : (!this.config[1][valueId][0] ? this.placeholderUrl : this.thumbnailDirUrl + this.config[1][valueId][0]);
        break;
        case 'pickerswap' :               
        case 'picker' :   
          id = 'ox_v_image_' + valueId;
          className = 'ox-picker-image ox-tooltip';          
          src = this.pickerImageDirUrl + this.config[1][valueId][0];        
          if (!this.isEditOrderPage && this.config[0][optionId][2]){
            title = "&lt;img class=&quot;ox-hover-image&quot; src=&quot;"+this.hoverImageDirUrl + this.config[1][valueId][4]+"&quot;/&gt;";
          }                
        break;
      }     
    
      element = '<img src="'+src+'" class="'+className+'"' + (id ? ' id="'+id+'"' : '') + (style ? ' style="'+style+'"' : '') + (title ? ' title="'+title+'"' : '') + (onclick ? ' onclick="'+onclick+'"' : '') + (popupSrc ? ' ox-data-popup="'+popupSrc+'"' : '') + '/>';             

      return element;
    },
  
  
    actAsLabel : function(e){

      var element = $(e.target).closest('.field').find('input');
      
      if (this.isEditOrderPage){
      
        element.trigger('click'); 
                       
      } else {
    
        if (element[0].type == 'radio'){
          element[0].checked |= true;       
        } else {
          element[0].checked = !element[0].checked;             
        }  
            
        element.trigger('change');
      }  
    },


    without : function(a, v){
      var i = a.indexOf(v);
      if (i != -1)
        a.splice(i, 1);
    } 


  };
    
});
