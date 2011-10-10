function showAjaxLoader(){
	$('#ajaxLoader').dialog({
		modal: true,
		resizable: false,
		draggable: false,
		position: 'center'
	}).show();
}

function removeAjaxLoader(){
	$('#ajaxLoader').dialog('close');
}
function updateRes(valType){
	var dataArr = new Object();
	dataArr.start_date = $('#start_date').val();
	dataArr.end_date = $('#end_date').val();
	dataArr.filter_status = $('#filterStatus').val();
	dataArr.filter_pay = $('#filterPay').val();
	if(valType == 'e'){
		if($('#eventSort').length){
			dataArr.eventSort = $('#eventSort').attr('type');
		}
	}
	if(valType == 'g'){
		if($('#gateSort').length){
			dataArr.gateSort = $('#gateSort').attr('type');
		}
	}
	$.ajax({
		cache: false,
		dataType: 'html',
		data: dataArr,
		beforeSend: showAjaxLoader,
		complete: removeAjaxLoader,
		url: js_app_link('appExt=payPerRentals&app=send&appPage=default&action=getReservations'),
		success: function (data){
			$('tbody', $('#reservationsTable')).html(data);
			$('.barcodeReplacement').keyup(function() {

				var link = js_app_link('appExt=payPerRentals&app=send&appPage=default&action=getBarcodes');
				var $barInput = $(this);
				$(this).autocomplete({
					source: function(request, response) {
						$.ajax({
							url: link,
							data: 'resid='+$barInput.attr('resid')+'&term='+request.term,
							dataType: 'json',
							type: 'POST',
							success: function(data){
								response(data);
							}
						});
					},
					minLength: 0,
					select: function(event, ui) {
						$barInput.val(ui.item.label);
						$barInput.attr('barid', ui.item.value);
						return false;
					}
				});
			});

			$('.barcodeReplacement').focus(function(){
				if($(this).val() == ''){
					$(this).keyup().autocomplete("search", "");
				}
			});
		}
	});
}

function submitRes(){

	$.ajax({
		cache: false,
		dataType: 'json',
		data: $('#reservationsTable *').serialize(),
        type:'post',
		beforeSend: showAjaxLoader,
		complete: removeAjaxLoader,
		url: js_app_link('appExt=payPerRentals&app=send&appPage=default&action=sendReservations'),
		success: function (data){
			$('.reservations', $('#reservationsTable')).each(function (){
				if (this.checked){
					$(this).parent().parent().remove();
				}
			});
		}
	});
}

function payRes(){

	$.ajax({
		cache: false,
		dataType: 'json',
		data: $('#reservationsTable *').serialize(),
		type:'post',
		beforeSend: showAjaxLoader,
		complete: removeAjaxLoader,
		url: js_app_link('appExt=payPerRentals&app=send&appPage=default&action=payReservations'),
		success: function (data){
			$('#errMsg').html(data.errMsg);
			updateRes();
		}
	});
}


function statusRes(){

	$.ajax({
		cache: false,
		dataType: 'json',
		data: $('#reservationsTable *').serialize(),
		type:'post',
		beforeSend: showAjaxLoader,
		complete: removeAjaxLoader,
		url: js_app_link('appExt=payPerRentals&app=send&appPage=default&action=statusReservations'),
		success: function (data){
			updateRes();
		}
	});
}


var dayShortNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

$(document).ready(function (){
	updateRes();
	$('#get_res').click(updateRes);
	$('#pay_res').click(payRes);
	$('#status_res').click(statusRes);
	$('#send').click(submitRes);

	$('#DP_startDate').datepicker({
		dateFormat: 'yy-mm-dd',
		gotoCurrent: true,
		altField: '#start_date',
		dayNamesMin: dayShortNames
	});

	$('#DP_endDate').datepicker({
		dateFormat: 'yy-mm-dd',
		gotoCurrent: true,
		altField: '#end_date',
		dayNamesMin: dayShortNames
	});

	$('#eventSort').css({'cursor':'pointer'});
	$('#gateSort').css({'cursor':'pointer'});

	$('#eventSort').click(function(){
		updateRes('e');
		if($(this).attr('type') == 'ASC'){
			$(this).attr('type','DESC');
		}else{
			$(this).attr('type','ASC');
		}
	});
	$('#gateSort').click(function(){
		updateRes('g');
		if($(this).attr('type') == 'ASC'){
			$(this).attr('type','DESC');
		}else{
			$(this).attr('type','ASC');
		}
	});


});