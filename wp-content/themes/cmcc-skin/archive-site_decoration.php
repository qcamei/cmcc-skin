<?php
/*
 * 获得当前用户所在营业厅的营业厅换装，并列表
 */
$wx = new WeixinAPI();

$auth_info = $wx->get_oauth_info();
$users = get_users(array('meta_key'=>'wx_openid','meta_value'=>$auth_info->openid));

if(!$users){
	$query_args = array(
		'access_token'=>$auth_info->access_token,
		'forward_to'=>urlencode_deep(current_url())
	);
	header('Location: ' . site_url() . '/site-signup/?' . build_query($query_args));
	exit;
}

$user_id = $users[0]->ID;
$site_id = get_user_meta($user_id, 'site');
$site_decorations = get_posts(array('post_type'=>'site_decoration', 'posts_per_page'=>-1, 'meta_key'=>'site_id', 'meta_value'=>$site_id));
get_header(); ?>

<header>
	<h1>
		<?php if($_GET['action'] === 'result-upload'){ ?>结果上传<?php }else{ ?>
		<?php	if(isset($_GET['decoration_tag'])){ echo $_GET['decoration_tag'] === '画面' ? '换装' : '物料'; }?>
		<?php	if(isset($_GET['action']) && $_GET['action'] === 'recept-confirmation'){ ?>签收<?php }else{ ?>发布<?php } ?>
		<?php } ?>
	</h1>
</header>

<table class="table table-bordered detail summary">
	<tbody>
		<?php foreach($site_decorations as $site_decoration){ ?>
		<?php if(isset($_GET['decoration_tag']) && !in_array($_GET['decoration_tag'], wp_get_post_tags(get_post_meta($site_decoration->ID, 'decoration', true), array('fields'=>'names')))) continue;?>
		<tr>
			<td><a href="<?php if(isset($_GET['action']) && $_GET['action'] === 'recept-confirmation'){ ?><?=get_the_permalink($site_decoration->ID)?>?action=recept-confirmation&step=<?=$_GET['step']?><?php }elseif(isset($_GET['action']) && $_GET['action'] === 'result-upload'){ ?><?=get_the_permalink(get_the_ID())?>?action=result-upload<?php }else{ ?><?=get_the_permalink(get_post_meta($site_decoration->ID, 'decoration', true))?><?php } ?>"><?=get_post(get_post_meta($site_decoration->ID, 'decoration', true))->post_title?></a></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php get_footer(); ?>
