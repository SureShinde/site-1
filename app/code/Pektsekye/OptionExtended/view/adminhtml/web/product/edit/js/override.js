
define([
    "jquery",
    "mage/template"
],function($, mageTemplate) { 

  return {

    options: {
        selectionItemCount: {}
    },

    _initCustomOptions: function () {
      //  this.baseTmpl = mageTemplate('#custom-option-base-template');
        this.rowTmpl = mageTemplate('#custom-option-select-type-row-template');

        this._initOptionBoxes();
        this._initSortableSelections();
        this._bindCheckboxHandlers();
        this._bindReadOnlyMode();
        this._addValidation();
    },

    _addValidation: function () {
        $.validator.addMethod(
            'required-option-select', function (value) {
                return (value !== '');
            }, $.mage.__('Select type of option.'));

        $.validator.addMethod(
            'required-option-select-type-rows', function (value, element) {
                var optionContainerElm = element.up('div[id*=_type_]'),
                    selectTypesFlag = false,
                    selectTypeElements = $('#' + optionContainerElm.id + ' .select-type-title');

                selectTypeElements.each(function () {
                    if (!$(this).closest('tr').hasClass('ignore-validate')) {
                        selectTypesFlag = true;
                    }
                });

                return selectTypesFlag;
            }, $.mage.__('Please add rows to option.'));
    },

    _initOptionBoxes: function () {
    /*
        if (!this.isReadonly) {
            this.element.sortable({
                axis: 'y',
                handle: '[data-role=draggable-handle]',
                items: '#product_options_container_top > div',
                update: this._updateOptionBoxPositions,
                tolerance: 'pointer'
            });
        }
        */
        var syncOptionTitle = function (event) {
            var currentValue = $(event.target).val(),
                optionBoxTitle = $('.ox-option-title > span', $(event.target).closest('.fieldset-wrapper')),
                newOptionTitle = $.mage.__('New Option');

            optionBoxTitle.text(currentValue === '' ? newOptionTitle : currentValue);
        };
        
        this._on({
            /**
             * Reset field value to Default
             */
            'click .use-default-label': function (event) {
                $(event.target).closest('label').find('input').prop('checked', true).trigger('change');
            },

            /**
             * Remove custom option or option row for 'select' type of custom option
             */
            'click button[id^=product_option_][id$=_delete]': function (event) {
                var element = $(event.target).closest('#product_options_container_top > div.fieldset-wrapper,tr');

                if (element.length) {
                    var elId = element.attr('id');
                    if (element.hasClass('fieldset-wrapper')){ // delete option
                      var optionId = elId.replace('option_', '')
                      if ($('#'+optionId+'-content fieldset').length == 0){
                        this.addSectionContent({id:optionId, option_id:optionId});
                      }
                      this.deleteOption(parseInt(optionId));  	               
                    } else { // delete option value
                      var re = /(\d+)[\D]+(\d+)/;
                      var res = elId.match(re);
                      var optionId = parseInt(res[1]);
                      var valueId = parseInt(res[2]);
                      var rowId = this.rowIdBySelectId[valueId];
                      this.deleteRow(optionId, rowId);
                    } 
                                   
                    $('#product_' + element.attr('id').replace('product_', '') + '_is_delete').val(1);
                    element.find('input,select').addClass('ignore-validate');
                    element.addClass('ignore-validate').hide();
                    this.refreshSortableElements();                   
                }
            },
            /**
             * Minimize custom option block
             */
            'click #product_options_container_top [data-target$=-content]': function () {
                if (this.isReadonly) {
                    return false;
                }
            },

            /**
             * Add new custom option
             */
            'click #add_new_defined_option': function (event) {
                this._addOption(event);
            },

            /**
             * Add new option row for 'select' type of custom option
             */
            'click button[id^=product_option_][id$=_add_select_row]': function (event) {
                this.addSelection(event);
            },

            /**
             * Import custom options from products
             */
            'click #import_new_defined_option': function () {
                var importContainer = $('#import-container'),
                    widget = this;

                importContainer.modal({
                    title: $.mage.__('Select Product'),
                    type: 'slide',
                    transitionEvent: null,
                    opened: function () {
                                       
                        var disableMassaction = function(){
              
                          importContainer.find('#productGrid_massaction').hide();
            
                          var input = importContainer.find('input[name=product]');
                          input.change(function () {
                            input.prop('checked',false);
                            $(this).prop('checked',true);                  
                          });  
                        }
  
                        disableMassaction();
              
                        importContainer.find('#productGrid').bind('contentUpdated', function () {
                          disableMassaction();                                
                        });                     
                    
                    
                        $(this).closest('.ui-dialog').addClass('ui-dialog-active');

                        var topMargin = $(this).closest('.ui-dialog').children('.ui-dialog-titlebar').outerHeight() + 135;
                        $(this).closest('.ui-dialog').css('margin-top', topMargin);

                        $(this).addClass('admin__scope-old'); // ToDo UI: remove with old styles removal
                    },
                    close: function () {
                        $(this).closest('.ui-dialog').removeClass('ui-dialog-active');
                    },
                    buttons: [
                        {
                            text: $.mage.__('Import'),
                            id: 'import-custom-options-apply-button',
                            'class': 'action-primary action-import',
                            click: function (event, massActionTrigger) {
                                var request = [];
                                
                                $(this.element).find('input[name=product]:checked').map(function () {
                                    request.push(this.value);
                                });                                

                                if (request.length === 0) {
                                    if (!massActionTrigger) {
                                        alert($.mage.__('Please select items.'));
                                    }

                                    return;
                                }

                                widget.importOptions(request[0]); 
                                
                                
                                /*
                                $.post(widget.customOptionsUrl, {
                                    'products[]': request,
                                    form_key: widget.formKey
                                }, function ($data) {
                                    $.parseJSON($data).each(function (el) {
                                        el.id = widget.getFreeOptionId(el.id);
                                        el.option_id = el.id;

                                        if (typeof el.optionValues !== 'undefined') {
                                            for (var i = 0; i < el.optionValues.length; i++) {
                                                el.optionValues[i].option_id = el.id;
                                            }
                                        }
                                        //Adding option
                                        widget._addOption(el);
                                        //Will save new option on server side
                                        $('#product_option_' + el.id + '_option_id').val(0);
                                        $('#option_' + el.id + ' input[name$="option_type_id]"]').val(-1);
                                    });
                                    importContainer.dialog('close');
                                });
                                
                                */
                                
                            }
                        }]
                });
                
                importContainer.modal('option', 'transitionEvent', null);
                
                importContainer.load(
                    this.options.productGridUrl,
                    {form_key: this.options.formKey, current_product_id : this.options.currentProductId},
                    function () {
                        importContainer.modal('openModal');
                    }
                );
            },

            'click #productGrid_massaction-form button': function () {
                $('#import-custom-options-apply-button').trigger('click', 'massActionTrigger');
            },

            /**
             * Change custom option type
             */
            'change select[id^=product_option_][id$=_type]': function (event, data) {  
                data = data || {};
                var widget = this,
                    currentElement = $(event.target),
                    parentId = '#' + currentElement.closest('.fieldset-alt').attr('id'),
                    group = currentElement.find('[value="' + currentElement.val() + '"]').closest('optgroup').attr('data-optgroup-name'),
                    previousGroup = $(parentId + '_previous_group').val(),
                    previousBlock = $(parentId + '_type_' + previousGroup),
                    tmpl;

                if (typeof group !== 'undefined') {
                    group = group.toLowerCase();
                }

                if (previousGroup !== group) {
                    if (previousBlock.length) {
                        previousBlock.addClass('ignore-validate').hide();
                        var optionId = currentElement.closest('.fieldset-alt').attr('id').replace('product_option_','');
                        this.unsetOptionType(parseInt(optionId), previousGroup);
                    }
                    $(parentId + '_previous_group').val(group);

                    if (typeof group === 'undefined') {
                        return;
                    }
                    var disabledBlock = $(parentId).find(parentId + '_type_' + group);

                    if (disabledBlock.length) {
                        disabledBlock.removeClass('ignore-validate').show();
                    } else {
                        if ($.isEmptyObject(data)) {
                            data.option_id = $(parentId + '_id').val();
                            data.price = data.sku = '';
                        }
                        data.group = group;

                        tmpl = widget.element.find('#custom-option-' + group + '-type-template').html();
                        tmpl = mageTemplate(tmpl, {
                            data: data
                        });

                        $(tmpl).insertAfter($(parentId));

                        if (data.price_type) {
                            var priceType = $('#' + widget.fieldId + '_' + data.option_id + '_price_type');
                            priceType.val(data.price_type).attr('data-store-label', data.price_type);
                        }
          //              this._bindUseDefault(widget.fieldId + '_' + data.option_id, data);
                        //Add selections
                        if (data.optionValues) {
                            data.optionValues.each(function (value) {
                                widget.addSelection(value);
                            });
                        }
                    }
                }
                
                this.onTypeChange(event, data);
                
            },
            //Sync title
            'change .field-option-title > .control > input[id$="_title"]': syncOptionTitle,
            'keyup .field-option-title > .control > input[id$="_title"]': syncOptionTitle,
            'paste .field-option-title > .control > input[id$="_title"]': syncOptionTitle
        });
    },

    _initSortableSelections: function () {
        if (!this.isReadonly) {
            this.element.find('[id^=product_option_][id$=_type_select] tbody').sortable({
                axis: 'y',
                handle: '[data-role=draggable-handle]',
                helper: function (event, ui) {
                    ui.children().each(function () {
                        $(this).width($(this).width());
                    });

                    return ui;
                },
                update: this._updateSelectionsPositions,
                tolerance: 'pointer'
            });
        }
    },

    /**
     * Sync sort order checkbox with hidden dropdown
     */
    _bindCheckboxHandlers: function () {
        this._on({
            'change [id^=product_option_][id$=_required]': function (event) {
                var $this = $(event.target);
                $this.closest('#product_options_container_top > div').find('[name$="[is_require]"]').val($this.is(':checked') ? 1 : 0);
            }
        });
        this.element.find('[id^=product_option_][id$=_required]').each(function () {
            $(this).prop('checked', $(this).closest('#product_options_container_top > div').find('[name$="[is_require]"]').val() > 0);
        });
    },

    /**
     * Update Custom option position
     */
    _updateOptionBoxPositions: function () {
        //'div[id^=option_]:not(.ignore-validate) .fieldset-alt > [name$="[sort_order]"]'
        $(this).find('div[id^=option_]:not(.ignore-validate) .fieldset-alt [name$="[sort_order]"]').each(function (index) {
            $(this).val(index);
        });        
    },

    /**
     * Update selections positions for 'select' type of custom option
     */
    _updateSelectionsPositions: function () {
        $(this).find('tr:not(.ignore-validate) [name$="[sort_order]"]').each(function (index) {
            $(this).val(index);
        });
    },

    /**
     * Disable input data if "Read Only"
     */
    _bindReadOnlyMode: function () {
        if (this.isReadonly) {
            $('div.product-custom-options').find('button,input,select,textarea,').each(function () {
                $(this).prop('disabled', true);

                if ($(this).is('button')) {
                    $(this).addClass('disabled');
                }
            });
        }
    },

    _bindUseDefault: function (id, data) {
        var title = $('#' + id + '_title'),
            price = $('#' + id + '_price'),
            priceType = $('#' + id + '_price_type');
        //enable 'use default' link for title
        if (data.checkboxScopeTitle) {
            title.useDefault({
                field: '.field',
                useDefault: 'label[for$=_title]',
                checkbox: 'input[id$=_title_use_default]',
                label: 'span'
            });
        }
        //enable 'use default' link for price and price_type
        if (data.checkboxScopePrice) {
            price.useDefault({
                field: '.field',
                useDefault: 'label[for$=_price]',
                checkbox: 'input[id$=_price_use_default]',
                label: 'span'
            });
            //@TODO not work set default value for second field
            priceType.useDefault({
                field: '.field',
                useDefault: 'label[for$=_price]',
                checkbox: 'input[id$=_price_use_default]',
                label: 'span'
            });
        }
    },

    /**
     * Add selection value for 'select' type of custom option
     */
    addSelection: function (event) {

      if (!this.nextSelectId) {
        this.nextSelectId = 1;
      }

      var data = {},
          element = event.target || event.srcElement || event.currentTarget;
      if (typeof element !== 'undefined') {
          data.id = $(element).closest('#product_options_container_top > div')
              .find('[name^="product[options]"][name$="[new_option_id]"]').val();
          data.option_type_id = -1;
          data.select_id = this.nextSelectId;
          data.price = data.sku = '';
      } else {
          data = event;
          data.id = data.option_id;
          data.select_id = data.option_type_id;
          if (data.item_count > this.nextSelectId){
            this.nextSelectId = data.item_count;
          }  
      }
    
      var rowTmpl = mageTemplate('#custom-option-select-type-row-template');   
    
      var rowTmplHtml = rowTmpl({
          data: data
      });

      $(rowTmplHtml).appendTo($('#select_option_type_row_' + data.id));

      //set selected price_type value if set
      if (data.price_type) {
          var priceType = $('#' + this.fieldId + '_' + data.id + '_select_' + data.select_id + '_price_type');
          priceType.val(data.price_type).attr('data-store-label', data.price_type);
      }
      this._bindUseDefault(this.fieldId + '_' + data.id + '_select_' + data.select_id, data);
      this.refreshSortableElements();
    
      this.nextSelectId++;
    
      this.addRow(event, data.id, data.select_id);
    },

    /**
     * Add custom option
     */
    _addOption: function (event) {
        var newOptionId = this.lastOptionId + 1;
        this.addSection(newOptionId);
        this.addSectionContent(event);
        $('#'+newOptionId+'-content').trigger("show");
        
                    
    /*
        var data = {},
            element = event.target || event.srcElement || event.currentTarget,
            baseTmpl;

        if (typeof element !== 'undefined') {
            data.id = this.options.itemCount;
            data.type = '';
            data.option_id = 0;
        } else {
            data = event;
            this.options.itemCount = data.item_count;
        }

        baseTmpl = this.baseTmpl({
            data: data
        });

        $(baseTmpl)
            .appendTo(this.element.find('#product_options_container_top'))
            .find('.collapse').collapsable();

        //set selected type value if set
        if (data.type) {
            $('#' + this.fieldId + '_' + data.id + '_type').val(data.type).trigger('change', data);
        }

        //set selected is_require value if set
        if (data.is_require) {
            $('#' + this.fieldId + '_' + data.id + '_is_require').val(data.is_require).trigger('change');
        }
 */
        this.refreshSortableElements();
        this._bindCheckboxHandlers();
        this._bindReadOnlyMode();

   //     $('#' + this.fieldId + '_' + newOptionId + '_title').trigger('change');
       
      //  this.addOption(event);
    },

    refreshSortableElements: function () {
        if (!this.isReadonly) {
        //    this.element.sortable('refresh');
       //     this._updateOptionBoxPositions.apply(this.element);
            this._updateSelectionsPositions.apply(this.element);
            this._initSortableSelections();
        }

        return this;
    },

    getFreeOptionId: function (id) {
        return $('#' + this.fieldId + '_' + id).length ? this.getFreeOptionId(parseInt(id, 10) + 1) : id;
    },
        

    _____overrideMagentoJs : function(){
      productForm._orSubmit = productForm.submit;
      productForm.submit = function(url) {
        if (url != undefined){
          return productForm._orSubmit(optionExtended.onSubmit(url));	
        } else {
          optionExtended.onSubmit();
          return productForm._orSubmit();	  	
        }													
      } 
            
      productForm._validate = function(){
        new Ajax.Request(this.validationUrl,{
            asynchronous: false,         
            method: 'post',
            parameters: $(this.formId).serialize(),
            onComplete: this._processValidationResult.bind(this),
            onFailure: this._processFailure.bind(this)
        });
      }          
    }
  
  };
    
});