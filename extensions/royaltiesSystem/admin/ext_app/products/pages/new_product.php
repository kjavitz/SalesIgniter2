<?php
/*
	Royalties System Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class royaltiesSystem_admin_products_new_product extends Extension_royaltiesSystem
{

	public function __construct() {
		parent::__construct();
	}

	public function load() {
		if ($this->enabled === false){
			return;
		}

		EventManager::attachEvents(array(
				'NewProductStreamingTableAddHeaderCol',
				'NewProductStreamingTableAddBodyCol',
				'NewProductStreamingTableAddInputRow',
				'NewProductDownloadsTableAddHeaderCol',
				'NewProductDownloadsTableAddBodyCol',
				'NewProductDownloadsTableAddInputRow',
				'NewProductPricingTabBottom',
				'NewProductPricingTabsComplete'
			), null, $this);
	}

	public function exemptedPurchaseTypes() {
		return array('stream', 'download');
	}

	public function NewProductStreamingTableAddBodyCol($sInfo, &$BodyColumns) {
		$Cselectbox = htmlBase::newElement('selectbox')
			->hide()
			->setName('stream_content_provider_id[' . $sInfo['stream_id'] . ']')
			->selectOptionByValue($sInfo['content_provider_id']);

		$Cselectbox->addOption('0', sysLanguage::get('TEXT_PLEASE_SELECT'), false);

		$providerName = '';
		$Qproviders = Doctrine_Query::create()
			->select('customers_id, CONCAT(customers_firstname," ", customers_lastname) as customers_name')
			->from('Customers')
			->where('is_content_provider = ?', '1')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qproviders){
			foreach($Qproviders as $pInfo){
				$Cselectbox->addOption(
					$pInfo['customers_id'],
					$pInfo['customers_name']
				);

				if ($pInfo['customers_id'] == $sInfo['content_provider_id']){
					$providerName = $pInfo['customers_firstname'] . ' ' . $pInfo['customers_lastname'];
				}
			}
		}

		$royaltyFeeInput = htmlBase::newElement('input')
			->hide()
			->attr('size', 6)
			->setName('stream_royalty_fee[' . $sInfo['stream_id'] . ']')
			->val($sInfo['royalty_fee']);

		$BodyColumns[] = array(
			'text' => '<span class="streamInfoText">' . $providerName . '</span>' . $Cselectbox->draw()
		);

		$BodyColumns[] = array(
			'text' => '<span class="streamInfoText">' . $sInfo['royalty_fee'] . '</span>' . $royaltyFeeInput->draw()
		);
	}

	public function NewProductStreamingTableAddHeaderCol(&$headerColumns) {
		$headerColumns[] = array(
			'text' => 'Stream Owner'
		);
		$headerColumns[] = array(
			'text' => 'Royalty Fee'
		);
	}

	public function NewProductStreamingTableAddInputRow(&$inputRow) {
		$Cselectbox = htmlBase::newElement('selectbox')
			->setName('new_stream_content_provider_id');

		$Cselectbox->addOption('0', sysLanguage::get('TEXT_PLEASE_SELECT'), false);

		$Qproviders = Doctrine_Query::create()
			->select('customers_id, CONCAT(customers_firstname," ", customers_lastname) as customers_name')
			->from('Customers')
			->where('is_content_provider = ?', '1')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qproviders){
			foreach($Qproviders as $pInfo){
				$Cselectbox->addOption(
					$pInfo['customers_id'],
					$pInfo['customers_name']
				);
			}
		}

		$royaltyFeeInput = htmlBase::newElement('input')
			->setName('new_stream_royalty_fee')
			->attr('size', 6);

		$inputRow[] = array(
			'text' => $Cselectbox->draw()
		);

		$inputRow[] = array(
			'text' => $royaltyFeeInput->draw()
		);
	}

	public function NewProductDownloadsTableAddBodyCol($dInfo, &$BodyColumns) {
		$Cselectbox = htmlBase::newElement('selectbox')
			->hide()
			->setName('download_content_provider_id[' . $dInfo['download_id'] . ']')
			->selectOptionByValue($dInfo['content_provider_id']);

		$Cselectbox->addOption('0', sysLanguage::get('TEXT_PLEASE_SELECT'), false);

		$providerName = '';
		$Qproviders = Doctrine_Query::create()
			->select('customers_id, CONCAT(customers_firstname," ", customers_lastname) as customers_name')
			->from('Customers')
			->where('is_content_provider = ?', '1')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qproviders){
			foreach($Qproviders as $pInfo){
				$Cselectbox->addOption(
					$pInfo['customers_id'],
					$pInfo['customers_name']
				);

				if ($pInfo['customers_id'] == $dInfo['content_provider_id']){
					$Cselectbox->selectOptionByValue($dInfo['content_provider_id']);
					$providerName = $pInfo['customers_name'];
				}
			}
		}

		$royaltyFeeInput = htmlBase::newElement('input')
			->hide()
			->attr('size', 6)
			->setName('download_royalty_fee[' . $dInfo['download_id'] . ']')
			->val($dInfo['royalty_fee']);

		$BodyColumns[] = array(
			'text' => '<span class="downloadInfoText">' . $providerName . '</span>' . $Cselectbox->draw()
		);

		$BodyColumns[] = array(
			'text' => '<span class="downloadInfoText">' . $dInfo['royalty_fee'] . '</span>' . $royaltyFeeInput->draw()
		);
	}

	public function NewProductDownloadsTableAddHeaderCol(&$headerColumns) {
		$headerColumns[] = array(
			'text' => 'Download Owner'
		);
		$headerColumns[] = array(
			'text' => 'Royalty Fee'
		);
	}

	public function NewProductDownloadsTableAddInputRow(&$inputRow) {
		$Cselectbox = htmlBase::newElement('selectbox')
			->setName('new_download_content_provider_id');

		$Cselectbox->addOption('0', sysLanguage::get('TEXT_PLEASE_SELECT'), false);

		$Qproviders = Doctrine_Query::create()
			->select('customers_id, CONCAT(customers_firstname," ", customers_lastname) as customers_name')
			->from('Customers')
			->where('is_content_provider = ?', '1')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qproviders){
			foreach($Qproviders as $pInfo){
				$Cselectbox->addOption(
					$pInfo['customers_id'],
					$pInfo['customers_name']
				);
			}
		}

		$royaltyFeeInput = htmlBase::newElement('input')
			->setName('new_download_royalty_fee')
			->attr('size', 6);

		$inputRow[] = array(
			'text' => $Cselectbox->draw()
		);

		$inputRow[] = array(
			'text' => $royaltyFeeInput->draw()
		);
	}

	public function NewProductPricingTabBottom($tInfo, Product &$Product, &$inputTable, PurchaseTypeBase &$PurchaseType) {
		if ($PurchaseType->getConfigData('ROYALTIES_SYSTEM_ENABLED') == 'True'){
			if (in_array($PurchaseType->getCode(), $this->exemptedPurchaseTypes())){
				return false;
			}
			if ($Product->getId() > 0){
				$ProductsRoyaltiesTable = Doctrine_Core::getTable('RoyaltiesSystemProductsRoyalties');
				$ProductsRoyalties = $ProductsRoyaltiesTable->findOneByProductsIdAndPurchaseType($Product->getId(), $PurchaseType->getCode());
			}
			$inputName = 'pricing[' . $typeName . '][' . $tInfo['id'] . '][royalties]';

			$Cselectbox = htmlBase::newElement('selectbox')
				->setName($inputName . '[content_provider]');

			$Cselectbox->addOption('0', sysLanguage::get('TEXT_PLEASE_SELECT'), false);

			$Qproviders = Doctrine_Query::create()
				->select('customers_id, CONCAT(customers_firstname," ", customers_lastname) as customers_name')
				->from('Customers')
				->where('is_content_provider = ?', '1')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if ($Qproviders){
				foreach($Qproviders as $pInfo){
					$Cselectbox->addOption(
						$pInfo['customers_id'],
						$pInfo['customers_name']
					);
				}
			}
			$Cselectbox->selectOptionByValue($ProductsRoyalties->content_provider_id);

			$royaltyFeeInput = htmlBase::newElement('input')
				->setName($inputName . '[royalty_fee]')
				->attr('size', 6)
				->val($ProductsRoyalties->royalty_fee);

			$inputTable->addBodyRow(array(
					'columns' => array(
						array('text' => sysLanguage::get('TEXT_CONTENT_PROVIDER')),
						array('text' => $Cselectbox->draw())
					)
				));
			$inputTable->addBodyRow(array(
					'columns' => array(
						array('text' => sysLanguage::get('TEXT_ROYALTY')),
						array('text' => $royaltyFeeInput->draw())
					)
				));
		}
	}
}

?>