<?php
	$post_date = $_POST['post_date'];

	if(tep_not_null($post_date) && $post_date != "Not Set"){
		//$post_date = (date('Y-m-d') < $post_date) ? $post_date : date('Y-m-d');
	}else{
		$post_date = date('Y-m-d');
	}


	$Posts = Doctrine_Core::getTable('BlogPosts');
	if (isset($_GET['pID'])){
		$Post = $Posts->findOneByPostId((int)$_GET['pID']);
	}elseif (isset($_POST['post_id'])){
		$Post = $Posts->findOneByPostId((int)$_POST['post_id']);
	}else{
		$Post = $Posts->create();
		
		$Post->BlogPostToCategories[0]['blog_categories_id'] = $current_blog_category_id;
	}

	$Post->post_date = $post_date;
	$Post->post_status = $_POST['post_status'];
	$Post->post_featured_media = $_POST['post_featured_media'];
	$Post->post_featured_image = $_POST['post_featured_image'];
	$Post->post_full_featured_image = $_POST['post_full_featured_image'];
	$Post->post_redirect_url = $_POST['post_redirect_url'];

	$PostsDescription = $Post->BlogPostsDescription;
	foreach (sysLanguage::getLanguages() as $lInfo) {
		$lang_id = $lInfo['id'];

		$PostsDescription[$lang_id]->language_id = $lang_id;
		$PostsDescription[$lang_id]->blog_post_title = isset($_POST['blog_post_title'][$lang_id])?$_POST['blog_post_title'][$lang_id]:'No Title';
		$PostsDescription[$lang_id]->blog_post_text = isset($_POST['blog_post_text'][$lang_id])?$_POST['blog_post_text'][$lang_id]:'';
		//$PostsDescription[$lang_id]->blog_post_seo_url = tep_friendly_seo_url($PostsDescription[$lang_id]->blog_post_title);

		$PostsDescription[$lang_id]->blog_post_head_title = isset($_POST['blog_post_head_title'][$lang_id])?$_POST['blog_post_head_title'][$lang_id]:$_POST['blog_post_title'][$lang_id];
		$PostsDescription[$lang_id]->blog_post_head_desc =  isset($_POST['blog_post_head_desc'][$lang_id])? $_POST['blog_post_head_desc'][$lang_id]:$_POST['blog_post_title'][$lang_id];
		$PostsDescription[$lang_id]->blog_post_head_keywords = isset($_POST['blog_post_head_keywords'][$lang_id])? $_POST['blog_post_head_desc'][$lang_id]:$_POST['blog_post_title'][$lang_id];

        if (empty($_POST['blog_post_seo_url'][$lang_id])){
			$PostsDescription[$lang_id]->blog_post_seo_url = tep_friendly_seo_url($PostsDescription[$lang_id]->blog_post_title);
		}else{
	        $PostsDescription[$lang_id]->blog_post_seo_url = $_POST['blog_post_seo_url'][$lang_id];
        }
        
		if (!empty($_POST['blog_post_head_title'][$lang_id])){
			$PostsDescription[$lang_id]->blog_post_head_title = $_POST['blog_post_head_title'][$lang_id];
		}

		if (!empty($_POST['blog_post_head_desc'][$lang_id])){
			$PostsDescription[$lang_id]->blog_post_head_desc = $_POST['blog_post_head_desc'][$lang_id];
		}

		if (!empty($_POST['blog_post_head_keywords'][$lang_id])){
			$PostsDescription[$lang_id]->blog_post_head_keywords = $_POST['blog_post_head_keywords'][$lang_id];
		}
	}


	
	$PostsToCategories = $Post->BlogPostToCategories;
	$PostsToCategories->delete();
	if (isset($_POST['blog_categories'])){
		foreach($_POST['blog_categories'] as $categoryId){
			$PostsToCategories[]->blog_categories_id = $categoryId;
		}
	}
	$Post->save();


	$link = itw_app_link(tep_get_all_get_params(array('action', 'pID')) . 'pID=' . $Post->post_id, null, 'default');

	EventManager::attachActionResponse($link, 'redirect');
?>