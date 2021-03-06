<?php
/**
 * Order total class for the checkout sale order total manager
 *
 * @package   CheckoutSale\TotalManager
 * @author    Stephen Walker <stephen@itwebexperts.com>
 * @since     2.0
 * @copyright 2012 I.T. Web Experts
 * @license   http://itwebexperts.com/license/ses-license.php
 */

class CheckoutSaleTotal extends OrderTotal
{

	/**
	 * @param array $TotalInfo
	 */
	public function init(array $TotalInfo)
	{
		$this->data = array_merge($this->data, $TotalInfo['data']);

		$this->Module = $this->getTotalModule($this->data['module_code']);
		if (isset($TotalInfo['module_json'])){
			$this->Module->load($TotalInfo['module_json']);
		}
	}

	/**
	 * @param $ModuleCode
	 * @return OrderTotalModuleBase
	 */
	public function getTotalModule($ModuleCode)
	{
		if (file_exists(sysConfig::getDirFsCatalog() . 'clientData/includes/classes/CheckoutSale/TotalManager/TotalModules/' . $ModuleCode . '/module.php')){
			$className = 'CheckoutSaleTotal' . ucfirst($ModuleCode);
			if (!class_exists($className)){
				require(sysConfig::getDirFsCatalog() . 'clientData/includes/classes/CheckoutSale/TotalManager/TotalModules/' . $ModuleCode . '/module.php');
			}
			$Module = new $className();
		}
		elseif (file_exists(__DIR__ . '/TotalModules/' . $ModuleCode . '/module.php')){
			$className = 'CheckoutSaleTotal' . ucfirst($ModuleCode);
			if (!class_exists($className)){
				require(__DIR__ . '/TotalModules/' . $ModuleCode . '/module.php');
			}
			$Module = new $className();
		}
		else {
			$Module = parent::getTotalModule($ModuleCode);
		}
		return $Module;
	}
}
