<?php
/**
 * Address class for the order creator address manager
 *
 * @package    OrderCreator\AddressManager
 * @author     Stephen Walker <stephen@itwebexperts.com>
 * @since      2.0
 * @copyright  2012 I.T. Web Experts
 * @license    http://itwebexperts.com/license/ses-license.php
 */

class OrderCreatorAddress extends OrderAddress
{

	public function loadSessionData($data){
		$this->Type = $data['Type'];
		$this->updateFromArray($data['addressInfo']);
	}

	/**
	 * @param string $val
	 */
	public function setName($val)
	{
		$this->addressInfo['entry_name'] = (string)$val;
	}

	/**
	 * @param string $val
	 */
	public function setCompany($val)
	{
		$this->addressInfo['entry_company'] = (string)$val;
	}

	/**
	 * @param string $val
	 */
	public function setCityBirth($val)
	{
		$this->addressInfo['entry_city_birth'] = (string)$val;
	}

	/**
	 * @param string $val
	 */
	public function setVATNumber($val)
	{
		$this->addressInfo['entry_vat'] = (string)$val;
	}

	/**
	 * @param string $val
	 */
	public function setFiscalCode($val)
	{
		$this->addressInfo['entry_cif'] = (string)$val;
	}

	/**
	 * @param string $val
	 */
	public function setDOB($val)
	{
		$this->addressInfo['entry_dob'] = (string)$val;
	}

	/**
	 * @param string $val
	 */
	public function setStreetAddress($val)
	{
		$this->addressInfo['entry_street_address'] = (string)$val;
	}

	/**
	 * @param string $val
	 */
	public function setSuburb($val)
	{
		$this->addressInfo['entry_suburb'] = (string)$val;
	}

	/**
	 * @param string $val
	 */
	public function setCity($val)
	{
		$this->addressInfo['entry_city'] = (string)$val;
	}

	/**
	 * @param string $val
	 */
	public function setPostcode($val)
	{
		$this->addressInfo['entry_postcode'] = (string)$val;
	}

	/**
	 * @param string $val
	 */
	public function setState($val)
	{
		$this->addressInfo['entry_state'] = (string)$val;

		$Qcheck = Doctrine_Query::create()
			->from('Zones')
			->where('zone_name = ?', $val)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qcheck){
			$this->Zone = $Qcheck[0];
		}
	}

	/**
	 * @param string $val
	 */
	public function setCountry($val)
	{
		$this->addressInfo['entry_country'] = (string)$val;

		$Qcheck = Doctrine_Query::create()
			->from('Countries c')
			->leftJoin('c.AddressFormat')
			->where('c.countries_name = ?', $val)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qcheck){
			$this->Country = $Qcheck[0];
			$this->addressInfo['entry_country_id'] = $Qcheck[0]['countries_id'];
			$this->Format = $Qcheck[0]['AddressFormat'];
		}
	}
}

?>