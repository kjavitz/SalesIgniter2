<?php
	$Module = OrderShippingModules::getModule($_GET['module']);
	$ModuleMethods = $Module->getMethods();
	
	$Methods = Doctrine_Core::getTable('ModulesShippingUpsReservationMethods');

	$saveArray = array();
	if (isset($_POST['method'])){
		foreach($_POST['method'] as $methodId => $mInfo){
			$Method = $Methods->find($methodId);
			if (!$Method){
				$Method = $Methods->create();
			}
			$Description = $Method->ModulesShippingUpsReservationMethodsDescription;
			foreach($mInfo['text'] as $langId => $Name){
				if (isset($Name) && !empty($Name)){
					$Description[$langId]->language_id = $langId;
					$Description[$langId]->method_text = $Name;
				}
			}

			$Method->method_status = $mInfo['status'];
			$Method->method_days_before = $mInfo['days_before'];
			$Method->method_days_after = $mInfo['days_after'];
			$Method->method_markup = $mInfo['markup'];
			$Method->method_upscode = $mInfo['upscode'];
			$Method->method_default = (isset($_POST['method_default']) && $_POST['method_default'] == $methodId ? '1' : '0');
			$Method->sort_order = $mInfo['sort_order'];
			
			$Method->save();
			
			$saveArray[] = $Method->method_id;
		}
	}
	
	if (!empty($saveArray)){
		Doctrine_Query::create()
		->delete('ModulesShippingUpsReservationMethods')
		->whereNotIn('method_id', $saveArray)
		->execute();
	}
	
?>