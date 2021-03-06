<?php
$site_id = get_post_meta(get_the_ID(), 'site_id', true);
$decoration_id = get_post_meta(get_the_ID(), 'decoration', true);
$frames = json_decode(get_post_meta(get_the_ID(), 'frames', true));
$frame_types = json_decode(get_option('frame_types'));

$decoration_tags = wp_get_post_tags($decoration_id, array('fields'=>'names'));
if($_GET['step'] === 'picture' && !in_array('画面', $decoration_tags)){
	header('Location: ' . get_the_permalink(get_the_ID()) . '?action=result-upload');
}

$unreceived = array('frames'=>0, 'pictures'=>0);

foreach($frames as $name => $frame){
	if(!$frame->received){
		$unreceived['frames']++;
	}
	foreach($frame->pictures as $picture){
		if(!$picture->received){
			$unreceived['pictures']++;
		}
	}
}
// TODO 需要验证身份
// TODO 并发勾选时，数据库中frames meta项的存取可能发生交错，导致脏数据保存
if($_SERVER['REQUEST_METHOD'] === 'POST' && (empty($_GET['action']) || $_GET['action'] === 'recept-confirmation')){
	
	if(isset($_POST['frame_received'])){
		if(is_array($_POST['frame_received'])){
			foreach($_POST['frame_received'] as $name => $received){
				$received = json_decode($received);
				$frames->$name->received = $received;
				$received ? $unreceived['frames'] -- : $unreceived['frames'] ++;
			}
			update_post_meta(get_the_ID(), 'frames', json_encode($frames, JSON_UNESCAPED_UNICODE));
		}
		else{
			update_post_meta(get_the_ID(), 'frames_received', json_decode($_POST['frame_received']));
		}
	}
	
	if(isset($_POST['picture_received'])){
		if(is_array($_POST['picture_received'])){
			foreach($_POST['picture_received'] as $frame_name => $received){
				$received = json_decode($received);
				$frames->$frame_name->pictures_received = $received;
				foreach($frames->$frame_name->pictures as &$picture){
					$picture->received = $received;
					$received ? $unreceived['pictures'] -- : $unreceived['pictures'] ++;
				}
			}
			update_post_meta(get_the_ID(), 'frames', json_encode($frames, JSON_UNESCAPED_UNICODE));
		}
		else{
			update_post_meta(get_the_ID(), 'pictures_received', json_decode($_POST['picture_received']));
		}
	}
	
	header('Content-Type: application/json');
	echo json_encode($unreceived);
	
	exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'result-upload'){
	
	include_once ABSPATH . 'wp-admin/includes/media.php';
	include_once ABSPATH . 'wp-admin/includes/file.php';
	include_once ABSPATH . 'wp-admin/includes/image.php';
	
	$result_photos = json_decode(get_post_meta(get_the_ID(), 'result_photos', true));
	!$result_photos && $result_photos = new stdClass();
	
	foreach($_FILES as $index => $file){
		$attachment_id = media_handle_upload($index, 0);
		if(is_integer($attachment_id)){
			$result_photos->$index = $attachment_id;
		}
	}
	
	update_post_meta(get_the_ID(), 'result_photos', json_encode($result_photos, JSON_UNESCAPED_UNICODE));
	
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	exit;
}

get_header();

if(empty($_GET['action']) || $_GET['action'] === 'recept-confirmation'){
	require get_template_directory() . '/recept-confirmation.php';
}elseif($_GET['action'] === 'result-upload'){
	require get_template_directory() . '/result-upload.php';
}elseif($_GET['action'] === 'result'){
	require get_template_directory() . '/site-result.php';
}

get_footer();
