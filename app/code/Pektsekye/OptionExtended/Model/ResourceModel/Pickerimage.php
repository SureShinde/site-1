<?php

namespace Pektsekye\OptionExtended\Model\ResourceModel;

class Pickerimage extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
   
  protected $_mediaConfig;
  protected $_mediaDirectory;    
  

  public function __construct(
      \Magento\Framework\Model\ResourceModel\Db\Context $resource,      
      \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
      \Magento\Framework\Filesystem $filesystem
  ) {      
      $this->_mediaConfig      = $mediaConfig; 
      $this->_mediaDirectory   = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA); 
      parent::__construct($resource);
  }



  public function _construct()
  {    
    $this->_init('optionextended_pickerimage', 'ox_image_id');
  } 




  public function getImageData()
  {        
    $select = $this->getConnection()->select()->from($this->getMainTable());  
             
    return $this->getConnection()->fetchAll($select);                                
  }     
 
  
  
  
  public function saveImages($images)
  {         
     
    if (count($images) == 0)
      return $this;
              
    $data = array();      
    foreach ($images as $imageId => $value){
    
      $image = '';
      if (preg_match("/.tmp$/i", $value['image'])) {
        $image = $this->_moveImageFromTmp($value['image']);
      }	elseif (isset($value['image_saved_as']) && $value['image_saved_as'] != '' && (!isset($value['delete_image']) || $value['delete_image'] == '')){
        $image = $value['image_saved_as'];          
      }	 
             
      if ($image == ''){
        $this->getConnection()->delete($this->getTable('optionextended_pickerimage'), $this->getConnection()->quoteInto('ox_image_id = ?', $imageId));
        continue; 
      }
      
      $data = array(
        'title'  => $value['title'],  
        'image'  => $image                   
      ); 
      
      $statement = $this->getConnection()->select()
        ->from($this->getMainTable())
        ->where("ox_image_id=?", $imageId);

      if ($this->getConnection()->fetchRow($statement)) {
          $this->getConnection()->update(
            $this->getMainTable(),
            $data,
            "ox_image_id={$imageId}"
          );
      } else {
        $this->getConnection()->insert($this->getMainTable(), $data);
      }        
           
    }      
                          
  }   



  protected function _moveImageFromTmp($file)
  {
      if (strrpos($file, '.tmp') == strlen($file) - 4) {
          $file = substr($file, 0, strlen($file) - 4);
      }

      $destFile = dirname(
          $file
      ) . '/' . \Magento\MediaStorage\Model\File\Uploader::getNewFileName(
          $this->_mediaDirectory->getAbsolutePath($this->_mediaConfig->getMediaPath($file))
      );
      
      $this->_mediaDirectory->renameFile(
        $this->_mediaConfig->getTmpMediaPath($file),
        $this->_mediaConfig->getMediaPath($destFile)
      );	
	
      return str_replace('\\', '/', $destFile);
  }


}
