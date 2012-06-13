<?php
require(sysConfig::getDirFsCatalog() . 'includes/classes/html/base/element.php');

/**
 * Static access for building html elements or widgets
 */
class htmlBase {

	/**
	 * Initializes html element/widget based on the element type
	 * @param string $elementType The element type or widget name to use
	 * @param array $options [optional] Configuration settings to send to the element
	 * @return htmlElement
	 * @return htmlWidget
	 */
  	public static function newElement($elementType, $options = array()){
  		$elementClassName = 'htmlElement_' . $elementType;
  		$widgetClassName = 'htmlWidget_' . $elementType;
		$elementDir = sysConfig::getDirFsCatalog() . 'includes/classes/html/elements/';
		$widgetDir = sysConfig::getDirFsCatalog() . 'includes/classes/html/widgets/';

  		if (file_exists($elementDir . $elementType . '.php')){
 	 		if (!class_exists($elementClassName, false)){
 	 			require($elementDir . $elementType . '.php');
	  		}
	  		$element = new $elementClassName($options);
   		}elseif (file_exists($widgetDir . $elementType . '.php')){
 	 		if (!class_exists($widgetClassName, false)){
 	 			require($widgetDir . $elementType . '.php');
	  		}
	  		$element = new $widgetClassName($options);
  		}else{
  			$element = new htmlElement($elementType);
  		}
		return $element->startChain();
  	}

	/**
	 * @static
	 * @return htmlElement_input
	 */
	public static function newInput(){
		return self::newElement('input');
	}

	/**
	 * @static
	 * @return htmlWidget_checkbox
	 */
	public static function newCheckbox(){
		return self::newElement('checkbox');
	}

	/**
	 * @static
	 * @return htmlWidget_radio
	 */
	public static function newRadio(){
		return self::newElement('radio');
	}

	/**
	 * @static
	 * @return htmlElement_selectbox
	 */
	public static function newSelectbox(){
		return self::newElement('selectbox');
	}

	/**
	 * @static
	 * @return htmlWidget_newGrid
	 */
	public static function newGrid(){
		return self::newElement('newGrid');
	}

	/**
	 * @static
	 * @return htmlElement_table
	 */
	public static function newTable(){
		return self::newElement('table');
	}

	/**
	 * @static
	 * @return htmlElement_list
	 */
	public static function newList(){
		return self::newElement('list');
	}
}
?>