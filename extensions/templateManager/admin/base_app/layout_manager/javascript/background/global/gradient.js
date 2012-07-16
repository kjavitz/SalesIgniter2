(function($, undefined) {

	$.fn.backgroundBuilder.global.gradient = function (o){
		this.helper = o.helper;
		this.engine = o.engine;
		this.sCont = o.sCont;
		this.activeEl = o.activeEl;
		this.layoutBuilder = o.layoutBuilder;

		var userBrowser = this.layoutBuilder.getBrowserInfo();

		var inputVals = this.activeEl.data('inputs');
		if (!inputVals.background){
			inputVals.background = {};
		}

		if (!inputVals.background.global){
			inputVals.background.global = {};
		}

		if (!inputVals.background.global.gradient){
			inputVals.background.global.gradient = {
				config       : {},
				colorStops   : [],
				imagesBefore : [],
				imagesAfter  : []
			};
		}

		this.getInputsData = function (){
			return this.activeEl.data('inputs');
		};

		this.setInputsData = function (data){
			this.activeEl.data('inputs', data);
		};

		this.getData = function (){
			return this.activeEl.data('inputs').background.global.gradient;
		};

		this.updateActiveElementData = function (elData){
			var inputVals = this.getInputsData();
			inputVals.background.global.gradient = elData;
			this.setInputsData(inputVals);
		};

		this.showSettings = function (){
			var self = this;
			var inputVals = this.getData();

			$(this.sCont).html(self.settings);

			$.fn.backgroundBuilder.setupGradientRGBA(self, $(this.sCont).find('.makeColorPicker_RGBA'), inputVals.config);

			$(this.sCont).find('input[name=angle]').each(function (){
				var inputEl = this;

				$(inputEl).keyup(function (){
					$(self.sCont).find('#' + $(this).attr('name') + 'Slider').slider('value', $(this).val());
					self.updateGradient();
				});

				if (inputVals.config[$(this).attr('name')]){
					$(this).val(inputVals.config[$(this).attr('name')]);
				}

				self.layoutBuilder.createAngleSlider($(self.sCont).find('#' + $(this).attr('name') + 'Slider'), {
					value: parseInt($(this).val()),
					slide: function (e, ui){
						$(inputEl).val(ui.value);
						self.updateGradient();
					}
				});
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
				backgroundKey = 'background-image',
				$sCont = $(this.sCont),
				applyBackground = true;

			if (userBrowser.engine == 'trident' && userBrowser.version < 10){
				$(this.sCont).find('.gradientPreview').html('Preview Not Available In Internet Explorer Versions Less Than 10');
				return;
			}else if (userBrowser.engine == 'presto' && userBrowser.version < 11.10){
				$(this.sCont).find('.gradientPreview').html('Preview Not Available In Opera Versions Less Than 11.10');
				return;
			}

			var colorStops = $sCont.find('.gradientStop');
			var backgroundArr = [];

			$(this.sCont).find('.gradientImagesBefore .gradientImage').each(function (){
				backgroundKey = 'background';
				backgroundArr.push($.fn.backgroundBuilder.backgroundUrlStringFromCollection($(this), 'noColor'));
			});

			var colorStopsArr = [];
			var startColorCollection = $sCont.find('input[name=start_color_r], input[name=start_color_g], input[name=start_color_b], input[name=start_color_a]');
			var endColorCollection = $sCont.find('input[name=end_color_r], input[name=end_color_g], input[name=end_color_b], input[name=end_color_a]');

			colorStopsArr.push($.fn.backgroundBuilder.rgbStringFromCollection(startColorCollection, true) + ' 0%');
			colorStops.each(function (){
				colorStopsArr.push(
					$.fn.backgroundBuilder.rgbStringFromCollection($(this), true) + ' ' +
					parseInt($(this).find('input[name=color_stop_pos]').val()) + '%'
				);
			});
			colorStopsArr.push($.fn.backgroundBuilder.rgbStringFromCollection(endColorCollection, true) + ' 100%');

			var cssPrefix = '';
			switch(true){
				case (userBrowser.engine == 'trident' && userBrowser.version >= 10):
					cssPrefix = '-ms-';
					break;
				case (userBrowser.engine == 'presto' && userBrowser.version >= 11.10):
					cssPrefix = '-o-';
					break;
				case (userBrowser.engine == 'gecko'):
					cssPrefix = '-moz-';
					break;
				case (userBrowser.engine == 'webkit'):
					cssPrefix = '-webkit-';
					break;
			}

			var angle = $(this.sCont).find('input[name=angle]').val();

			backgroundArr.push(cssPrefix + 'linear-gradient(' + angle + 'deg, ' + colorStopsArr.join(', ') + ')');

			$(this.sCont).find('.gradientImagesAfter .gradientImage').each(function (){
				backgroundKey = 'background';
				backgroundArr.push($.fn.backgroundBuilder.backgroundUrlStringFromCollection($(this), true));
			});

			if (applyBackground === true){
				$.fn.backgroundBuilder.updatePreviewElementsStyle(this.sCont.find('.gradientPreview'), backgroundKey, backgroundArr.join(', '));
				$.fn.backgroundBuilder.updateActiveElementStyle(this.activeEl, backgroundKey, backgroundArr.join(', '));
			}
		};

		this.updateGradient = function (){
			var self = this,
				$sCont = $(this.sCont);

			self.updatePreview();

			inputVals.background.global.gradient.config = {
				gradient_type : $sCont.find('select[name=gradient_type]').val(),
				angle         : $sCont.find('input[name=angle]').val(),
				start_color_r : $sCont.find('input[name=start_color_r]').val(),
				start_color_g : $sCont.find('input[name=start_color_g]').val(),
				start_color_b : $sCont.find('input[name=start_color_b]').val(),
				start_color_a : $sCont.find('input[name=start_color_a]').val(),
				end_color_r   : $sCont.find('input[name=end_color_r]').val(),
				end_color_g   : $sCont.find('input[name=end_color_g]').val(),
				end_color_b   : $sCont.find('input[name=end_color_b]').val(),
				end_color_a   : $sCont.find('input[name=end_color_a]').val()
			};

			inputVals.background.global.gradient.colorStops = [];
			$sCont.find('.gradientStop').each(function (){
				inputVals.background.global.gradient.colorStops.push({
					color_stop_pos     : $(this).find('input[name=color_stop_pos]').val(),
					color_stop_color_r : $(this).find('input[name=color_stop_color_r]').val(),
					color_stop_color_g : $(this).find('input[name=color_stop_color_g]').val(),
					color_stop_color_b : $(this).find('input[name=color_stop_color_b]').val(),
					color_stop_color_a : $(this).find('input[name=color_stop_color_a]').val()
				});
			});

			inputVals.background.global.gradient.imagesBefore = [];
			$sCont.find('.gradientImagesBefore .gradientImage').each(function (){
				inputVals.background.global.gradient.imagesBefore.push({
					image_source             : $(this).find('input[name=image_source]').val(),
					image_attachment         : $(this).find('select[name=image_attachment]').val(),
					image_pos_x              : $(this).find('input[name=image_pos_x]').val(),
					image_pos_y              : $(this).find('input[name=image_pos_y]').val(),
					image_repeat             : $(this).find('select[name=image_repeat]').val()
				});
			});

			inputVals.background.global.gradient.imagesAfter = [];
			$sCont.find('.gradientImagesAfter .gradientImage').each(function (){
				inputVals.background.global.gradient.imagesAfter.push({
					image_background_color_r : $(this).find('input[name=image_background_color_r]').val(),
					image_background_color_g : $(this).find('input[name=image_background_color_g]').val(),
					image_background_color_b : $(this).find('input[name=image_background_color_b]').val(),
					image_background_color_a : $(this).find('input[name=image_background_color_a]').val(),
					image_source             : $(this).find('input[name=image_source]').val(),
					image_attachment         : $(this).find('select[name=image_attachment]').val(),
					image_pos_x              : $(this).find('input[name=image_pos_x]').val(),
					image_pos_y              : $(this).find('input[name=image_pos_y]').val(),
					image_repeat             : $(this).find('select[name=image_repeat]').val()
				});
			});

			this.updateActiveElementData(inputVals.background.global.gradient);
		};

		this.createColorStop = function(values){
			var self = this;

			var $newStop = $(this.colorStopSettings);

			$(self.sCont).find('.gradientStops').append($newStop);

			$.fn.backgroundBuilder.setupGradientRGBA(self, $newStop.find('.makeColorPicker_RGBA'), values);

			$newStop.find('input[name=color_stop_pos]').each(function (){
				var inputEl = this;

				$(inputEl).keyup(function (){
					$newStop.find('#' + $(this).attr('name') + 'Slider').slider('value', $(this).val());
					self.updateGradient();
				});

				if (values && values[$(this).attr('name')]){
					$(this).val(values[$(this).attr('name')]);
				}

				self.layoutBuilder.createPercentSlider($newStop.find('#' + $(this).attr('name') + 'Slider'), {
					value: parseInt($(this).val()),
					slide: function (e, ui){
						$(inputEl).val(ui.value);
						self.updateGradient();
					}
				});
			});

			$newStop.find('.removeGradientStop').click(function (){
				$newStop.remove();
				self.updateGradient();
			});
		};

		this.createImage = function (loc, values){
			var self = this;
			var removeSel = '.' + (loc == 'Before' ? 'after' : 'before') + 'Only';

			var $newImage = $(this.imageSettings);
			$newImage.find(removeSel).remove();

			$(self.sCont).find('.gradientImages' + loc).append($newImage);

			$.fn.backgroundBuilder.setupGradientRGBA(self, $newImage.find('.makeColorPicker_RGBA'), values);

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

				if (values && values[$(this).attr('name')]){
					$(this).val(values[$(this).attr('name')]);
				}

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
			}
		};

		this.settings = '' +
			'<span>Preview</span>' +
			'<div class="gradientPreview" style="height:100px;border:1px solid #cccccc;"></div>' +
			'<br>' +
			'<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;">' +
			'<table cellpadding="0" cellspacing="0" border="0" width="960px">' +
			'<tr>' +
			'<td><table width="100%">' +
			'<tr>' +
			'<td>' +
			'<button class="addGradientImageBefore" tooltip="Add Image For Multiple Backgrounds">' +
			'<span>Add Image Above Gradient</span>' +
			'</button>' +
			'</td>' +
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
			'<td valign="top">' +
			'<table style="border-spacing: 15px 0px;" width="100%">' +
			'<thead>' +
			'<tr>' +
			'<th></th>' +
			'<th style="text-align:center" colspan="4">Color</th>' +
			'<th style="text-align:center">Angle</th>' +
			'</tr>' +
			'</thead>' +
			'<tbody>' +
			'<tr>' +
			'<td rowspan="3" valign="top"><b>Start</b></td>' +
			'<td style="border:1px solid #cccccc;" class="makeColorPicker_RGBA" colspan="4" align="center">Click Here For Color Picker</td>' +
			'<td><div id="angleSlider"></div></td>' +
			'</tr>' +
			'<tr id="start">' +
			'<td style="text-align:center"><input type="text" size="4" class="colorPickerRGBA_Red" name="start_color_r" value="255"></td>' +
			'<td style="text-align:center"><input type="text" size="4" class="colorPickerRGBA_Green" name="start_color_g" value="255"></td>' +
			'<td style="text-align:center"><input type="text" size="4" class="colorPickerRGBA_Blue" name="start_color_b" value="255"></td>' +
			'<td style="text-align:center"><input type="text" size="4" class="colorPickerRGBA_Alpha" name="start_color_a" value="100">%</td>' +
			'<td style="text-align:center"><input type="text" value="270" size="4" name="angle">%</td>' +
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
			'</tr>' +
			'<tr id="end">' +
			'<td style="text-align:center"><input type="text" size="4" class="colorPickerRGBA_Red" name="end_color_r" value="255"></td>' +
			'<td style="text-align:center"><input type="text" size="4" class="colorPickerRGBA_Green" name="end_color_g" value="255"></td>' +
			'<td style="text-align:center"><input type="text" size="4" class="colorPickerRGBA_Blue" name="end_color_b" value="255"></td>' +
			'<td style="text-align:center"><input type="text" size="4" class="colorPickerRGBA_Alpha" name="end_color_a" value="100">%</td>' +
			'</tr>' +
			'<tr>' +
			'<td style="text-align:center">Red</td>' +
			'<td style="text-align:center">Green</td>' +
			'<td style="text-align:center">Blue</td>' +
			'<td style="text-align:center">Alpha</td>' +
			'</tr>' +
			'</tbody>' +
			'</table>' +
			'</td>' +
			'</tr>' +
			'<tr class="radial" style="display:none">' +
			'</tr>' +
			'<tr>' +
			'<td>&nbsp;</td>' +
			'</tr>' +
			'<tr>' +
			'<td>' +
			'<table width="100%">' +
			'<thead>' +
			'</thead>' +
			'<tbody>' +
			'<tr>' +
			'<td>' +
			'<button class="addGradientStop" tooltip="Add Gradient Color Stop">' +
			'<span>Add Color Stop</span>' +
			'</button>' +
			'</td>' +
			'</tr>' +
			'<tr>' +
			'<td valign="top" class="gradientStops"></td>' +
			'</tr>' +
			'</tbody>' +
			'</table>' +
			'</td>' +
			'</tr>' +
			'<tr>' +
			'<td>&nbsp;</td>' +
			'</tr>' +
			'<tr>' +
			'<td>' +
			'<table width="100%">' +
			'<thead>' +
			'</thead>' +
			'<tbody>' +
			'<tr>' +
			'<td>' +
			'<button class="addGradientImageAfter" tooltip="Add Image For Multiple Backgrounds">' +
			'<span>Add Image Below Gradient</span>' +
			'</button>' +
			'</td>' +
			'</tr>' +
			'<tr>' +
			'<td valign="top" class="gradientImagesAfter"></td>' +
			'</tr>' +
			'</tbody>' +
			'</table>' +
			'</td>' +
			'</tr>' +
			'</table>' +
			'</div>' +
			'<br>' +
			'<span>Preview</span>' +
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
			'<th align="center" colspan="4" class="afterOnly">Background Color</th>' +
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
			'<td colspan="4" class="afterOnly makeColorPicker_RGBA" align="center">Click Here For Color Picker</td>' +
			'<td><input type="text" name="image_source" class="fileManager" data-files_source="' + jsConfig.get('DIR_FS_CATALOG') + 'templates/"></td>' +
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
			'<td style="text-align:center" class="afterOnly"><input type="text" size="4" class="colorPickerRGBA_Red" name="image_background_color_r" value="255"></td>' +
			'<td style="text-align:center" class="afterOnly"><input type="text" size="4" class="colorPickerRGBA_Green" name="image_background_color_g" value="255"></td>' +
			'<td style="text-align:center" class="afterOnly"><input type="text" size="4" class="colorPickerRGBA_Blue" name="image_background_color_b" value="255"></td>' +
			'<td style="text-align:center" class="afterOnly"><input type="text" size="4" class="colorPickerRGBA_Alpha" name="image_background_color_a" value="100">%</td>' +
			'<td colspan="3"></td>' +
			'<td style="text-align:center"><input type="text" value="50" size="4" name="image_pos_x">%</td>' +
			'<td style="text-align:center"><input type="text" value="50" size="4" name="image_pos_y">%</td>' +
			'</tr>' +
			'<tr class="afterOnly">' +
			'<td style="text-align:center">Red</td>' +
			'<td style="text-align:center">Green</td>' +
			'<td style="text-align:center">Blue</td>' +
			'<td style="text-align:center">Alpha</td>' +
			'</tr>' +
			'</tbody>' +
			'</table>' +
			'</div>';
	};
	
})(jQuery);
