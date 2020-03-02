  
**Dependent Custom Options (gallery) 1.0 for Magento 2.2**  Dec 2 2017

Check the latest README file:
http://hottons.com/demo/m2/ox/README.html

This addition makes product custom options dependent. So you can set different size for different gender.  
Or display the file upload field if a customer wants to upload a sample.  
This extension has "Option Templates" and dataflow .csv Import / Export features.  
So that even if it takes long to create dependent options you will be able to create a template and then apply it to many products at once.  
Also you can upload images and add HTML note or description to an option.  

 
**Index:**

*   Installation
*   An example of how to set dependency
*   Picker Images
*   How to use "select children" Ss selector
*   Option Templates
*   Mark options as Selected By Default
*   Export options
*   Import options
*   Export Templates
*   Import Templates
*   Disable images in cart
*   Adjust front-end styles
*   Troubleshooting


### Installation

**1)** Upload 213 new files:  

    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Edit.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Edit/Form.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Edit/Tab/Main.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Edit/Tab/Options.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Edit/Tab/Products/Grid.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Edit/Tabs.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Grid.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Grid/Renderer/Action.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Grid/Renderer/Text.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Option.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Option/Edit.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Option/Edit/Form.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Option/Edit/Tab/Main.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Option/Edit/Tab/Values.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Option/Edit/Tabs.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Option/Grid.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Option/Grid/Renderer/Action.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Option/Grid/Renderer/Options.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Option/Import.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Option/Import/Form.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Option/Import/Grid.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Value.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Value/Edit.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Value/Edit/Form.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Value/Grid.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Value/Grid/Renderer/Image.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Value/Helper/Form/Image.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Value/Helper/Form/Image/Content.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Value/Helper/Form/Text.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Optiontemplate/Value/Helper/Form/Text/Content.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Ox/Export.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Ox/Pickerimage.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Product/Edit/Js.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Product/Edit/Tab/Options.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Product/Edit/Tab/Options/Option.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Product/Edit/Tab/Options/Template.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Product/Edit/Tab/Options/Type/Date.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Product/Edit/Tab/Options/Type/File.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Product/Edit/Tab/Options/Type/Select.php
    app/code/Pektsekye/OptionExtended/Block/Adminhtml/Product/Edit/Tab/Options/Type/Text.php
    app/code/Pektsekye/OptionExtended/Block/Product/View/Js.php
    app/code/Pektsekye/OptionExtended/composer.json
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Delete.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Duplicate.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Edit.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Grid.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Index.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/MassDelete.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/MassStatus.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/NewAction.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Option.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Option/Delete.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Option/Doimport.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Option/Duplicate.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Option/Edit.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Option/Grid.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Option/Import.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Option/Index.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Option/MassDelete.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Option/NewAction.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Option/Save.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/ProductsGrid.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Save.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/TemplateData.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Value.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Value/Delete.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Value/Edit.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Value/Grid.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Value/Index.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Value/MassDelete.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Value/NewAction.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Optiontemplate/Value/Save.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Ox/Export.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Ox/Export/Export.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Ox/Export/Import.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Ox/Export/Index.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Ox/Pickerimage.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Ox/Pickerimage/Index.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Ox/Pickerimage/Save.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Product/Edit.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Product/Edit/ImportOptions.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Product/Edit/LoadOption.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Product/Edit/Option.php
    app/code/Pektsekye/OptionExtended/Controller/Adminhtml/Product/Edit/Options.php
    app/code/Pektsekye/OptionExtended/etc/acl.xml
    app/code/Pektsekye/OptionExtended/etc/adminhtml/di.xml
    app/code/Pektsekye/OptionExtended/etc/adminhtml/events.xml
    app/code/Pektsekye/OptionExtended/etc/adminhtml/menu.xml
    app/code/Pektsekye/OptionExtended/etc/adminhtml/routes.xml
    app/code/Pektsekye/OptionExtended/etc/adminhtml/system.xml
    app/code/Pektsekye/OptionExtended/etc/config.xml
    app/code/Pektsekye/OptionExtended/etc/di.xml
    app/code/Pektsekye/OptionExtended/etc/frontend/di.xml
    app/code/Pektsekye/OptionExtended/etc/frontend/events.xml
    app/code/Pektsekye/OptionExtended/etc/module.xml
    app/code/Pektsekye/OptionExtended/etc/product_options.xml
    app/code/Pektsekye/OptionExtended/etc/webapi_rest/events.xml    
    app/code/Pektsekye/OptionExtended/Helper/Data.php
    app/code/Pektsekye/OptionExtended/i18n/en_US.csv
    app/code/Pektsekye/OptionExtended/LICENSE.txt
    app/code/Pektsekye/OptionExtended/Model/Catalog/Product/Option/Type/DatePlugin.php
    app/code/Pektsekye/OptionExtended/Model/Catalog/Product/Option/Type/FilePlugin.php
    app/code/Pektsekye/OptionExtended/Model/Catalog/Product/Option/Type/SelectPlugin.php
    app/code/Pektsekye/OptionExtended/Model/Catalog/Product/Option/Type/TextPlugin.php
    app/code/Pektsekye/OptionExtended/Model/Catalog/Product/Type/Plugin.php
    app/code/Pektsekye/OptionExtended/Model/CsvImportHandler.php
    app/code/Pektsekye/OptionExtended/Model/Observer/AddOptionTemplatesToProduct.php
    app/code/Pektsekye/OptionExtended/Model/Observer/OptionSaveAfter.php
    app/code/Pektsekye/OptionExtended/Model/Observer/ProductSaveAfter.php
    app/code/Pektsekye/OptionExtended/Model/Observer/ProductSaveBefore.php
    app/code/Pektsekye/OptionExtended/Model/Option.php
    app/code/Pektsekye/OptionExtended/Model/Pickerimage.php
    app/code/Pektsekye/OptionExtended/Model/ResourceModel/Catalog/Product/CollectionPlugin.php
    app/code/Pektsekye/OptionExtended/Model/ResourceModel/Option.php
    app/code/Pektsekye/OptionExtended/Model/ResourceModel/Option/Collection.php
    app/code/Pektsekye/OptionExtended/Model/ResourceModel/Pickerimage.php
    app/code/Pektsekye/OptionExtended/Model/ResourceModel/Template.php
    app/code/Pektsekye/OptionExtended/Model/ResourceModel/Template/Collection.php
    app/code/Pektsekye/OptionExtended/Model/ResourceModel/Template/Option.php
    app/code/Pektsekye/OptionExtended/Model/ResourceModel/Template/Option/Collection.php
    app/code/Pektsekye/OptionExtended/Model/ResourceModel/Template/Value.php
    app/code/Pektsekye/OptionExtended/Model/ResourceModel/Template/Value/Collection.php
    app/code/Pektsekye/OptionExtended/Model/ResourceModel/Value.php
    app/code/Pektsekye/OptionExtended/Model/ResourceModel/Value/Collection.php
    app/code/Pektsekye/OptionExtended/Model/Template.php
    app/code/Pektsekye/OptionExtended/Model/Template/Option.php
    app/code/Pektsekye/OptionExtended/Model/Template/Value.php
    app/code/Pektsekye/OptionExtended/Model/Value.php
    app/code/Pektsekye/OptionExtended/Plugin/Catalog/Model/Product/Option/Repository.php
    app/code/Pektsekye/OptionExtended/Plugin/Catalog/Model/Product/Option/SaveHandler.php    
    app/code/Pektsekye/OptionExtended/Plugin/Catalog/Model/Product/Option/Value.php   
    app/code/Pektsekye/OptionExtended/Plugin/Catalog/Controller/Adminhtml/Product/Initialization/Helper.php     
    app/code/Pektsekye/OptionExtended/Plugin/Catalog/Ui/DataProvider/Product/Form/Modifier/CustomOptions.php
    app/code/Pektsekye/OptionExtended/README.md
    app/code/Pektsekye/OptionExtended/registration.php
    app/code/Pektsekye/OptionExtended/Setup/InstallSchema.php
    app/code/Pektsekye/OptionExtended/view/adminhtml/layout/CATALOG_PRODUCT_COMPOSITE_CONFIGURE.xml
    app/code/Pektsekye/OptionExtended/view/adminhtml/layout/catalog_product_new.xml
    app/code/Pektsekye/OptionExtended/view/adminhtml/layout/optionextended_optiontemplate_edit.xml
    app/code/Pektsekye/OptionExtended/view/adminhtml/layout/optionextended_optiontemplate_index.xml
    app/code/Pektsekye/OptionExtended/view/adminhtml/layout/optionextended_optiontemplate_option_edit.xml
    app/code/Pektsekye/OptionExtended/view/adminhtml/layout/optionextended_optiontemplate_option_import.xml
    app/code/Pektsekye/OptionExtended/view/adminhtml/layout/optionextended_optiontemplate_option_index.xml
    app/code/Pektsekye/OptionExtended/view/adminhtml/layout/optionextended_optiontemplate_value_edit.xml
    app/code/Pektsekye/OptionExtended/view/adminhtml/layout/optionextended_optiontemplate_value_index.xml
    app/code/Pektsekye/OptionExtended/view/adminhtml/layout/optionextended_ox_export_index.xml
    app/code/Pektsekye/OptionExtended/view/adminhtml/layout/optionextended_ox_pickerimage_index.xml
    app/code/Pektsekye/OptionExtended/view/adminhtml/layout/sales_order_create_index.xml
    app/code/Pektsekye/OptionExtended/view/adminhtml/requirejs-config.js
    app/code/Pektsekye/OptionExtended/view/adminhtml/templates/optiontemplate/edit/tab/options.phtml
    app/code/Pektsekye/OptionExtended/view/adminhtml/templates/optiontemplate/helper/image.phtml
    app/code/Pektsekye/OptionExtended/view/adminhtml/templates/optiontemplate/helper/text.phtml
    app/code/Pektsekye/OptionExtended/view/adminhtml/templates/optiontemplate/option/edit/tab/values.phtml
    app/code/Pektsekye/OptionExtended/view/adminhtml/templates/ox/export.phtml
    app/code/Pektsekye/OptionExtended/view/adminhtml/templates/ox/pickerimage.phtml
    app/code/Pektsekye/OptionExtended/view/adminhtml/templates/product/composite/configure/js.phtml
    app/code/Pektsekye/OptionExtended/view/adminhtml/templates/product/edit/js.phtml
    app/code/Pektsekye/OptionExtended/view/adminhtml/templates/product/edit/options.phtml
    app/code/Pektsekye/OptionExtended/view/adminhtml/templates/product/edit/options/option.phtml
    app/code/Pektsekye/OptionExtended/view/adminhtml/templates/product/edit/options/template.phtml
    app/code/Pektsekye/OptionExtended/view/adminhtml/templates/product/edit/options/type/date.phtml
    app/code/Pektsekye/OptionExtended/view/adminhtml/templates/product/edit/options/type/file.phtml
    app/code/Pektsekye/OptionExtended/view/adminhtml/templates/product/edit/options/type/select.phtml
    app/code/Pektsekye/OptionExtended/view/adminhtml/templates/product/edit/options/type/text.phtml
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/optiontemplate/.DS_Store
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/optiontemplate/option/edit.css
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/optiontemplate/option/edit.js
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/optiontemplate/option/grid.css
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/optiontemplate/value/edit.css
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/optiontemplate/value/edit.js
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/optiontemplate/value/grid.css
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/order_create.css
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/ox/export/main.css
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/ox/pickerimage/edit.js
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/ox/pickerimage/main.css
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/product/edit/images/arrows-bg.svg
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/product/edit/images/draggable-handle-vertical.png
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/product/edit/js/dependency.js
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/product/edit/js/duplicate.js
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/product/edit/js/dynamic.js
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/product/edit/js/ids.js
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/product/edit/js/main.js
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/product/edit/js/optiontemplate.js
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/product/edit/js/override.js
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/product/edit/js/parent.js
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/product/edit/js/sd.js
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/product/edit/js/uploader.js
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/product/edit/js/util.js
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/product/edit/js/widget.js
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/product/edit/main.css
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/product/edit/styles-old.css
    app/code/Pektsekye/OptionExtended/view/adminhtml/web/template/form/components/js.html
    app/code/Pektsekye/OptionExtended/view/base/web/images/border1.png
    app/code/Pektsekye/OptionExtended/view/base/web/images/border2.png
    app/code/Pektsekye/OptionExtended/view/base/web/images/check_icon.png
    app/code/Pektsekye/OptionExtended/view/base/web/images/info_icon.gif
    app/code/Pektsekye/OptionExtended/view/base/web/images/loading.gif
    app/code/Pektsekye/OptionExtended/view/base/web/images/spacer.gif
    app/code/Pektsekye/OptionExtended/view/base/web/js/dependent.js
    app/code/Pektsekye/OptionExtended/view/base/web/js/images.js
    app/code/Pektsekye/OptionExtended/view/base/web/js/jquery.oxcolorbox-min.js
    app/code/Pektsekye/OptionExtended/view/base/web/js/jquery.tooltipster.min.js
    app/code/Pektsekye/OptionExtended/view/base/web/js/main.js
    app/code/Pektsekye/OptionExtended/view/base/web/js/widget.js
    app/code/Pektsekye/OptionExtended/view/base/web/oxcolorbox.css
    app/code/Pektsekye/OptionExtended/view/base/web/tooltipster-shadow.css
    app/code/Pektsekye/OptionExtended/view/base/web/tooltipster.css
    app/code/Pektsekye/OptionExtended/view/frontend/layout/catalog_product_view.xml
    app/code/Pektsekye/OptionExtended/view/frontend/requirejs-config.js
    app/code/Pektsekye/OptionExtended/view/frontend/templates/product/view/js.phtml
    app/code/Pektsekye/OptionExtended/view/frontend/web/main.css 
  

**2)** Connect to your website via SSH:  
Type in Terminal of your computer:  
```
ssh -p 2222 username@yourdomain.com  
```
Then enter your server password  

If you are connected to your server change directory with command:  
```
cd /full_path_to_your_magento_root_directory  
```
Update magento modules with command:  
```
./bin/magento setup:upgrade  
```
>NOTE: If it shows permission error make it executable with command: ` chmod +x bin/magento `  

**3)** Manually remove cached _requirejs diectory:  

    pub/static/_requirejs  

**4)** Refresh magento cache:  
Go to _Magento admin panel -> System -> Cache Managment_  
Click the "Flush Magento Cache" button  


### An example of how to set dependency

**1)** Go to Magento admin panel -> Products -> Catalog  
**2)** Find a product with type Simple Product and without custom options then click the "Edit" link.  
**3)** Add a custom option to it: Title - "Gender", Input Type - "Drop-down".  
**4)** Add rows to it: "Mens", "Womens".  
**5)** Add second custom option: Title - "Size", Input Type - "Drop-down".  
**6)** Add four rows with titles: "S", "M", "L", "XL".  
**7)** Scroll the page back to the "Gender" option.  
**8)** Find the "Children" field of the "Mens" row.  
**9)** Enter two row ids of the "Size" option separated with commas: 3,4  
**10)** Find the "Children" field of the "Womens" row.  
**11)** Enter another two row ids of the "Size" option: 5,6  
**12)** Check everything with the  
**13)** Click the "Save" button.  
**14)** Open your product page in the front-end.  
**15)** If you select gender "Mens" the size option must appear with two values S and M.

  



### Picker Images

To save time you can upload images directly for picker options in:  
_Products -> Picker Images_   

The word from the "Match option by title" field will be used to match product option values by title.  
For example the word "royalblue" will match product option values with titles:  
Royal Blue, RoyalBlue, Royalblue  

The option must have layout "Picker" or "Picker & Main"  

  



### How to use "select children" Ss selector

The Ss selector does not displays an option that already has a parent option.  
The Ss selector does not allow to select any of the parent option as child.  
For example if Option 1 has Option 2 as child and Option 2 has Option 3 as child you will not be able to select Option 1 as child for the Option 3.  
If all the options already have a parent or the product has just one option the Ss selector does not appear.  

>NOTE: Ss selector is just to save your time. If you need more complex dependency or you need to copy the same ids for new rows you can enter row ids manually into the Children field.  



### Option Templates

Create and manage option templates in:  
_Products -> Option Templates_   

You can import options from product by clicking "Import Options From Product" on the template options page   
The "Import Options From Product" appears only when template does not have options   

You can apply a template to a product on the Products tab in _Products -> OptionTemplates -> edit template page_   
Or on the Custom Options tab in  _Products -> Catalog -> edit product page_   

  



### Mark options as Selected by Default

You can mark options to be pre selected when product page loads.   
Use radio buttons at the end of option value rows on the Custom Options tab of the edit product page.
  



### To export options

**1)** Go to _Magento admin panel -> System -> Dependent Product Options_  
**2)** Click the "Options" button in the "Export Product Custom Options" section.   
**3)** Click the "Values" button in the same "Export Product Custom Options" section.   
**4)** ( optional ) Click the "Options for Translation"and "Values for Translation" buttons in the same section to export additional option titles of different magento stores (languages).  
**5)** Find product_options.csv and product_option_values.csv files in the "Downloads" directory specified in your browser  

  



### To import options

**FIRST STEP** Prepare two import files with the Excel program:  

**1)** product_options.csv  

Required fields are five:  
"product_sku", "code", "title", "type" and "row_id" when type is "field", "area", "file", "date", "date_time", "time".  
Valid option types are:  
"field", "area", "file", "drop_down", "radio", "checkbox", "multiple", "date", "date_time", "time" 
Valid price types are:  
"fixed", "percent"  
Valid layouts are:  
"above","below","before","swap","picker","pickerswap","grid","list"  
  
  
**2)** product_option_values.csv  

Required fields are three:  
"option_code", "row_id", "title".  
Valid price types are:  
"fixed", "percent"  


**3)** product_options_translate.csv ( optional )  

Required fields are two:  
"option_code", "store"  

  
**4)** product_option_values_translate.csv ( optional )  

Required fields are three:  
"option_code", "row_id", "store" 
 

The "**code**" field is a unique option code. Ex.: opt-166-82  

The "**row_id**" field is a unique number of the option value within one product .  
It means that one product cannot have repeated ids but two different products can have the same row ids.  
In the product_options.csv file this field is required only when option type is "field", "area", "file", "date", "date_time", "time".  
For other option types ("drop_down", "radio", "checkbox", "multiple") row_id of the option value is used.  

The "**children**" field contains comma separated row ids of the other option values of the same product.  
It must contain only existent row ids the product.  
Or it can be empty.  

The "**image**" field contains the name of the image uploaded to the media/import directory.  
Or it can contain the path to the existent Magento image in the pub/media/catalog/product directory.  

The "**store**" field contains the code of the store (for example: default, german, french)  
To find out the store code go to _Stores -> All Stores_   
Then click on the store link to edit it.  

>NOTE: The store code is not the same as the store name. For example the store name is "Default Store View" and its code is "default"  
  

**SECOND STEP**  

**2)** Go to Magento admin panel -> System -> Dependent Product Options  
**3)** Choose your product_options.csv file and click the "Options" button in the "Import Product Custom Options" section.   
**4)** Choose your product_option_values.csv file and click the "Values" button in the same "Import Product Custom Options" section.   
**5)** ( optional ) Import product_options_translate.csv and product_option_values_translate.csv files with the "Options for Translation" and "Values for Translation" buttons in the same section to import additional option titles of different magento stores (languages).  

>NOTE: Options and option values are tied together with "option_code" field.  
Option values and option value titles to translate are tied together with "option_code" and "row_id" fields.  
When you run option import profile all the options with option values of all the stores (german, french...) of the same product are deleted.  
So that before start importing options do export and save all .csv files **to not lose data !**  

  



### To export templates

**1)** Go to Magento admin panel -> System -> Dependent Product Options  
**2)** Click the "Template Entities" button in the "Export Option Templates" section.   
**3)** Click the "Template Products" button in the same "Export Option Templates" section.   
**4)** Click the "Template Options" button in the same section.  
**5)** Click the "Template Values" button in the same section.  
**6)** ( optional ) Click the "Template Options for Translation"and "Template Values for Translation" buttons in the same section to export additional option titles of different magento stores (languages).  
**7)** Find template_entities.csv, template_products.csv, template_options.csv, template_values.csv files in the "Downloads" directory specified in your browser  

  



### To import templates

**FIRST STEP** Prepare four import files with the Excel program:  

**1)** templates.csv  

Required fields are two:  
"code", "title"  

  
**2)** template_products.csv  

Required fields are two:  
"product_sku", "template_code".  

  
**3)** template_options.csv  

Required fields are five:  
"template_code", "code", "title", "type" and "row_id" when type is "field", "area", "file", "date", "date_time", "time". 
Valid option types are:  
"field", "area", "file", "drop_down", "radio", "checkbox", "multiple", "date", "date_time", "time"  
Valid price types are:  
"fixed", "percent"  
Valid layouts are:  
"above","below","before","swap","picker","pickerswap","grid","list"  

 
**4)** template_values.csv  

Required fields are three:  
"option_code", "row_id", "title".  
Valid price types are:  
"fixed", "percent" 

**5)** template_options_translate.csv ( optional )  

Required fields are two:  
"option_code", "store"

 
**6)** template_values_translate.csv ( optional )  

Required fields are three:  
"option_code", "row_id", "store"  


The "**code**" field is a unique option code. Ex.: opt-166-82  

The "**row_id**" field is a unique number of the option value within one product .  
It means that one product cannot have repeated ids but two different products can have the same row ids.  
In the template_options.csv file this field is required only when option type is "field", "area", "file", "date", "date_time", "time".  
For other option types ("drop_down", "radio", "checkbox", "multiple") row_id of the option value is used.  

The "**children**" field contains comma separated row ids of the other option values of the same product.  
It must contain only existent row ids of the product.  
Or it can be empty.  

The "**image**" field contains the name of the image uploaded to the media/import directory.  
Or it can contain the path to the existent Magento image in the media/catalog/product directory.  

The "**store**" field contains the code of the store (for example: default, german, french)  
To find out the store code go to _Magento admin panel -> System -> Manage Stores_   
Then click on the store link to edit it.  

>NOTE: The store code is not the same as the store name. For example the store name is "Default Store View" and its code is "default"  

  

**SECOND STEP** Upload files into var/import directory.  

**2)** Go to Magento admin panel -> System -> Dependent Product Options  
**3)** Choose your template_entities.csv file and click the "Template Entities" button in the "Import Option Templates" section.   
**4)** Choose your template_products.csv file and click the "Template Products" button in the same "Import Option Templates" section.   
**3)** Choose your template_options.csv file and click the "Template Options" button in the same section.  
**4)** Choose your template_values.csv file and click the "Template Values" button in the same section.  
**5)** ( optional ) Import template_options_translate.csv and template_option_values_translate.csv files with the "Template Options for Translation" and "Template Values for Translation" buttons in the same section to import additional option titles of different magento stores (languages).  

>NOTE: Templates and template options are tied together with "template_code" field.  
Options and option values are tied together with "option_code" field.  
Option values and option value titles to translate are tied together with "option_code" and "row_id" fields.  
When you run template import profile all the options with option values of all the stores (german, french...) of the same template are deleted.  
So that before start importing options do export and save all .csv files **to not lose data !**  

  



### Disable images in cart

Go to _Magento admin panel -> Stores -> Configuration -> Sales -> Checkout -> Shopping Cart -> Custom Option Images_  
  

  



### Troubleshooting

*   To force Magento reinstall the module.
    
    Remove the "Pektsekye_OptionExtended" entry from the "setup_module" database table with phpMyAdmin.

    Connect to your server via SSH and change directory with command:
    ```
    cd /full_path_to_your_magento_root_directory
    ```

    Update magento modules with command:
    ```
    ./bin/magento setup:upgrade
    ```
    
    >NOTE: You can export template options and product custom options as .csv files before reinstalling the module to not lose data.


*   If you are not sure whether a problem is because of this extension or not.
    
    Try to disable this extension by setting: 
   
    ```
    'Pektsekye_OptionExtended' => 0,
    ```
       
    in the file:  
    
        app/etc/config.php

