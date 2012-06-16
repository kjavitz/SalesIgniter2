<?php
class CheckoutSale extends Order implements Serializable
{

	/**
	 * @var array
	 */
	private $data = array();

	/**
	 * @var CheckoutSaleAddressManager
	 */
	public $AddressManager;

	/**
	 * @var CheckoutSaleProductManager
	 */
	public $ProductManager;

	/**
	 * @var CheckoutSaleTotalManager
	 */
	public $TotalManager;

	/**
	 * @var CheckoutSalePaymentManager
	 */
	public $PaymentManager;

	/**
	 * @var array
	 */
	private $errorMessages = array();

	/**
	 * @var AccountsReceivableModule
	 */
	protected $SaleModule = null;

	protected $SaleModuleId = null;

	protected $SaleModuleRev = null;

	public function __construct($saleType, $saleId = 0, $revision = 0)
	{
		$this->InfoManager = new CheckoutSaleInfoManager();
		$this->AddressManager = new CheckoutSaleAddressManager();
		$this->ProductManager = new CheckoutSaleProductManager();
		$this->TotalManager = new CheckoutSaleTotalManager();
		$this->PaymentManager = new CheckoutSalePaymentManager();

		if (AccountsReceivableModules::loadModule($saleType)){
			$Module = AccountsReceivableModules::getModule($saleType);
			$this->SaleModule = $Module;
			$this->SaleModule->load($this, true, $saleId, $revision);
		}

		if ($saleId == 0){
			$this->InfoManager->setInfo('status', 1);
			$this->InfoManager->setInfo('currency', Session::get('currency'));
			$this->InfoManager->setInfo('currency_value', Session::get('currency_value'));

			$SubTotalModule = $this->TotalManager->getTotal('subtotal');
			if ($SubTotalModule === false){
				$SubTotal = new CheckoutSaleTotal('subtotal', array(
					'sort_order' => 1,
					'value'      => 0
				));

				$this->TotalManager->add($SubTotal);
			}

			$TaxModule = $this->TotalManager->getTotal('tax');
			if ($TaxModule === false){
				$Tax = new CheckoutSaleTotal('tax', array(
					'sort_order' => 2,
					'value'      => 0
				));

				$this->TotalManager->add($Tax);
			}

			$TotalModule = $this->TotalManager->getTotal('total');
			if ($TotalModule === false){
				$Total = new CheckoutSaleTotal('total', array(
					'sort_order' => 3,
					'value'      => 0
				));

				$this->TotalManager->add($Total);
			}
		}

		$this->errorMessages = array();

		EventManager::notify('CheckoutSaleLoadSale', $this);
	}

	/**
	 *
	 */
	public function init()
	{
		$InfoManagerJson = $this->InfoManager;
		$this->InfoManager = new CheckoutSaleInfoManager();
		$this->InfoManager->jsonDecode($InfoManagerJson);

		$AddressManagerJson = $this->AddressManager;
		$this->AddressManager = new CheckoutSaleAddressManager();
		$this->AddressManager->jsonDecode($AddressManagerJson);

		$ProductManagerJson = $this->ProductManager;
		$this->ProductManager = new CheckoutSaleProductManager();
		$this->ProductManager->jsonDecode($ProductManagerJson);

		$TotalManagerJson = $this->TotalManager;
		$this->TotalManager = new CheckoutSaleTotalManager();
		$this->TotalManager->jsonDecode($TotalManagerJson);

		$PaymentManagerJson = $this->PaymentManager;
		$this->PaymentManager = new CheckoutSalePaymentManager();
		$this->PaymentManager->jsonDecode($PaymentManagerJson);

		if (isset($this->SaleModule)){
			$this->SaleModule = AccountsReceivableModules::getModule($this->SaleModule);
			$this->SaleModule->load(
				$this,
				false,
				$this->SaleModuleId,
				$this->SaleModuleRev
			);
		}
	}

	public function hasSaleId()
	{
		return $this->getOrderId() > 0;
	}

	public function hasSaleModule()
	{
		return !($this->SaleModule === null);
	}

	public function getSaleModule()
	{
		return $this->SaleModule;
	}

	/**
	 * @return string
	 */
	public function serialize()
	{
		$data = array(
			'orderId'        => $this->getOrderId(),
			'customerId'     => $this->getCustomerId(),
			'mode'           => $this->mode,
			'Order'          => $this->Order,
			'InfoManager'    => $this->InfoManager->prepareJsonSave(),
			'ProductManager' => $this->ProductManager->prepareJsonSave(),
			'AddressManager' => $this->AddressManager->prepareJsonSave(),
			'TotalManager'   => $this->TotalManager->prepareJsonSave(),
			'PaymentManager' => $this->PaymentManager->prepareJsonSave(),
			'errorMessages'  => $this->errorMessages,
			'data'           => $this->data
		);

		if (is_object($this->SaleModule)){
			$data['SaleModule'] = $this->SaleModule->getCode();
			$data['SaleModuleId'] = $this->SaleModule->getSaleId();
			$data['SaleModuleRev'] = $this->SaleModule->getCurrentRevision();
		}
		return serialize($data);
	}

	/**
	 * @param string $data
	 * @return mixed|string
	 */
	public function unserialize($data)
	{
		$data = unserialize($data);
		foreach($data as $key => $dInfo){
			if (in_array($key, array('InfoManager', 'ProductManager', 'AddressManager', 'TotalManager', 'PaymentManager'))){
				$this->$key = json_encode($dInfo);
			}
			else {
				$this->$key = $dInfo;
			}
		}
		return $data;
	}

	/**
	 * @param string|int $k
	 * @param mixed      $v
	 */
	public function setData($k, $v)
	{
		$this->data[$k] = $v;
	}

	/**
	 * @param string|int $k
	 * @return mixed
	 */
	public function getData($k)
	{
		return $this->data[$k];
	}

	/**
	 * @param string|int $k
	 * @return bool
	 */
	public function hasData($k)
	{
		return (isset($this->data[$k]));
	}

	/**
	 * @param string $val
	 */
	public function addErrorMessage($val)
	{
		$this->errorMessages[] = $val;
	}

	/**
	 * @return bool
	 */
	public function hasErrors()
	{
		global $messageStack;
		return ($messageStack->size('CheckoutSale') > 0);
	}

	/**
	 * @return array
	 */
	public function getErrors()
	{
		global $messageStack;
		return $messageStack->output('CheckoutSale', true);
	}

	public function importUserAccount(RentalStoreUser $userAccount)
	{
	}

	public function importShoppingCart(ShoppingCart $ShoppingCart)
	{
		$toRemove = array();
		foreach($this->ProductManager->getContents() as $SaleProduct){
			$toRemove[$SaleProduct->getId()] = $SaleProduct->getId();
		}

		foreach($ShoppingCart->getContents() as $CartProduct){
			if ($this->ProductManager->cartProductExists($CartProduct->getId())){
				$OrderProduct = $this->ProductManager->getByCartProductHash($CartProduct->getId());
				//echo __FILE__ . '::' . __LINE__ . '<br>';
				$Success = $OrderProduct->updateFromCart($CartProduct);
				if ($Success === false){
					$this->addErrorMessage('There was an error updating a cart product!');
				}
				else {
					$this->TotalManager->onProductUpdated($this->ProductManager);
				}
			}
			else {
				//echo __FILE__ . '::' . __LINE__ . '<br>';
				//echo '<div style="margin-left:15px;">';
				$Success = $this->ProductManager->addFromCart($CartProduct);
				if ($Success === false){
					$this->addErrorMessage('There was an error adding a cart product to the sale!');
				}
				else {
					//echo '</div>';
					//echo __FILE__ . '::' . __LINE__ . '<br>';
					//echo '<div style="margin-left:15px;">';
					$this->TotalManager->onProductAdded($this->ProductManager);
					//echo '</div>';
				}
			}

			if (isset($toRemove[$CartProduct->getId()])){
				unset($toRemove[$CartProduct->getId()]);
			}
		}

		foreach($toRemove as $SaleProductId){
			$this->ProductManager->remove($SaleProductId);
		}
		$this->ProductManager->cleanUp();
	}

	public function createCustomerAccount()
	{
		global $userAccount;
		if ($this->InfoManager->getInfo('createAccount') === true){
			$addressBook =& $userAccount->plugins['addressBook'];
			$customerAddress = $this->AddressManager->getAddress('customer');

			$userAccount->setFirstName($customerAddress->getFirstName());
			$userAccount->setLastName($customerAddress->getLastName());
			$userAccount->setEmailAddress($this->InfoManager->getInfo('customers_email_address'));
			$userAccount->setFaxNumber($this->InfoManager->getInfo('customers_fax_number'));
			$userAccount->setTelephoneNumber($this->InfoManager->getInfo('customers_telephone_number'));
			$userAccount->setPassword($this->InfoManager->getInfo('customers_password'));
			$userAccount->setGender($customerAddress->getGender());
			$userAccount->setDateOfBirth($customerAddress->getDateOfBirth());
			$userAccount->setCityBirth($customerAddress->getCityBirth());
			$userAccount->setNewsletter($this->InfoManager->getInfo('customers_newsletter'));
			$userAccount->setLanguageId(Session::get('languages_id'));
			$customerId = $userAccount->createNewAccount();

			$defaultId = $addressBook->insertAddress($this->AddressManager->getAddress('billing')->toArray());
			$isShipping = true;
			if (isset($_POST['shipping_diff'])){
				$shippingId = $addressBook->insertAddress($this->AddressManager->getAddress('delivery')->toArray());
				$isShipping = false;
			}

			if (isset($_POST['pickup_diff'])){
				$addressBook->insertAddress($this->AddressManager->getAddress('pickup')->toArray(), $isShipping);
			}

			$addressBook->setDefaultAddress($defaultId, true);

			if (isset($shippingId)){
				$addressBook->setDeliveryDefaultAddress($shippingId, true);
			}

			//if ($this->isMembershipCheckout() === true){
			//	$userAccount->plugins['membership']->setRentalAddress($defaultId);
			//}

			EventManager::notify('CheckoutAddNewCustomer', $customerId);
		}
		else {
			if ($userAccount->isLoggedIn() === false){
				/* Confusing, i know. it's for anonymous accounts */
				$userAccount->processLogOut();
				$this->__destruct();
			}
		}
	}
}

require(dirname(__FILE__) . '/InfoManager/Base.php');
require(dirname(__FILE__) . '/AddressManager/Base.php');
require(dirname(__FILE__) . '/ProductManager/Base.php');
require(dirname(__FILE__) . '/TotalManager/Base.php');
require(dirname(__FILE__) . '/PaymentManager/Base.php');
