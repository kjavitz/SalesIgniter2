<?php
/**
 *
 */
class OrderTotalModuleBase extends ModuleBase
{

	/**
	 * @var array|null
	 */
	protected $data = array(
		'display_order' => 0,
		'value'         => 0
	);

	/**
	 * @param string $code
	 * @param bool   $forceEnable
	 * @param bool   $moduleDir
	 */
	public function init($code, $forceEnable = false, $moduleDir = false)
	{
		$this->import(new Installable);

		$this->setModuleType('orderTotal');
		parent::init($code, $forceEnable, $moduleDir);

		if ($this->configExists($this->getModuleInfo('display_order_key'))){
			$this->setDisplayOrder((int)$this->getConfigData($this->getModuleInfo('display_order_key')));
		}
	}

	/**
	 * @param $data
	 */
	public function setData($data)
	{
		$this->data = $data;
	}

	/**
	 * @param      $k
	 * @param null $v
	 */
	public function updateData($k, $v = null)
	{
		if (is_array($k)){
			foreach($k as $key => $value){
				$this->data[$key] = $value;
			}
		}
		else {
			$this->data[$k] = $v;
		}
	}

	/**
	 * @param $k
	 * @return mixed
	 */
	public function getData($k)
	{
		return $this->data[$k];
	}

	/**
	 * @param array $outputData
	 */
	public function process(array &$outputData)
	{
		$outputData['title'] = $this->getTitle() . ':';
		$outputData['text'] = $this->getText();
		$outputData['value'] = $this->getValue();
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->data['value'];
	}

	/**
	 * @param $val
	 */
	public function setValue($val)
	{
		$this->data['value'] = $val;
	}

	/**
	 * @param $val
	 */
	public function addToValue($val)
	{
		$this->data['value'] += $val;
	}

	/**
	 * @param $val
	 */
	public function subtractFromValue($val)
	{
		$this->data['value'] -= $val;
	}

	/**
	 * @param $val
	 */
	public function setDisplayOrder($val)
	{
		$this->data['display_order'] = $val;
	}

	/**
	 * @return mixed
	 */
	public function getDisplayOrder()
	{
		return $this->data['display_order'];
	}

	/**
	 *
	 */
	public function setText($val)
	{
		$this->data['text'] = $val;
	}

	/**
	 *
	 */
	public function getText()
	{
		return $this->data['text'];
	}

	/**
	 * @return array|null
	 */
	public function prepareSave()
	{
		return $this->data;
	}
}

?>