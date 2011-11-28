  <table border="0" width="100%" cellspacing="0" cellpadding="0">
   <tr>
    <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
     <tr>
      <td class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></td>
      <td class="pageHeading" align="right"></td>
     </tr>
    </table></td>
   </tr>
   <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
     <tr>
      <td width="50%" class="main" valign="top"><fieldset>
       <legend><?php echo sysLanguage::get('LEGEND_FROM_DATE');?></legend>
       <div type="text" id="DP_startDate"></div><br>
       <input type="text" name="start_date" id="start_date" value="<?php echo (isset($_GET['start_date']))?$_GET['start_date']:date('Y-m-d');?>">
	      <?php
	      if(isset($_GET['highlightOID'])){
	      ?>
	      <input type="hidden" name="highlight" id="highlight" value="<?php echo $_GET['highlightOID'];?>">
	  <?php
          }
	      ?>
      </fieldset></td>
      <td width="50%" class="main" valign="top"><fieldset>
       <legend><?php echo sysLanguage::get('LEGEND_TO_DATE');?></legend>
       <div type="text" id="DP_endDate"></div><br>
       <input type="text" name="end_date" id="end_date" value="<?php echo (isset($_GET['end_date']))?$_GET['end_date']:date('Y-m-d');?>">
       </fieldset></td>
     </tr>
    </table></td>
   </tr>
   <tr>
    <td align="right">
	    <select name="filter_pay" id="filterPay">
		    <?php
		        if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_PROCESS_SEND') == 'True'){
			?>
		    <option value="pay">Payed Reservations</option>
		    <option value="notpay">Not Payed Reservations</option>
	  <?php
	    }
		    ?>
		    </select>
	    <select name="filter_status" id="filterStatus">
		    <option value="-1">Any Type</option>
		    <?php
		    $QrentalStatus = Doctrine_Query::create()
		    ->from('RentalStatus')
		    ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			foreach($QrentalStatus as $iStatus){
				echo '<option value="'.$iStatus['rental_status_id'].'">'.$iStatus['rental_status_text'].'</option>';
			}
		    ?>
	    </select>
	    <input type="button" value="<?php echo sysLanguage::get('TEXT_BUTTON_GET_RES');?>" name="get_res" id="get_res">

    </td>
   </tr>
	  <tr>
		  <td>
			  <div id="errMsg"></div>
		  </td>
	  </tr>
   <tr>
    <td>

	    <?php echo tep_draw_separator('pixel_trans.gif', '10', '10');?></td>
   </tr>
   <tr>
    <td><table cellpadding="2" cellspacing="0" border="0" width="100%" id="reservationsTable">
     <thead>
      <tr class="dataTableHeadingRow">
       <td class="dataTableHeadingContent" style="text-align:left;"><?php echo sysLanguage::get('TABLE_HEADING_SEND');?></td>
       <td class="dataTableHeadingContent" style="text-align:left;"><?php echo sysLanguage::get('TABLE_HEADING_CUSTOMERS_NAME');?></td>
       <td class="dataTableHeadingContent" style="text-align:left;"><?php echo sysLanguage::get('TABLE_HEADING_PRODUCTS_NAME');?></td>
       <td class="dataTableHeadingContent" style="text-align:left;"><?php echo sysLanguage::get('TABLE_HEADING_BARCODE');?></td>
	   <td class="dataTableHeadingContent" style="text-align:left;"><?php echo sysLanguage::get('TABLE_HEADING_BARCODE_REPLACE');?></td>
	      <?php
	      	if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
	      ?>
      <td class="dataTableHeadingContent" style="text-align:left;"><div id="eventSort" type="ASC"><?php echo sysLanguage::get('TABLE_HEADING_EVENT');?></div></td>
	      <?php
		  if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'True'){
		?>
            <td class="dataTableHeadingContent" style="text-align:left;"><div id="gateSort" type="ASC"><?php echo sysLanguage::get('TABLE_HEADING_GATE');?></div></td>
		  <?php
		  }

      }
	      ?>
	   <td class="dataTableHeadingContent"><?php echo 'Dates';?></td>
	   <td class="dataTableHeadingContent" style="text-align:left;"><?php echo "Location";?></td>
	      <?php
	    if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_TRACKING_NUMBER_COLUMN') == 'True'){
		?>
	   <td class="dataTableHeadingContent"><?php echo 'Tracking Number';?></td>
		    <?php
	    }
?>
	      <?php
	      if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_RESERVATION_STATUS_COLUMN') == 'True'){
	      ?>
	   <td class="dataTableHeadingContent"><?php echo 'Reservation Status';?></td>
	  <?php
		}
	  ?>
	      <?php
		  if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_PROCESS_SEND') == 'True'){
	  ?>
	  <td class="dataTableHeadingContent"><?php echo 'Pay Reservation';?></td>
	  <?php
		}
	  ?>
       <td class="dataTableHeadingContent">View Order</td>
      </tr>
     </thead>
     <tfoot>
      <tr>
       <td colspan="6" align="right">
	       <input type="button" value="<?php echo sysLanguage::get('TEXT_BUTTON_SEND');?>" name="send" id="send">
	       <input type="button" value="<?php echo sysLanguage::get('TEXT_BUTTON_PAY_RES');?>" name="pay_res" id="pay_res">
	       <input type="button" value="<?php echo sysLanguage::get('TEXT_BUTTON_STATUS_RES');?>" name="pay_res" id="status_res">
	       </td>
      </tr>
     </tfoot>
     <tbody>
     </tbody>
    </table></td>
   </tr>
  </table>
<div id="ajaxLoader" title="Ajax Operation">Performing An Ajax Operation<br>Please Wait....</div>