<?php
/*
	Blog Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	$blog = $appExtension->getExtension('blog');

	if ($_GET['appPage'] == 'default'){
		$app_pg = null;
	}else{
		$app_pg = $_GET['appPage'];
	}

	if(!isset($_GET['page'])){
		$pg = 1;
	}else{
		$pg = $_GET['page'];
	}


	$pg_limit = (int) sysConfig::get('EXTENSION_BLOG_POST_PER_PAGE');

	$pagerBar = '';
	//$posts = $blog->getCategoriesPosts(null, $app_pg, $pg_limit, $pg, &$pagerBar);
	$posts = $blog->getPostsWithPaging(null, $app_pg, $pg_limit, $pg, &$pagerBar);
	$contentHtml = '';
	foreach ($posts as $post){
		$categ = '';
		$postCategories = $blog->getPostCategories($post['post_id']);
		$Qcomments = Doctrine_Query::create()
			->from('BlogCommentToPost c')
			->leftJoin('c.BlogComments pc')
			->where('c.blog_post_id = ?', $post['post_id'])
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		foreach ($postCategories as $cat){
			$categ .= $cat['BlogCategories']['BlogCategoriesDescription'][Session::get('languages_id')]['blog_categories_title'] . ', ';
		}
		$categ = substr($categ, 0, strlen($categ) - 2);
		/*special modifications*/
		$contentHtml.= "<h2 class='blog_post_title'><a href='" . itw_app_link('appExt=blog', 'show_post', $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_seo_url']) . "'>" . $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_title'] . "</a></h2>";
		$contentHtml.= "<div class='blog_post_text'>" . $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_text'] . "</div>";
		$contentHtml.= "<p class='blog_post_foot'>" . "Date: " . $post['post_date']->format(sysLanguage::getDateFormat('short')) . "<br/>Categories: " . $categ. "<br/>" . count($Qcomments).' Comments( <a href="'.itw_app_link('appExt=blog', 'show_post', $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_seo_url']).'#comments">click here to post a comment</a>)' . "</p>";
	}

	if ($app_pg == null){
		$contentHeading = "Blog";
	}else{
		$contentHeading = $blog->getCategoryHeaderTitle($app_pg);
	}

	if ($app_pg == null){
		$contentHeadingDesc = 'Below is a list of articles with the most recent ones listed first.';
	}else{
		$contentHeadingDesc = $blog->getCategoryHeaderDescription($app_pg);
	}

	$contentHtml .= "<br/><br/>".$pagerBar;

	$pageTitle = stripslashes($contentHeading);
	$pageContents = stripslashes($contentHtml);

	$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setHref(itw_app_link(null, 'index', 'default'))
	->draw();

	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
