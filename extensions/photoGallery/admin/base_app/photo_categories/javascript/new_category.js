
function uploadManagerField($el){
	if ($el.attr('type') == 'file'){
		alert($el.attr('name') + ' cannot be an upload manager field because it is not a text input field.');
		return;
	}

	var isMulti = false;
	var autoUpload = true;
	var hasPreviewContainer = false;
	var debug = false;
	var $debugger = $('#' + $el.attr('id') + '_uploadDebugOutput').addClass('uploadDebugger');
	if (debug === true){
		$debugger.show();
	}

	if ($el.attr('data-is_multi')){
		isMulti = ($el.attr('data-is_multi') == 'true');
	}

	if ($el.attr('data-has_preview')){
		hasPreviewContainer = true;
		var $previewContainer = $('#' + $el.attr('id') + '_previewContainer');
	}

	if ($el.attr('data-auto_upload')){
		autoUpload = ($el.attr('data-auto_upload') == 'true');
	}

	var fileType = $el.attr('data-file_type');
	$el.uploadify({
		uploader: DIR_WS_CATALOG + 'ext/jQuery/external/uploadify/uploadify.swf',
		script: 'application.php',
		method: 'GET',
		multi: isMulti,
		scriptData: {
			'appExt': 'photoGallery',
			'app': 'photo_categories',
			'appPage': 'new_category',
			'action': 'uploadFile',
			'rType': 'ajax',
			'osCAdminID': sessionId,
			'fileType': fileType
		},
		cancelImg: DIR_WS_CATALOG + 'ext/jQuery/external/uploadify/images/cancel.png',
		auto: autoUpload,
		onError: function (event, queueID, fileObj, errorObj){
			var curVal = $debugger.val();
			$debugger.val(curVal + "\nError Uploading: " + errorObj.type + " :: " + errorObj.info);
		},
		onAllComplete: function (){
			var curVal = $debugger.val();
			$debugger.val(curVal + "\nAll Uploads Completed!");
		},
		onOpen: function (event, queueID, fileObj){
			var curVal = $debugger.val();
			$debugger.val(curVal + "\nBeginning Upload: " + fileObj.name);
		},
		onProgress: function (event, queueID, fileObj, data){
			var curVal = $debugger.val();
			$debugger.val(curVal + "\nUpload Speed: " + data.speed + ' KB/ps');
		},
		onComplete: function (event, queueID, fileObj, resp, data){
			var curVal = $debugger.val();
			$debugger.val(curVal + "\nUpload Completed\nJson Response: " + resp);

			var theResp = eval('(' + resp + ')');

			if (theResp.success == true){
				if (isMulti){
					if ($el.val() != ''){
						$el.val($el.val() + ';' + theResp.image_name);
					}else{
						$el.val(theResp.image_name);
					}
				}else{
					$el.val(theResp.image_name);
				}

				if (hasPreviewContainer === true){
					var $deleteIcon = $('<a></a>')
						.addClass('ui-icon ui-icon-closethick');

					var $zoomIcon = $('<a></a>')
						.addClass('ui-icon ui-icon-zoomin');

					var $fancyBox = $('<a></a>')
						.addClass('fancyBox')
						.attr('href', theResp.image_path);

					var $img = $('<img></img>')
						.attr('src', theResp.thumb_path)
						.appendTo($fancyBox);

					var $thumbHolder = $('<div></div>')
						.css('text-align', 'center')
						.append($fancyBox)
						.append($zoomIcon)
						.append($deleteIcon);

					var $caption = $('<input type="text" name="caption_'+theResp.image_name.replace('.', '_')+'"/>');
					var $desc = $('<textarea rows="8" cols="5" name="desc_'+theResp.image_name.replace('.', '_')+'"></textarea>');
					var $capTitle = $('<span>Title:</span>');
					var $capDesc = $('<span>Description:</span>');
					var $theBox = $('<div>').css({
						'float'  : 'left',
						'width'  : '80px',
						'height' : '100px',
						'border' : '1px solid #cccccc',
						'margin' : '.5em'
					}).append($thumbHolder).append($capTitle).append($caption).append($capDesc).append($desc);

					if (isMulti){
						$previewContainer.append($theBox);
					}else{
						$previewContainer.html($theBox);
					}
					$('.fancyBox', $theBox).trigger('loadBox');
				}
			}
		}
	});
}

$(document).ready(function (){

	$('#page-2').tabs();
	$('#tab_container').tabs();
	$('.makeFCK').each(function (){
		if ($(this).is(':hidden')) return;

		CKEDITOR.replace(this, {
			filebrowserBrowseUrl: DIR_WS_ADMIN + 'rentalwysiwyg/editor/filemanager/browser/default/browser.php'
		});
	});
	$('.ajaxUpload, .ajaxUploadMulti, .uploadManagerInput').each(function (){
		uploadManagerField($(this));
	});
	$('.fancyBox').live('loadBox', function (){
		$(this).fancybox({
			speedIn: 500,
			speedOut: 500,
			overlayShow: false,
			type: 'image'
		});
	}).trigger('loadBox');

	$('.ui-icon-zoomin').live('click mouseover mouseout', function (event){
		switch(event.type){
			case 'click':
				$(this).parent().find('.fancyBox').click();
				break;
			case 'mouseover':
				this.style.cursor = 'pointer';
				//$(this).addClass('ui-state-hover');
				break;
			case 'mouseout':
				this.style.cursor = 'default';
				//$(this).removeClass('ui-state-hover');
				break;
		}
	});

	$('.deleteImage').live('click', function (event){
		var newVal = [];
		var imageInput = $('#' + $(this).parent().attr('data-input_id'));
		var currentVal = imageInput.val();
		var images = currentVal.split(';');
		for(var i=0; i<images.length; i++){
			if (images[i] != $(this).parent().attr('data-image_file_name')){
				newVal.push(images[i]);
			}
		}
		imageInput.val(newVal.join(';'));
		$(this).parent().parent().remove();
	});
});