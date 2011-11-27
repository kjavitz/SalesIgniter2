<?php
class productInventoryNormal_barcode {
	public function __construct($invData){
		$this->invData = $invData;
		$this->invUnavailableStatus = array(
			'B', //Broken
			'O', //Out
			'P', //Purchased
			'R', //Reserved
			'T',  //In Transfer,
			'M',  //Maintenance
			'Q'  //Quarantine
		);
	}

	public function setInvUnavailableStatus($val){
		$this->invUnavailableStatus = $val;
	}

	public function getInvUnavailableStatus(){
		return $this->invUnavailableStatus;
	}

	public function hasInventory(){
		$Qcheck = Doctrine_Query::create()
		->select('count(ib.barcode_id) as total')
		->from('ProductsInventoryBarcodes ib')
		->where('ib.inventory_id = ?', $this->invData['inventory_id'])
		->andWhereNotIn('ib.status', $this->invUnavailableStatus);

		EventManager::notify('ProductInventoryBarcodeHasInventoryQueryBeforeExecute', $this->invData, &$Qcheck);
		
		$Result = $Qcheck->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		return ($Result[0]['total'] > 0);
	}

	public function getInventoryItemCount(){
		$count = 0;

		$invItems = $this->getInventoryItems();
		foreach($invItems as $invItem){
			$addTotal = true;

			EventManager::notify('ProductInventoryBarcodeGetItemCount', &$this->invData, &$invItem, &$addTotal);

			if ($addTotal === true){
				$count++;
			}
		}
		return $count;
	}
	
	public function addStockToCollection(&$Product, &$CollectionObj){
		if ($this->invData['type'] == 'new' || $this->invData['type'] == 'used' || $this->invData['type'] == 'reservation'){
			$Qcheck = Doctrine_Query::create()
			->select('ib.barcode_id')
			->from('ProductsInventoryBarcodes ib')
			->where('ib.status = ?', 'A')
			->andWhere('ib.inventory_id = ?', $this->invData['inventory_id'])
			->limit('1');
			
			EventManager::notify('ProductInventoryBarcodeUpdateStockQueryBeforeExecute', $this->invData, &$Qcheck);
			
			$Result = $Qcheck->execute();
			if ($Result){
				$CollectionObj->barcode_id = (int) $Result[0]->barcode_id;
				$CollectionObj->ProductsInventoryBarcodes->status = 'P';
			}
		}
	}

	public function updateStock($orderId, $orderProductId, $cartProduct){
		if ($this->invData['type'] == 'new' || $this->invData['type'] == 'used' || $this->invData['type'] == 'reservation'){
			$Qcheck = Doctrine_Query::create()
			->select('ib.barcode_id')
			->from('ProductsInventoryBarcodes ib')
			->where('ib.status = ?', 'A')
			->andWhere('ib.inventory_id = ?', $this->invData['inventory_id'])
			->limit('1');
			
			EventManager::notify('ProductInventoryBarcodeUpdateStockQueryBeforeExecute', $this->invData, &$Qcheck);
			
			$Result = $Qcheck->execute();
			if ($Result){
				$Result[0]->status = 'P';
				$Result[0]->save();
				Doctrine_Query::create()
				->update('OrdersProducts')
				->set('barcode_id', '?', (int)$Result[0]->barcode_id)
				->where('orders_products_id = ?', (int)$orderProductId)
				->execute();
			}
		}
	}
	
	public function getNextInventoryItemId(){
		$QinvItem = Doctrine_Query::create()
			->select('ib.barcode_id')
			->from('ProductsInventoryBarcodes ib')
			->where('ib.status = ?', 'A')
			->andWhere('ib.inventory_id = ?', $this->invData['inventory_id'])
			->limit('1');
			
		EventManager::notify('ProductInventoryBarcodeGetNextInventoryItemIdQueryBeforeExecute', $this->invData, &$QinvItem);
			
		$Result = $QinvItem->execute()->toArray();
		return $Result[0]['barcode_id'];
	}

	public function getInventoryItems(){
		$barcodes = array();
		$Qcheck = Doctrine_Query::create()
		->from('ProductsInventoryBarcodes ib')
		->where('ib.inventory_id = ?', $this->invData['inventory_id'])
		->andWhereNotIn('ib.status', $this->invUnavailableStatus);
		
		EventManager::notify('ProductInventoryBarcodeGetInventoryItemsQueryBeforeExecute', $this->invData, $Qcheck);
		
		$Result = $Qcheck->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Result){
			foreach($Result as $i => $barcode){
				$barcodes[$i] = array(
					'id'           => $barcode['barcode_id'],
					'barcode'      => $barcode['barcode'],
					'inventory_id' => $barcode['inventory_id'],
					'status'       => $barcode['status']
				);
				
				EventManager::notify('ProductInventoryBarcodeGetInventoryItemsArrayPopulate', $barcode, &$barcodes[$i]);
			}
		}
		return $barcodes;
	}
}
?>