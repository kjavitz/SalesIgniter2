/**
 * Sales Igniter E-Commerce System
 * Version: {ses_version}
 *
 * I.T. Web Experts
 * http://www.itwebexperts.com
 *
 * Copyright (c) {ses_copyright} I.T. Web Experts
 *
 * This script and its source are not distributable without the written consent of I.T. Web Experts
 */

$(document).ready(function () {
	var $PageGrid = $('.gridContainer');
	$PageGrid.newGrid('option', 'buttons', [
		{
			selector          : '.backButton',
			disableIfNone     : false,
			disableIfMultiple : false,
			click             : function (e, GridClass) {
				js_redirect(GridClass.buildAppRedirect('layout_manager', 'default', 'templateManager'));
			}
		},
		{
			selector          : '.editButton',
			disableIfNone     : true,
			disableIfMultiple : true,
			click             : function (e, GridClass) {
				js_redirect(GridClass.buildAppRedirect('layout_manager', 'editLayout', 'templateManager', [GridClass.getDataKey() + '=' + GridClass.getSelectedData()]));
			}
		},
		{
			selector          : '.newButton',
			disableIfNone     : false,
			disableIfMultiple : false,
			click             : function (e, GridClass) {
				GridClass.clearSelected();
				GridClass.showWindow({
					buttonEl   : this,
					contentUrl : GridClass.buildActionWindowLink('newLayout'),
					onAfterShow: function (){
						var height = 0;
						var width = 0;
						$('.mainBox').each(function () {
							if ($(this).outerWidth() > width){
								width = $(this).outerWidth();
							}

							if ($(this).outerHeight() > height){
								height = $(this).outerHeight();
							}
						});

						$('.mainBox').width(width).height(height);

						$('.checkAll').click(function(){
							var self = this;
							$(this).parent().find('input:checkbox').each(function (){
								this.checked = self.checked;
							});

							if (self.checked){
								$(this).parent().find('.checkAllText').html('Uncheck All');
							}else{
								$(this).parent().find('.checkAllText').html('Check All');
							}
						});

						$('.checkAllPages').click(function (){
							var self = this;
							$(self).parent().parent().find('.pageBox').each(function (){
								this.checked = self.checked;
							});
						});

						$('.checkAllApps').click(function (){
							var self = this;
							$(self).parent().parent().find('.appBox').each(function (){
								this.checked = self.checked;
							});
							$(self).parent().parent().find('.pageBox').each(function (){
								this.checked = self.checked;
							});
						});

						$(this).find('select[name=pageType]').change(function (){
							$('tr[data-for_page_type]').hide();
							var self = this;
							$('tr[data-for_page_type="' + $(this).val() + '"]').show('fast', function (){
								if ($(self).val() == 'template'){
									$('.mainBox').width('auto').height('auto');
									var height = 0;
									var width = 0;
									$('.mainBox').each(function () {
										if ($(this).outerWidth() > width){
											width = $(this).outerWidth();
										}

										if ($(this).outerHeight() > height){
											height = $(this).outerHeight();
										}
									});

									$('.mainBox').width(width).height(height);
								}
							});
						}).change();
					},
					buttons    : ['cancel', {
						type: 'save',
						click: GridClass.windowButtonEvent('save', {
							actionName: 'createLayout',
							addGetVars: ['template_id=' + $_GET['template_id']],
							onSuccess: function (){
								js_redirect(GridClass.buildCurrentAppRedirect('layouts', ['template_id=' + $_GET['template_id']]));
							}
						})
					}]
				});
			}
		},
		{
			selector          : '.configureButton',
			disableIfNone     : true,
			disableIfMultiple : true,
			click             : function (e, GridClass) {
				GridClass.showWindow({
					buttonEl   : this,
					contentUrl : GridClass.buildActionWindowLink('newLayout', true),
					onAfterShow: function (){
						var height = 0;
						var width = 0;
						$('.mainBox').each(function () {
							if ($(this).outerWidth() > width){
								width = $(this).outerWidth();
							}

							if ($(this).outerHeight() > height){
								height = $(this).outerHeight();
							}
						});

						$('.mainBox').width(width).height(height);

						$('.checkAll').click(function(){
							var self = this;
							$(this).parent().find('input:checkbox').each(function (){
								this.checked = self.checked;
							});

							if (self.checked){
								$(this).parent().find('.checkAllText').html('Uncheck All');
							}else{
								$(this).parent().find('.checkAllText').html('Check All');
							}
						});

						$('.checkAllPages').click(function (){
							var self = this;
							$(self).parent().parent().find('.pageBox').each(function (){
								this.checked = self.checked;
							});
						});

						$('.checkAllApps').click(function (){
							var self = this;
							$(self).parent().parent().find('.appBox').each(function (){
								this.checked = self.checked;
							});
							$(self).parent().parent().find('.pageBox').each(function (){
								this.checked = self.checked;
							});
						});

						$(this).find('select[name=pageType]').change(function (){
							$('tr[data-for_page_type]').hide();
							var self = this;
							$('tr[data-for_page_type="' + $(this).val() + '"]').show('fast', function (){
								if ($(self).val() == 'template'){
									$('.mainBox').width('auto').height('auto');
									var height = 0;
									var width = 0;
									$('.mainBox').each(function () {
										if ($(this).outerWidth() > width){
											width = $(this).outerWidth();
										}

										if ($(this).outerHeight() > height){
											height = $(this).outerHeight();
										}
									});

									$('.mainBox').width(width).height(height);
								}
							});
						}).change();
					},
					buttons    : ['cancel', {
						type: 'save',
						click: GridClass.windowButtonEvent('save', {
							actionName: 'createLayout'
						})
					}]
				});
			}
		},
		{
			selector          : '.deleteButton',
			disableIfNone     : true,
			disableIfMultiple : false,
			click             : function (e, GridClass) {
				GridClass.showDeleteDialog({
					buttonEl   : this,
					confirmUrl : GridClass.buildActionLink('deleteLayout', [GridClass.getDataKey() + '=' + GridClass.getSelectedData()])
				});
			}
		},
		{
			selector          : '.duplicateButton',
			disableIfNone     : true,
			disableIfMultiple : true,
			click             : function (e, GridClass) {
				GridClass.showConfirmDialog({
					title: 'Duplicate Layout',
					content: 'New Layout Name: <input type="text" name="layout_name">',
					errorMessage: 'This layout could not be duplicated.',
					onConfirm: function (){
						var dialogEl = this;

						$.ajax({
							cache: false,
							url: GridClass.buildActionLink('duplicateLayout', [GridClass.getDataKey() + '=' + GridClass.getSelectedData()]),
							dataType: 'json',
							data: $(dialogEl).find('*').serialize(),
							type: 'post',
							success: function (data) {
								js_redirect(GridClass.buildCurrentAppRedirect());
							}
						});
					},
					success: function () {
					}
				});
			}
		}
	]);

	$('.generateCode').click(function(){
		$.ajax({
			cache: false,
			url: js_app_link('appExt=templateManager&app=layout_manager&appPage=layouts&action=generateCode&layout_id=' + $('.gridBodyRow.state-active').data('layout_id')),
			dataType: 'json',
			type: 'post',
			success: function (data) {
				$('<div title="Javascript generated Code"></div>').html(data.html )
					.dialog({
						height: 500,
						width: 600,
						close: function(event, ui)
						{
							$(this).dialog('destroy').remove();
						}
					});
				$('.genType, .genTemplate, .genProduct').change(function(){
					$.ajax({
						cache: false,
						url: js_app_link('appExt=templateManager&app=layout_manager&appPage=layouts&action=generateCode&layout_id=' + $('.gridBodyRow.state-active').data('layout_id')),
						dataType: 'json',
						data:'onlyCode=1&type='+$('.genType').val()+'&templateName='+$('.genTemplate').val()+'&products_id='+$('.genProduct option:selected').val(),
						type: 'post',
						success: function (data) {
							$('.genCode').html(data.html);
						}
					});
				});
			}
		});


	});
});