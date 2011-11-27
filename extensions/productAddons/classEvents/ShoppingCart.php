<?php
	class ShoppingCart_productAddons {
		
		public function __construct(){
		}
		
		public function init(){
			
			EventManager::attachEvents(array(
				'AddToCartAfterAction',
				'AddToCartAllow'
			), 'ShoppingCart', $this);
		}

		public function AddToCartAllow($cartData, $Product){
			return true;
		}

		public function AddToCartAfterAction(ShoppingCartProduct &$cartProduct){
			global $messageStack, $ShoppingCart;
			$v = $_POST['addon_product'];
			unset($_POST['addon_product']);
			foreach($v as $addon => $val){
				$purchaseTypeCode = $_POST['addon_product_type'][$addon];
				$ShoppingCart->add($addon);

			}
		}
	}
?>