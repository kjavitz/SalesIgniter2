<?php
/**
 * Exception class for warnings
 * @package ExceptionManager
 */
class ExceptionWarning extends ExceptionParser{
	/**
	 * Icon css class to use for the exception report
	 * @var string
	 */
	private $iconClass = '';

	public function __construct(ErrorException $Exception){
		$this->e = $Exception;
	}

	public function setIconClass($class){
		$this->iconClass = $class;
	}

	public function setErrorDescription($val){
		$this->errDesc = $val;
	}

	public function addInfo($val){
		$this->addedInfo = $val;
	}

	public function output(){
		$ReportContainer = htmlBase::newElement('div')->addClass('ui-messageStack ui-messageStack-footerStack');
		$TableContainer = htmlBase::newElement('div')->addClass('ui-messageStack-warning ui-messageStack-warning ui-corner-all');
		$Icon = htmlBase::newElement('span')->addClass('ui-messageStack-icon ui-icon ui-icon-warning');
		$TableContainer->append($Icon);
		$ErrorTable = htmlBase::newElement('table')->setCellPadding(2)->setCellSpacing(0)->css(array(
			'display' => 'inline-block',
			'vertical-align' => 'top'
		));
		$ErrorTable->addBodyRow(array('columns' => array(array('addCls' => 'main', 'text' => '<b>PHP Error Description:</b>'), array('addCls' => 'main', 'text' => $this->errDesc))));
		$ErrorTable->addBodyRow(array('columns' => array(array('addCls' => 'main', 'text' => '<b>Server Message:</b>'), array('addCls' => 'main', 'text' => $this->e->getMessage()))));
		if (isset($this->e->addedInfo)){
			$this->parseAddedInfo(&$ErrorTable);
		}
		$ErrorTable->addBodyRow(array('columns' => array(array('addCls' => 'main', 'text' => '<b>Time Reported:</b>'), array('addCls' => 'main', 'text' => date('m-d-Y H:i:s')))));
		$ErrorTable->addBodyRow(array('columns' => array(array('addCls' => 'main', 'valign' => 'top', 'text' => '<b>PHP Trace:</b>'), array('addCls' => 'main', 'text' => '<div><a href="#" class="phpTraceView">View Trace</a></div>' . $this->parseTrace($this->e->getTrace())))));
		$TableContainer->append($ErrorTable);
		$ReportContainer->append($TableContainer);

		if (isset($_GET['rType']) && $_GET['rType'] == 'ajax'){
			return strip_tags(str_replace('</tr>', "\n", $ReportContainer->draw()));
		}else{
			return $ReportContainer->draw();
		}
	}
}

?>