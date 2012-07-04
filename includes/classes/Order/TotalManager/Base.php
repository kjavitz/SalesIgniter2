<?php
/**
 * Totals manager for the order class
 *
 * @package   Order\TotalManager
 * @author    Stephen Walker <stephen@itwebexperts.com>
 * @since     1.0
 * @copyright 2012 I.T. Web Experts
 * @license   http://itwebexperts.com/license/ses-license.php
 */

class OrderTotalManager
{

	/**
	 * @var OrderTotal[]
	 */
	protected $totals = array();

	/**
	 *
	 */
	public function __construct()
	{
	}

	/**
	 * @param $ModuleCode
	 * @return bool|OrderTotal
	 */
	public function &getTotal($ModuleCode)
	{
		$return = false;
		if (isset($this->totals[$ModuleCode])){
			$return =& $this->totals[$ModuleCode];
		}
		return $return;
	}

	/**
	 * @param OrderTotal $orderTotal
	 */
	public function add(OrderTotal $orderTotal)
	{
		$this->totals[$orderTotal
			->getModule()
			->getCode()] = $orderTotal;
	}

	/**
	 * @param string $type
	 * @return float|null
	 */
	public function getTotalValue($type)
	{
		$OrderTotal = $this->get($type);
		if ($OrderTotal !== false){
			return (float)$OrderTotal
				->getModule()
				->getValue();
		}
		return null;
	}

	/**
	 * @param string $moduleType
	 * @return bool|OrderTotal
	 */
	public function get($moduleType)
	{
		$orderTotal = false;
		if (isset($this->totals[$moduleType])){
			$orderTotal = $this->totals[$moduleType];
		}
		return $orderTotal;
	}

	/**
	 * @return array|OrderTotal[]
	 */
	public function getAll()
	{
		$TotalsSorted = $this->totals;
		usort($TotalsSorted, function ($a, $b)
		{
			return ($a
				->getModule()
				->getDisplayOrder() < $b
				->getModule()
				->getDisplayOrder() ? -1 : 1);
		});
		return $TotalsSorted;
	}

	/**
	 * @return htmlElement_table
	 */
	public function show()
	{
		$orderTotalTable = htmlBase::newElement('table')
			->setCellPadding(2)
			->setCellSpacing(0);

		foreach($this->totals as $OrderTotal){
			$orderTotalTable->addBodyRow(array(
				'columns' => array(
					array(
						'align' => 'right',
						'text'  => $OrderTotal
							->getModule()
							->getTitle()
					),
					array(
						'align' => 'right',
						'text'  => $OrderTotal
							->getModule()
							->getText()
					)
				)
			));
		}

		return $orderTotalTable;
	}

	/**
	 * @return array
	 */
	public function prepareJsonSave()
	{
		$TotalJsonArray = array();
		foreach($this->totals as $OrderTotal){
			$TotalJsonArray[] = $OrderTotal->prepareJsonSave();
		}
		return $TotalJsonArray;
	}

	/**
	 * Used when loading the sale from the database
	 *
	 * @param AccountsReceivableSalesTotals $Total
	 */
	public function jsonDecodeTotal(AccountsReceivableSalesTotals $Total)
	{
		$TotalDecoded = json_decode($Total->total_json, true);
		$OrderTotal = new OrderTotal($TotalDecoded['data']['module_code']);
		$OrderTotal->jsonDecode($TotalDecoded);

		$this->add($OrderTotal);
	}

	/**
	 * @param OrderProductManager $ProductManager
	 */
	public function onProductAdded(OrderProductManager $ProductManager)
	{
		foreach($this->getAll() as $Module){
			//echo __FILE__ . '::' . __LINE__ . '<br>';
			//echo '<div style="margin-left:15px;">';
			$Module->onProductAdded($ProductManager);
			//echo '</div>';
		}
	}

	/**
	 * @param OrderProductManager $ProductManager
	 */
	public function onProductUpdated(OrderProductManager $ProductManager)
	{
		foreach($this->getAll() as $Module){
			//echo __FILE__ . '::' . __LINE__ . '::' . $Module->getModule()->getTitle() . '<br>';
			//echo '<div style="margin-left:15px;">';
			$Module->onProductUpdated($ProductManager);
			//echo '</div>';
		}
	}

	public function getEmailList()
	{
		$orderTotals = '';
		foreach($this->getAll() as $OrderTotal){
			$orderTotals .= strip_tags($OrderTotal
				->getModule()
				->getTitle()) . ' ' . strip_tags($OrderTotal
				->getModule()
				->getText()) . "\n";
		}
		return $orderTotals;
	}
}

require(dirname(__FILE__) . '/Total.php');
