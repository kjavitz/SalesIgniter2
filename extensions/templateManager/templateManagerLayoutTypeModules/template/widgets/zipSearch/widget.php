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

class TemplateManagerWidgetZipSearch extends TemplateManagerWidget
{

	public function __construct()
	{
		global $App;
		$this->init('zipSearch', false, __DIR__);
	}

	public function show(TemplateManagerLayoutBuilder $LayoutBuilder)
	{
		$stores = Doctrine_Query::create()
			->select()
			->from('Stores')
			->orderBy('stores_location')
			->execute();

		ob_start();
		?>
	<div class="location-block">
		<div class="l2">
			<div class="l3">
				<div class="img"><img src="/templates/travelbabees/images/left-map.png" width="188" height="129" alt="">
				</div>
				<div class="text">
					Please enter the delivery zip code or select a city from the list below
				</div>
				<div class="zip-code bcont">
					<form action="/multiStore/zip/default.php" method="get">
						<em><input type="text" name="zip" class="input" value=""></em>
						<input type="submit" value="Search" class="submit">
					</form>
				</div>

				<div class="country">
					<?php foreach($stores as $current_store): ?>
					<?php if (!empty($current_store->stores_location)): ?>
						<div class="item">
							<a href="http://<?php echo $current_store->stores_domain; ?>/products/all.php"><?php echo $current_store->stores_location; ?></a>
						</div>
						<?php endif; ?>
					<?php endforeach;?>
				</div>
			</div>
		</div>
	</div>
	<?php
		$content = ob_get_clean();

		$this->setBoxContent($content);
		return $this->draw();
	}
}