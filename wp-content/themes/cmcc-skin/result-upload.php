<?php
/*
 * Sub Template in Single "site_decoration"
 */
$result_positions = json_decode(get_option('result_upload_positions'));
$result_photos = json_decode(get_post_meta(get_the_ID(), 'result_photos', true));
?>
<div class="result-upload">
	<form method="post" enctype="multipart/form-data">
		<?php foreach($result_positions as $slug => $name){ ?>
		<div class="row">
			<div class="col-xs-12">
				<h2><?=$name?></h2>
				<?php if(isset($result_photos->$slug) && wp_get_attachment_image($result_photos->$slug, 'large')){ ?>
				<?=wp_get_attachment_image($result_photos->$slug, 'large')?>
				<div class="upload-file" style="display:none">
					<span class="fa fa-edit"></span>
					<span>更换照片</span>
					<input type="file" name="<?=$slug?>" capture="camera">
				</div>
				<?php }else{ ?>
				<img class="preview empty" />
				<div class="upload-file">
					<span class="fa fa-plus"></span>
					<span>上传照片</span>
					<input type="file" name="<?=$slug?>" capture="camera">
				</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
		<div class="form-actions">
			<button type="submit" class="btn btn-success">上传</button>
			<?php if(count((array) $result_photos) >= 9){ ?>
			<a href="<?php the_permalink(); ?>?action=result" class="btn btn-success">预览</a>
			<?php } ?>
		</div>
	</form>
</div>
<script type="text/javascript">
jQuery(function($){
	
	$('.result-upload form input[type="file"]').change(function(){
		
		var input = $(this);
		
		if (this.files && this.files[0]) {
			var reader = new FileReader();
			
			reader.onload = function (e) {
				input.parent('.upload-file').siblings('img').attr('src', e.target.result).removeClass('empty');
			}
			
			reader.readAsDataURL(this.files[0]);
		}
	});
	
	$('.attachment-large').on('click', function(){
		console.log($(this).siblings('.upload-file').get(0));
		$(this).siblings('.upload-file').toggle();
	});
	
});
</script>