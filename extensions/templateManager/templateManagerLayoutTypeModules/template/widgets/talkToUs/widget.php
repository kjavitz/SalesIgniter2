<?php
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

class TemplateManagerWidgetTalkToUs extends TemplateManagerWidget
{

	public function __construct()
	{
		$this->init('talkToUs', false, __DIR__);
	}

	public function show(TemplateManagerLayoutBuilder $LayoutBuilder)
	{
		$htmlForm = htmlBase::newElement('form')
			->attr('name', 'talkToUs')
			->attr('method', 'post')
			->attr('action', tep_href_link('includes/modules/infoboxes/talkToUs/formResponse.php'));

		$url = (!empty($_SERVER['HTTPS'])) ? "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] : "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

		$htmlURL = htmlBase::newElement('input')
			->setType('hidden')
			->setName('url')
			->setValue($url);

		$htmlText = htmlBase::newElement('p')
			->html('We want to hear from you.  Please let us know if you have questions, comments or feedback on any of our products or services.');

		$htmlBr = htmlBase::newElement('br');
		$htmlEmail = htmlBase::newElement('input')
			->setName('email_address')
			->setLabel('Email Address:')
			->setLabelPosition('before')
			->setLabelSeparator('<br/>')
			->setId('emailAddressTalkToUs');

		$htmlPhone = htmlBase::newElement('input')
			->setName('phone')
			->setLabel('Phone Number:')
			->setLabelPosition('before')
			->setLabelSeparator('<br/>')
			->setId('phoneTalkToUs');

		$htmlName = htmlBase::newElement('input')
			->setName('name')
			->setLabel('Name:')
			->setLabelPosition('before')
			->setLabelSeparator('<br/>')
			->setId('nameTalkToUs');

		$htmlTextBeforeMessage = htmlBase::newElement('span')
			->html('Message:');

		$htmlMessage = htmlBase::newElement('textarea')
			->attr('rows', 5)
			->attr('cols', 20)
			->attr('id', 'messageTalkToUs')
			->setName('message');

		$htmlImg = htmlBase::newElement('span')
			->html('<img src="' . tep_href_link('securimage_show.php', session_name() . '=' . session_id()) . '">');

		$htmlCode = htmlBase::newElement('input')
			->setName('code')
			->setLabel('Antibot:')
			->setLabelPosition('before')
			->setLabelSeparator('<br/>')
			->setId('codeTalkToUs');

		$htmlButton = htmlBase::newElement('button')
			->setType('submit')
			->setText('Send');
		$htmlForm->append($htmlText)
			->append($htmlName)
			->append($htmlBr)
			->append($htmlPhone)
			->append($htmlBr)
			->append($htmlEmail)
			->append($htmlBr)
			->append($htmlTextBeforeMessage)
			->append($htmlMessage)
			->append($htmlImg)
			->append($htmlCode)
			->append($htmlURL)
			->append($htmlButton);
		$this->setBoxContent($htmlForm->draw());

		return $this->draw();
	}
}

?>