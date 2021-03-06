(function( $, undefined ) {

function trident2(){ }
	
trident2.image = function (){
	this.settings = '<table cellpadding="0" cellspacing="0" border="0" style="margin:.5em;">' +
		'<tr>' +
			'<td valign="top"><table cellpadding="2" cellspacing="0" border="0" style="margin:0;">' +
				'<tr>' +
					'<td>Fallback Color: </td>' +
					'<td><input class="makeColorPicker" type="text" name="background_color"></td>' +
				'</tr>' +
				'<tr>' +
					'<td>Image: </td>' +
					'<td><input type="text" name="background_image" class="BrowseServerField" currentFolder=""></td>' +
				'</tr>' +
				'<tr>' +
					'<td>Repeat: </td>' +
					'<td><select name="background_repeat">' + 
						'<option value="no-repeat">No Repeat</option>' + 
						'<option value="repeat">Tile</option>' + 
						'<option value="repeat-x">Repeat Horizontal</option>' + 
						'<option value="repeat-y">Repeat Vertical</option>' + 
					'</select></td>' +
				'</tr>' +
			'</table></td>' +
			'<td valign="top"><table cellpadding="2" cellspacing="0" border="0" style="margin:0;margin-left:2em;">' +
				'<tr>' +
					'<td valign="top">Position Horizontal: </td>' +
					'<td width="150">' + 
						'<input type="text" name="background_position_x" class="percentSliderVal" size="3" maxlength="3">%&nbsp;&nbsp;' + 
						'<div class="backgroundPositionX"></div>' + 
					'</td>' +
				'</tr>' +
				'<tr>' +
					'<td valign="top">Position Vertical: </td>' +
					'<td width="150">' + 
						'<input type="text" name="background_position_y" class="percentSliderVal" size="3" maxlength="3">%&nbsp;&nbsp;' + 
						'<div class="backgroundPositionY"></div>' + 
					'</td>' +
				'</tr>' +
			'</table></td>' +
		'</tr>' +
	'</table>';
	
	this.showSettings = function (){
		return this.settings;
	};
}

trident2.solid = function (){
	this.settings = '<table cellpadding="0" cellspacing="0" border="0" style="margin:.5em;width:200px;">' +
		'<tr>' +
			'<td>Color: </td>' +
			'<td colspan="2" align="center"><span class="makeColorPicker_RGBA"></span></td>' +
		'</tr>' +
		'<tr>' +
			'<td>Red</td>' +
			'<td>( 0 - 255 )</td>' +
			'<td><input type="text" name="background_r" size="3" maxlength="3" value="255"></td>' +
		'</tr>' +
		'<tr>' +
			'<td>Green</td>' +
			'<td>( 0 - 255 )</td>' +
			'<td><input type="text" name="background_g" size="3" maxlength="3" value="255"></td>' +
		'</tr>' +
		'<tr>' +
			'<td>Blue</td>' +
			'<td>( 0 - 255 )</td>' +
			'<td><input type="text" name="background_b" size="3" maxlength="3" value="255"></td>' +
		'</tr>' +
		'<tr>' +
			'<td>Alpha</td>' +
			'<td>( 0 - 100 )</td>' +
			'<td><input type="text" name="background_a" size="3" maxlength="3" value="100">%</td>' +
		'</tr>' +
	'</table>';
	
	this.showSettings = function (){
		return this.settings;
	};
};

trident2.gradient = function (o){
	this.helper = o.helper;
	this.engine = o.engine;
	this.sCont = o.sCont;
	this.activeEl = o.activeEl;
	
	var userBrowser = getBrowserInfo();
		
	var StylesInfo = this.activeEl.data('styles_info');
	var inputVals = StylesInfo.input_vals;
	if (!inputVals.trident2){
		inputVals.trident2 = {};
	}
	
	if (!inputVals.trident2.gradient){
		inputVals.trident2.gradient = {
			config       : {},
			colorStops   : [],
			imagesBefore : [],
			imagesAfter  : []
		};
	}
	
	this.settings = '<span>' + 
		'Preview' + 
		(userBrowser.engine == 'trident' || userBrowser.engine == 'trident2' || userBrowser.engine == 'presto' ? ' ( There is a 1-3 second delay for preview in Internet Explorer and Opera )' : '') + 
	'</span>' + 
	'<div class="gradientPreview" style="height:100px;border:1px solid #cccccc;"></div>' + 
	'<br>' + 
	'<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;">' + 
		'<table cellpadding="0" cellspacing="0" border="0" width="960px">' +
			'<tr>' +
				'<td><table width="100%">' +
					'<tr>' +
						'<td><button class="addGradientImageBefore" tooltip="Add Image For Multiple Backgrounds"><span>Add Image Above Gradient</span></button></td>' +
					'</tr>' +
					'<tr>' +
						'<td valign="top" class="gradientImagesBefore"></td>' +
					'</tr>' +
				'</table></td>' +
			'</tr>' +
			'<tr>' +
				'<td>&nbsp;</td>' +
			'</tr>' +
			'<tr>' +
				'<td style="height:2em;">Gradient Type: <select name="gradient_type">' +
					'<option value="linear">Linear</option>' +
					//'<option value="radial">Radial</option>' +
				'</select></td>' +
			'</tr>' +
			'<tr class="linear">' +
				'<td valign="top"><table style="border-spacing: 15px 0px;" width="100%">' +
					'<thead>' +
						'<tr>' +
							'<th></th>' +
							'<th style="text-align:center" colspan="4">Color</th>' +
							'<th style="text-align:center">Horizonal Pos</th>' +
							'<th style="text-align:center">Vertical Pos</th>' +
						'</tr>' +
					'</thead>' +
					'<tbody>' +
						'<tr>' +
							'<td rowspan="3" valign="top"><b>Start</b></td>' +
							'<td style="border:1px solid #cccccc;" class="makeColorPicker_RGBA" colspan="4" align="center">Click Here For Color Picker</td>' +
							'<td><div id="start_horizontal_posSlider"></div></td>' +
							'<td><div id="start_vertical_posSlider"></div></td>' +
						'</tr>' +
						'<tr id="start">' +
							'<td style="text-align:center"><input type="text" size="4" class="colorPickerRGBA_Red" name="start_color_r" value="255"></td>' +
							'<td style="text-align:center"><input type="text" size="4" class="colorPickerRGBA_Green" name="start_color_g" value="255"></td>' +
							'<td style="text-align:center"><input type="text" size="4" class="colorPickerRGBA_Blue" name="start_color_b" value="255"></td>' +
							'<td style="text-align:center"><input type="text" size="4" class="colorPickerRGBA_Alpha" name="start_color_a" value="100">%</td>' +
							'<td style="text-align:center"><input type="text" value="0" size="4" name="start_horizontal_pos">%</td>' +
							'<td style="text-align:center"><input type="text" value="0" size="4" name="start_vertical_pos">%</td>' +
						'</tr>' +
						'<tr>' +
							'<td style="text-align:center">Red</td>' +
							'<td style="text-align:center">Green</td>' +
							'<td style="text-align:center">Blue</td>' +
							'<td style="text-align:center">Alpha</td>' +
						'</tr>' +
						'<tr>' +
							'<td rowspan="3" valign="top"><b>End</b></td>' +
							'<td style="border:1px solid #cccccc;" class="makeColorPicker_RGBA" colspan="4" align="center">Click Here For Color Picker</td>' +
							'<td><div id="end_horizontal_posSlider"></div></td>' +
							'<td><div id="end_vertical_posSlider"></div></td>' +
						'</tr>' +
						'<tr id="end">' +
							'<td style="text-align:center"><input type="text" size="4" class="colorPickerRGBA_Red" name="end_color_r" value="255"></td>' +
							'<td style="text-align:center"><input type="text" size="4" class="colorPickerRGBA_Green" name="end_color_g" value="255"></td>' +
							'<td style="text-align:center"><input type="text" size="4" class="colorPickerRGBA_Blue" name="end_color_b" value="255"></td>' +
							'<td style="text-align:center"><input type="text" size="4" class="colorPickerRGBA_Alpha" name="end_color_a" value="100">%</td>' +
							'<td style="text-align:center"><input type="text" value="0" size="4" name="end_horizontal_pos">%</td>' +
							'<td style="text-align:center"><input type="text" value="100" size="4" name="end_vertical_pos">%</td>' +
						'</tr>' +
						'<tr>' +
							'<td style="text-align:center">Red</td>' +
							'<td style="text-align:center">Green</td>' +
							'<td style="text-align:center">Blue</td>' +
							'<td style="text-align:center">Alpha</td>' +
						'</tr>' +
					'</tbody>' +
				'</table></td>' +
			'</tr>' +
			'<tr class="radial" style="display:none">' +
			'</tr>' +
			'<tr>' +
				'<td>&nbsp;</td>' +
			'</tr>' +
			'<tr>' +
				'<td><table width="100%">' +
					'<tr>' +
						'<td><button class="addGradientStop" tooltip="Add Gradient Color Stop"><span>Add Color Stop</span></button></td>' +
					'</tr>' +
					'<tr>' +
						'<td valign="top" class="gradientStops"></td>' +
					'</tr>' +
				'</table></td>' +
			'</tr>' +
			'<tr>' +
				'<td>&nbsp;</td>' +
			'</tr>' +
			'<tr>' +
				'<td><table width="100%">' +
					'<tr>' +
						'<td><button class="addGradientImageAfter" tooltip="Add Image For Multiple Backgrounds"><span>Add Image Below Gradient</span></button></td>' +
					'</tr>' +
					'<tr>' +
						'<td valign="top" class="gradientImagesAfter"></td>' +
					'</tr>' +
				'</table></td>' +
			'</tr>' +
		'</table>' + 
	'</div>' + 
	'<br>' + 
	'<span>' + 
		'Preview' + 
		(userBrowser.engine == 'trident' || userBrowser.engine == 'trident2' || userBrowser.engine == 'presto' ? ' ( There is a 1-3 second delay for preview in Internet Explorer and Opera )' : '') + 
	'</span>' + 
	'<div class="gradientPreview" style="height:100px;border:1px solid #cccccc;"></div>';
	
	this.colorStopSettings = '<div class="gradientStop">' + 
		'<table width="100%" style="border-spacing: 15px 0px;">' +
			'<thead>' + 
				'<tr>' + 
					'<th colspan="4" align="center">Color</th>' + 
					'<th align="center">Position</th>' + 
					'<th align="right" style="width:16px"><span class="ui-icon ui-icon-closethick removeGradientStop"></span></th>' + 
				'</tr>' + 
			'</thead>' + 
			'<tbody>' + 
				'<tr>' +
					'<td style="border:1px solid #cccccc;" class="makeColorPicker_RGBA" colspan="4" align="center">Click Here For Color Picker</td>' +
					'<td><div id="color_stop_posSlider"></div></td>' +
				'</tr>' +
				'<tr>' +
					'<td style="text-align:center"><input type="text" size="4" class="colorPickerRGBA_Red" name="color_stop_color_r" value="255"></td>' +
					'<td style="text-align:center"><input type="text" size="4" class="colorPickerRGBA_Green" name="color_stop_color_g" value="255"></td>' +
					'<td style="text-align:center"><input type="text" size="4" class="colorPickerRGBA_Blue" name="color_stop_color_b" value="255"></td>' +
					'<td style="text-align:center"><input type="text" size="4" class="colorPickerRGBA_Alpha" name="color_stop_color_a" value="100">%</td>' + 
					'<td style="text-align:center"><input type="text" value="50" size="4" name="color_stop_pos">%</td>' + 
				'</tr>' +
				'<tr>' +
					'<td style="text-align:center">Red</td>' +
					'<td style="text-align:center">Green</td>' +
					'<td style="text-align:center">Blue</td>' +
					'<td style="text-align:center">Alpha</td>' +
				'</tr>' +
			'</tbody>' + 
		'</table>' + 
	'</div>';

	this.imageSettings = '<div class="gradientImage">' + 
		'<table width="100%" style="border-spacing: 15px 0px;">' +
			'<thead>' + 
				'<tr>' + 
					'<th align="center">Image</th>' + 
					'<th align="center">Attachment</th>' + 
					'<th align="center">Repeat</th>' + 
					'<th align="center">Horizontal Pos</th>' + 
					'<th align="center">Vertical Pos</th>' + 
					'<th align="right" style="width:16px"><span class="ui-icon ui-icon-closethick removeGradientImage"></span></th>' + 
				'</tr>' + 
			'</thead>' + 
			'<tbody>' + 
				'<tr>' +
					'<td><input type="text" name="image_source" class="BrowseServerField" currentFolder=""></td>' +
					'<td><select name="image_attachment">' + 
						'<option value="">Inherit</option>' + 
						'<option value="scroll">Scroll</option>' + 
						'<option value="fixed">Fixed</option>' + 
					'</select</td>' +
					'<td><select name="image_repeat">' + 
						'<option value="no-repeat">No Repeat</option>' + 
						'<option value="repeat">Tile</option>' + 
						'<option value="repeat-x">Repeat Horizontal</option>' + 
						'<option value="repeat-y">Repeat Vertical</option>' + 
					'</select</td>' +
					'<td><div id="image_pos_xSlider"></div></td>' + 
					'<td><div id="image_pos_ySlider"></div></td>' + 
				'</tr>' +
				'<tr>' +
					'<td colspan="3"></td>' + 
					'<td style="text-align:center"><input type="text" value="50" size="4" name="image_pos_x">%</td>' + 
					'<td style="text-align:center"><input type="text" value="50" size="4" name="image_pos_y">%</td>' + 
				'</tr>' +
			'</tbody>' + 
		'</table>' + 
	'</div>';

	this.showSettings = function (){
		var self = this;
		var inputVals = this.activeEl.data('styles_info').input_vals.trident2.gradient;

		$(this.sCont).html(self.settings);

		$(this.sCont).find('.makeColorPicker_RGBA').each(function (){
			buildBackgroundColorPicker_RGBA($(this), {
				inputR: $(this).parent().next().find('.colorPickerRGBA_Red'),
				inputG: $(this).parent().next().find('.colorPickerRGBA_Green'),
				inputB: $(this).parent().next().find('.colorPickerRGBA_Blue'),
				inputA: $(this).parent().next().find('.colorPickerRGBA_Alpha')
			});
			
			$(this).bind('onChange', function (){
				self.updateGradient();
			});
		});

		$(this.sCont).find('input[name=start_horizontal_pos], input[name=start_vertical_pos], input[name=end_horizontal_pos], input[name=end_vertical_pos]').each(function (){
			var inputEl = this;
			
			$(inputEl).keyup(function (){
				$(self.sCont).find('#' + $(this).attr('name') + 'Slider').slider('value', $(this).val());
				self.updateGradient();
			});

			self.layoutBuilder.createPercentSlider($(self.sCont).find('#' + $(this).attr('name') + 'Slider'), {
				value: parseInt($(this).val()),
				slide: function (e, ui){
					$(inputEl).val(ui.value);
					self.updateGradient();
				}
			});
			
			if (inputVals.config[$(this).attr('name')]){
				$(this).val(inputVals.config[$(this).attr('name')]);
			}
		});
		
		$(this.sCont).find('input[name=start_color_r], input[name=start_color_g], input[name=start_color_b], input[name=start_color_a]').each(function (){
			$(this).keyup(function (){
				$(this).parent().parent().parent().find('.makeColorPicker_RGBA').trigger('updateBackground');
				self.updateGradient();
			});
			
			if (inputVals.config[$(this).attr('name')]){
				$(this).val(inputVals.config[$(this).attr('name')]);
			}
		});
		
		$(this.sCont).find('input[name=end_color_r], input[name=end_color_g], input[name=end_color_b], input[name=end_color_a]').each(function (){
			$(this).keyup(function (){
				$(this).parent().parent().parent().find('.makeColorPicker_RGBA').trigger('updateBackground');
				self.updateGradient();
			});
			
			if (inputVals.config[$(this).attr('name')]){
				$(this).val(inputVals.config[$(this).attr('name')]);
			}
		});

		if (inputVals.colorStops && inputVals.colorStops.length > 0){
			$.each(inputVals.colorStops, function (){
				self.createColorStop(this);
			});
		}
		
		if (inputVals.imagesBefore && inputVals.imagesBefore.length > 0){
			$.each(inputVals.imagesBefore, function (){
				self.createImage('Before', this);
			});
		}
		
		if (inputVals.imagesAfter && inputVals.imagesAfter.length > 0){
			$.each(inputVals.imagesAfter, function (){
				self.createImage('After', this);
			});
		}
		
		$(this.sCont).find('.addGradientStop').click(function (){
			self.createColorStop();
			self.updateGradient();
		});
		
		$(this.sCont).find('.addGradientImageBefore').click(function (){
			self.createImage('Before');
			self.updateGradient();
		});
		
		$(this.sCont).find('.addGradientImageAfter').click(function (){
			self.createImage('After');
			self.updateGradient();
		});
		
		$(this.sCont).find('button').button();
		
		self.updateGradient();
	};

	this.updatePreview = function (){
		var self = this,
			urlBeforeStr = '',
			urlAfterStr = '',
			backgroundKey = 'background-image',
			$sCont = $(this.sCont);
			
		var colorStops = $sCont.find('.gradientStop');

		switch(userBrowser.engine){
			case 'webkit':
				var colorStopsStr = '';
				colorStops.each(function (){
					colorStopsStr += ', color-stop(' +
						(parseInt($(this).find('input[name=color_stop_pos]').val()) / 100) + ', ' +
						'rgba(' +
							$sCont.find('input[name=color_stop_color_r]').val() + ', ' +
							$sCont.find('input[name=color_stop_color_g]').val() + ', ' +
							$sCont.find('input[name=color_stop_color_b]').val() + ', ' +
							(parseInt($sCont.find('input[name=color_stop_color_a]').val()) / 100) +
						')' +
					')';
				});
				var gradientString = '-webkit-gradient(' +
					$sCont.find('select[name=gradient_type]').val() + ', ' +
					$sCont.find('input[name=start_horizontal_pos]').val() + '% ' +
					$sCont.find('input[name=start_vertical_pos]').val() + '%, ' +
					$sCont.find('input[name=end_horizontal_pos]').val() + '% ' +
					$sCont.find('input[name=end_vertical_pos]').val() + '%, ' +
					'from(' +
						'rgba(' +
							$sCont.find('input[name=start_color_r]').val() + ', ' +
							$sCont.find('input[name=start_color_g]').val() + ', ' +
							$sCont.find('input[name=start_color_b]').val() + ', ' +
							(parseInt($sCont.find('input[name=start_color_a]').val()) / 100) +
						')' +
					'), ' +
					'to(' +
						'rgba(' +
							$sCont.find('input[name=end_color_r]').val() + ', ' +
							$sCont.find('input[name=end_color_g]').val() + ', ' +
							$sCont.find('input[name=end_color_b]').val() + ', ' +
							(parseInt($sCont.find('input[name=end_color_a]').val()) / 100) +
						')' +
					')' +
					colorStopsStr +
				')';

				var imageStr = 'url(%source) %attachment %xpos %ypos %repeat';
				break;
			case 'trident':
				var gradientString = '-pie-background(%from, %to, %stops)';
				var imageStr = 'url(%source) %attachment %xpos %ypos %repeat';
				break;
			case 'trident2':
				var colorStopsStr = [];
				colorStopsStr.push({
					color: rgb2hex('rgb(' + 
						$sCont.find('input[name=start_color_r]').val() + ', ' +
						$sCont.find('input[name=start_color_g]').val() + ', ' +
						$sCont.find('input[name=start_color_b]').val() +
					')'),
					opacity: (parseInt($sCont.find('input[name=start_color_a]').val()) / 100),
					pos: 0
				});
				
				colorStops.each(function (){
					colorStopsStr.push({
						color: rgb2hex('rgb(' + 
							$sCont.find('input[name=color_stop_color_r]').val() + ', ' +
							$sCont.find('input[name=color_stop_color_g]').val() + ', ' +
							$sCont.find('input[name=color_stop_color_b]').val() +
						')'),
						opacity: (parseInt($sCont.find('input[name=color_stop_color_a]').val()) / 100),
						pos: parseInt($(this).find('input[name=color_stop_pos]').val())
					});
				});
				
				colorStopsStr.push({
					color: rgb2hex('rgb(' + 
						$sCont.find('input[name=end_color_r]').val() + ', ' +
						$sCont.find('input[name=end_color_g]').val() + ', ' +
						$sCont.find('input[name=end_color_b]').val() +
					')'),
					opacity: (parseInt($sCont.find('input[name=end_color_a]').val()) / 100),
					pos: 100
				});
				
				var gradientString = 'url(\'/extensions/templateManager/catalog/globalFiles/IE9_gradient.php' + 
					'?start_pos_x=' + $sCont.find('input[name=start_horizontal_pos]').val() + 
					'&start_pos_y=' + $sCont.find('input[name=start_vertical_pos]').val() + 
					'&end_pos_x=' + $sCont.find('input[name=end_horizontal_pos]').val() + 
					'&end_pos_y=' + $sCont.find('input[name=end_vertical_pos]').val() + 
					'&colorStops=' + escape(JSON.stringify(colorStopsStr)) +
				'\')';
				
				var imageStr = 'url(%source) %attachment %xpos %ypos %repeat';
				break;
			case 'presto':
				break;
			case 'gecko':
				var colorStopsStr = '';
				colorStops.each(function (){
					colorStopsStr += ', rgba(' +
						$sCont.find('input[name=color_stop_color_r]').val() + ', ' +
						$sCont.find('input[name=color_stop_color_g]').val() + ', ' +
						$sCont.find('input[name=color_stop_color_b]').val() + ', ' +
						(parseInt($sCont.find('input[name=color_stop_color_a]').val()) / 100) +
					')' + 
					parseInt($(this).find('input[name=color_stop_pos]').val()) + '%';
				});
				var gradientString = '-moz-' + $sCont.find('select[name=gradient_type]').val() + '-gradient(' + 
					$sCont.find('input[name=start_horizontal_pos]').val() + '% ' +
					$sCont.find('input[name=start_vertical_pos]').val() + '% ' +
					$sCont.find('input[name=start_angle]').val() + 'deg, ' +
					'rgba(' +
						$sCont.find('input[name=start_color_r]').val() + ', ' +
						$sCont.find('input[name=start_color_g]').val() + ', ' +
						$sCont.find('input[name=start_color_b]').val() + ', ' +
						(parseInt($sCont.find('input[name=start_color_a]').val()) / 100) +
					'), ' +
					'rgba(' +
						$sCont.find('input[name=end_color_r]').val() + ', ' +
						$sCont.find('input[name=end_color_g]').val() + ', ' +
						$sCont.find('input[name=end_color_b]').val() + ', ' +
						(parseInt($sCont.find('input[name=end_color_a]').val()) / 100) +
					')' +
					colorStopsStr +
				')';
					
				var imageStr = 'url(%source) %attachment %xpos %ypos %repeat';
				break;
		}
		
		$(this.sCont).find('.gradientImagesBefore .gradientImage').each(function (){
			urlBeforeStr += 'url(' + 
				$(this).find('input[name=image_source]').val() + 
			') ' + 
			$(this).find('select[name=image_attachment]').val() + ' ' + 
			$(this).find('input[name=image_pos_x]').val() + '% ' + 
			$(this).find('input[name=image_pos_y]').val() + '% ' + 
			$(this).find('select[name=image_repeat]').val() + ', ';
		});
		
		$(this.sCont).find('.gradientImagesAfter .gradientImage').each(function (){
			urlAfterStr += ', url(' + 
				$(this).find('input[name=image_source]').val() + 
			') ' + 
			$(this).find('select[name=image_attachment]').val() + ' ' + 
			$(this).find('input[name=image_pos_x]').val() + '% ' + 
			$(this).find('input[name=image_pos_y]').val() + '% ' + 
			$(this).find('select[name=image_repeat]').val();
		});
		
		if (urlBeforeStr.length > 0 || urlAfterStr.length > 0){
			backgroundKey = 'background';
		}
		
		$(this.sCont).find('.gradientPreview').each(function (){
			$(this).attr('style', 'border:1px solid #cccccc;height:100px;' + backgroundKey + ': ' + urlBeforeStr + gradientString + urlAfterStr);
		});
		
		this.activeEl.each(function (){
			$(this).css(backgroundKey, urlBeforeStr + gradientString + urlAfterStr);
		});
	};
	
	this.updateGradient = function (){
		var self = this,
			$sCont = $(this.sCont);

		self.updatePreview();
		
		inputVals.trident2.gradient.config = {
			gradient_type        : $sCont.find('select[name=gradient_type]').val(),
			start_horizontal_pos : $sCont.find('input[name=start_horizontal_pos]').val(),
			start_vertical_pos   : $sCont.find('input[name=start_vertical_pos]').val(),
			end_horizontal_pos   : $sCont.find('input[name=end_horizontal_pos]').val(),
			end_vertical_pos     : $sCont.find('input[name=end_vertical_pos]').val(),
			start_color_r        : $sCont.find('input[name=start_color_r]').val(),
			start_color_g        : $sCont.find('input[name=start_color_g]').val(),
			start_color_b        : $sCont.find('input[name=start_color_b]').val(),
			start_color_a        : $sCont.find('input[name=start_color_a]').val(),
			end_color_r          : $sCont.find('input[name=end_color_r]').val(),
			end_color_g          : $sCont.find('input[name=end_color_g]').val(),
			end_color_b          : $sCont.find('input[name=end_color_b]').val(),
			end_color_a          : $sCont.find('input[name=end_color_a]').val()
		};
		
		inputVals.trident2.gradient.colorStops = [];
		$sCont.find('.gradientStop').each(function (){
			inputVals.trident2.gradient.colorStops.push({
				color_stop_pos     : $(this).find('input[name=color_stop_pos]').val(),
				color_stop_color_r : $(this).find('input[name=color_stop_color_r]').val(),
				color_stop_color_g : $(this).find('input[name=color_stop_color_g]').val(),
				color_stop_color_b : $(this).find('input[name=color_stop_color_b]').val(),
				color_stop_color_a : $(this).find('input[name=color_stop_color_a]').val()
			});
		});
		
		inputVals.trident2.gradient.imagesBefore = [];
		$sCont.find('.gradientImagesBefore .gradientImage').each(function (){
			inputVals.trident2.gradient.imagesBefore.push({
				image_source     : $(this).find('input[name=image_source]').val(),
				image_attachment : $(this).find('select[name=image_attachment]').val(),
				image_pos_x      : $(this).find('input[name=image_pos_x]').val(),
				image_pos_y      : $(this).find('input[name=image_pos_y]').val(),
				image_repeat     : $(this).find('select[name=image_repeat]').val()
			});
		});
		
		inputVals.trident2.gradient.imagesAfter = [];
		$sCont.find('.gradientImagesAfter .gradientImage').each(function (){
			inputVals.trident2.gradient.imagesAfter.push({
				image_source     : $(this).find('input[name=image_source]').val(),
				image_attachment : $(this).find('select[name=image_attachment]').val(),
				image_pos_x      : $(this).find('input[name=image_pos_x]').val(),
				image_pos_y      : $(this).find('input[name=image_pos_y]').val(),
				image_repeat     : $(this).find('select[name=image_repeat]').val()
			});
		});
		
		var StylesInfo = this.activeEl.data('styles_info');
		StylesInfo.input_vals.trident2.gradient = inputVals.trident2.gradient;
		this.activeEl.data('styles_info', StylesInfo);
	};
	
	this.createColorStop = function(values){
		var self = this;
		
		var $newStop = $(this.colorStopSettings);
		
		$(self.sCont).find('.gradientStops').append($newStop);

		$newStop.find('.makeColorPicker_RGBA').each(function (){
			buildBackgroundColorPicker_RGBA($(this), {
				inputR: $(this).parent().next().find('.colorPickerRGBA_Red'),
				inputG: $(this).parent().next().find('.colorPickerRGBA_Green'),
				inputB: $(this).parent().next().find('.colorPickerRGBA_Blue'),
				inputA: $(this).parent().next().find('.colorPickerRGBA_Alpha')
			});
			
			$(this).bind('onChange', function (){
				self.updateGradient();
			});
		});

		$newStop.find('input[name=color_stop_pos]').each(function (){
			var inputEl = this;
			
			$(inputEl).keyup(function (){
				$newStop.find('#' + $(this).attr('name') + 'Slider').slider('value', $(this).val());
				self.updateGradient();
			});

			self.layoutBuilder.createPercentSlider($newStop.find('#' + $(this).attr('name') + 'Slider'), {
				value: parseInt($(this).val()),
				slide: function (e, ui){
					$(inputEl).val(ui.value);
					self.updateGradient();
				}
			});
		});
		
		$newStop.find('input[name=color_stop_color_r], input[name=color_stop_color_g], input[name=color_stop_color_b], input[name=color_stop_color_a]').each(function (){
			$(this).keyup(function (){
				$newStop.find('.makeColorPicker_RGBA').trigger('updateBackground');
				self.updateGradient();
			});
		});
		
		$newStop.find('.removeGradientStop').click(function (){
			$newStop.remove();
			self.updateGradient();
		});
		
		if (values){
			$newStop.find('input[name=color_stop_color_r]').val(values.color_stop_color_r);
			$newStop.find('input[name=color_stop_color_g]').val(values.color_stop_color_g);
			$newStop.find('input[name=color_stop_color_b]').val(values.color_stop_color_b);
			$newStop.find('input[name=color_stop_color_a]').val(values.color_stop_color_a);
			$newStop.find('input[name=color_stop_pos]').val(values.color_stop_pos);
			$newStop.find('.makeColorPicker_RGBA').trigger('updateBackground');
		}
	};
	
	this.createImage = function (loc, values){
		var self = this;
		
		var $newImage = $(this.imageSettings);
		
		$(self.sCont).find('.gradientImages' + loc).append($newImage);
	
		$newImage.find('select[name=image_attachment]').change(function (){
			self.updateGradient();
		});
		
		$newImage.find('select[name=image_repeat]').change(function (){
			self.updateGradient();
		});
		
		$newImage.find('input[name=image_source]').blur(function (){
			self.updateGradient();
		});
		
		$newImage.find('input[name=image_pos_x], input[name=image_pos_y]').each(function (){
			var inputEl = this;
			
			$(inputEl).keyup(function (){
				$newImage.find('#' + $(this).attr('name') + 'Slider').slider('value', $(this).val());
				self.updateGradient();
			});

			self.layoutBuilder.createPercentSlider($newImage.find('#' + $(this).attr('name') + 'Slider'), {
				value: parseInt($(this).val()),
				slide: function (e, ui){
					$(inputEl).val(ui.value);
					self.updateGradient();
				}
			});
		});
		
		$newImage.find('.removeGradientImage').click(function (){
			$newImage.remove();
			self.updateGradient();
		});
	
		if (values){
			$newImage.find('input[name=image_source]').val(values.image_source);
			$newImage.find('select[name=image_attachment]').val(values.image_attachment);
			$newImage.find('select[name=image_repeat]').val(values.image_repeat);
			$newImage.find('input[name=image_pos_x]').val(values.image_pos_x);
			$newImage.find('input[name=image_pos_y]').val(values.image_pos_y);
		}
	};
}

$.extend($.fn.backgroundBuilder, {
	trident2: trident2
});
})(jQuery);
