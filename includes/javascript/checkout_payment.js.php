<script language="javascript" type="text/javascript"><!--
var selected;
<?php // #################### Added CGV ###################### ?>
var submitter = null;
function submitFunction() {
   submitter = 1;
   }
<?php // #################### End Added CGV ###################### ?>

function CVVPopUpWindow(url) {
	window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,copyhistory=no,width=600,height=233,screenX=150,screenY=150,top=150,left=150')
}

function CVVPopUpWindowEx(url) {
	window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,copyhistory=no,width=600,height=510,screenX=150,screenY=150,top=150,left=150')
}

function selectRowEffect(object, buttonSelect) {

  // #################### Begin Added CGV JONYO ######################
  if (!document.checkout_payment.payment[0].disabled){
  // #################### End Added CGV JONYO ######################
    if (!selected) {

    if (document.getElementById) {

      selected = document.getElementById('defaultSelected');

    } else {

      selected = document.all['defaultSelected'];

    }

  }



  if (selected) selected.className = 'moduleRow';

  object.className = 'moduleRowSelected';

  selected = object;



// one button is not an array

  if (document.checkout_payment.payment[0]) {

    document.checkout_payment.payment[buttonSelect].checked=true;

  } else {

    document.checkout_payment.payment.checked=true;

  }
  // #################### Begin Added CGV JONYO ######################
  }
  // #################### End Added CGV JONYO ######################
}



function rowOverEffect(object) {

  if (object.className == 'moduleRow') object.className = 'moduleRowOver';

}



function rowOutEffect(object) {

  if (object.className == 'moduleRowOver') object.className = 'moduleRow';

}

<?php // #################### Begin Added CGV JONYO ###################### ?>

<?php
if (MODULE_ORDER_TOTAL_INSTALLED)
	$temp=$order_total_modules->process();
	$temp=$temp[count($temp)-1];
	$temp=$temp['value'];

	$gvResult = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select amount from " . TABLE_COUPON_GV_CUSTOMER . " where customer_id = '" . $customer_id . "'");

if ($gvResult[0]['amount']>=$temp){ $coversAll=true;

?>

function clearRadeos(){
document.checkout_payment.cot_gv.checked=!document.checkout_payment.cot_gv.checked;
for (counter = 0; counter < document.checkout_payment.payment.length; counter++)
{
// If a radio button has been selected it will return true
// (If not it will return false)
if (document.checkout_payment.cot_gv.checked){
document.checkout_payment.payment[counter].checked = false;
document.checkout_payment.payment[counter].disabled=true;
//document.checkout_payment.cot_gv.checked=false;
} else {
document.checkout_payment.payment[counter].disabled=false;
//document.checkout_payment.cot_gv.checked=true;
}
}
}<? } else { $coversAll=false;?>
function clearRadeos(){
document.checkout_payment.cot_gv.checked=!document.checkout_payment.cot_gv.checked;
}<? } ?>
<?php // #################### End Added CGV JONYO ###################### ?>

//--></script>

<?php // #################### Begin Added CGV JONYO ###################### ?>
<?php // echo $payment_modules->javascript_validation(); ?>
<?php echo $payment_modules->javascript_validation($coversAll); ?>
<?php // #################### End Added CGV JONYO ###################### ?>

