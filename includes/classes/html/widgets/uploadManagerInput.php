<?php
/**
 * Upload Manager Widget Class
 * @package Html
 */
class htmlWidget_uploadManagerInput implements htmlWidgetPlugin {
	protected $inputElement;
	
	public function __construct(){
		$this->inputElement = htmlBase::newElement('input')
		->setType('text')
		->addClass('uploadManagerInput');
		
		$this->previewWidth = 150;
		$this->previewHeight = 150;
		$this->autoUpload = false;
		$this->showPreview = false;
		$this->showLocalField = false;
		$this->isMulti = false;
		$this->fileType = 'image';
		$this->showPhpUploadSize = false;
		$this->previewFiles = array();
		$this->setVal = null;
	}
	
	public function __call($function, $args){
		$return = call_user_func_array(array($this->inputElement, $function), $args);
		if (!is_object($return)){
			return $return;
		}
		return $this;
	}
	
	/* Required Functions From Interface: htmlElementPlugin --BEGIN-- */
	public function startChain(){
		return $this;
	}
	
	public function setId($val){
		$this->inputElement->setId($val);
		return $this;
	}
	
	public function setName($val){
		$this->inputElement->setName($val);
		return $this;
	}
	
	public function setValue($val){
		$this->setVal = $val;
		return $this;
	}
	
	public function val($val){
		$this->setVal = $val;
		return $this;
	}
	
	public function draw(){
		if ($this->inputElement->hasAttr('id') === false || $this->inputElement->attr('id') == ''){
			$this->inputElement->setIdRandom();
		}
		
		if ($this->inputElement->hasAttr('name') === false || $this->inputElement->attr('name') == ''){
			$this->inputElement->setNameRandom();
		}
		
		$this->inputElement->attr('data-file_type', $this->fileType);
		
		if ($this->isMulti === true){
			$this->inputElement->attr('data-is_multi', 'true');
		}else{
			$this->inputElement->removeAttr('data-is_multi');
		}
		
		if ($this->autoUpload === true){
			$this->inputElement->attr('data-auto_upload', 'true');
		}else{
			$this->inputElement->removeAttr('data-auto_upload');
		}
		
		$tableColumns = array();
		if ($this->showPreview === true){
			$this->inputElement->attr('data-has_preview', true);
			
			$previewHtml = '';
			if (!empty($this->previewFiles) && $this->fileType == 'image'){
				if ($this->isMulti === true){
					foreach($this->previewFiles as $fileName){
						$previewContainer = $this->buildImagePreview($fileName);
						$previewHtml .= $previewContainer->draw();
					}
					$this->inputElement->val(implode(';', $this->previewFiles));
				}else{
					$previewFile = $this->previewFiles[sizeof($this->previewFiles)-1];
					$previewContainer = $this->buildImagePreview($previewFile);
					$this->inputElement->val($previewFile);
					$previewHtml .= $previewContainer->draw();
				}
			}
			
			$tableColumns[1] = array(
				'attr' => array(
					'id' => $this->inputElement->attr('id') . '_previewContainer'
				),
				'text' => $previewHtml
			);
		}else{
			if (is_null($this->setVal) === false){
				$this->inputElement->val($this->setVal);
			}
		}
		
		$maxUpload = '';
		if ($this->showPhpUploadSize === true){
			$maxUpload = '<br />Maximum Upload Size: ' . ini_get('upload_max_filesize') . '<br />';
		}
		
		$tableColumns[0] = array(
			'valign' => 'top',
			'text' => $this->inputElement->draw() . ($this->isMulti === false ? '<br />Current: ' . $this->inputElement->val() : '') . $maxUpload
		);
		
		ksort($tableColumns);
		
		$html = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0);
		
		if ($this->isMulti === true && $this->showPreview === true){
			foreach($tableColumns as $colInfo){
				$html->addBodyRow(array(
					'columns' => array($colInfo)
				));
			}
		}else{
			$html->addBodyRow(array(
				'columns' => $tableColumns
			));
		}
		
		if ($this->showLocalField === true){
			$localField = htmlBase::newElement('input')
			->setName($this->inputElement->attr('name') . '_local')
			->setLabel('Local: ')
			->setLabelPosition('before')
			->val($this->setVal);
			
			$html->addBodyRow(array(
				'columns' => array(
					array('colspan' => ($this->isMulti === true && $this->showPreview === true ? 1 : 2), 'text' => $localField)
				)
			));
		}
		
		$debugField = htmlBase::newElement('textarea')
		->setID($this->inputElement->attr('id') . '_uploadDebugOutput')
		->css(array(
			'width' => '300px',
			'height' => '150px'
		))
		->hide();
		
		$html->addBodyRow(array(
			'columns' => array(
				array('colspan' => ($this->isMulti === true && $this->showPreview === true ? 1 : 2), 'text' => $debugField)
			)
		));
		
		return $html->draw();
	}
	/* Required Functions From Interface: htmlElementPlugin --END-- */
	
	private function buildImagePreview($imgSrc){
		global $fileTypeUploadDirs;
		$zoomIcon = htmlBase::newElement('icon')
		->setType('zoomIn')
		->css(array(
			'position' => 'absolute',
			'bottom' => '5px',
			'right' => '26px'
		));
		
		$deleteIcon = htmlBase::newElement('icon')
		->setType('closeThick')
		->addClass('deleteImage')
		->css(array(
			'position' => 'absolute',
			'bottom' => '5px',
			'right' => '5px'
		));
		
		$previewImage = htmlBase::newElement('image')
		->css(array(
			'width' => $this->previewWidth,
			'height' => $this->previewHeight
		))
		->setSource($fileTypeUploadDirs[$this->fileType]['rel'] . $imgSrc)
		->thumbnailImage(true);
				
		$previewEl = htmlBase::newElement('a')
		->setId($this->inputElement->attr('data-preview_field_id'))
		->addClass('fancyBox')
		->css(array(
			'display' => 'block'
		))
		->setHref($fileTypeUploadDirs[$this->fileType]['rel'] . $imgSrc)
		->append($previewImage);
				
		$previewThumbContainer = htmlBase::newElement('div')
		->css(array(
			'text-align' => 'center'
		))
		->attr('data-image_file_name', $imgSrc)
		->attr('data-input_id', $this->inputElement->attr('id'))
		->append($previewEl)
		->append($zoomIcon)
		->append($deleteIcon);
				
		$previewContainer = htmlBase::newElement('div')
		->css(array(
			'position' => 'relative',
			'float'  => 'left',
			'width'  => $this->previewWidth . 'px',
			'height' => $this->previewHeight + 26 . 'px',
			'border' => '1px solid #cccccc',
			'margin' => '.5em'
		))
		->append($previewThumbContainer);
				
		return $previewContainer;
	}
	
	public function setFileType($val){
		$this->fileType = $val;
		return $this;
	}
	
	public function allowMultipleUploads($val){
		$this->isMulti = $val;
		return $this;
	}
	
	public function showPreview($val){
		$this->showPreview = $val;
		return $this;
	}
	
	public function autoUpload($val){
		$this->autoUpload = $val;
		return $this;
	}
	
	public function setPreviewWidth($val){
		$this->previewWidth = $val;
		return $this;
	}
	
	public function setPreviewHeight($val){
		$this->previewHeight = $val;
		return $this;
	}
	
	public function setPreviewFile($val){
		$this->previewFiles[] = $val;
		return $this;
	}
	
	public function showMaxUploadSize($val){
		$this->showPhpUploadSize = $val;
		return $this;
	}
	
	public function allowLocalSelection($val){
		$this->showLocalField = $val;
		return $this;
	}
}
?>