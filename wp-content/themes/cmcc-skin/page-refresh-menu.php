<?php
/**
 *  访问这个页面，会删除现有微信自定义菜单，并重建
 */
$wx = new WeixinAPI();

$wx->remove_menu();

$data = array(
	'button'=>array(
		array(
			'name'=>'通知',
			'sub_button'=>array(
				array(
					'name'=>'换装发布',
					'type'=>'view',
					'url'=>site_url() . '/site_decoration/?decoration_tag=' . urlencode('画面')
				),
				array(
					'name'=>'物料下发',
					'type'=>'view',
					'url'=>site_url() . '/site_decoration/?decoration_tag=' . urlencode('器架')
				),
			)
		),
		array(
			'name'=>'签收',
			'sub_button'=>array(
				array(
					'type'=>'view',
					'name'=>'换装确认',
					'url'=>site_url() . '/site_decoration/?decoration_tag=' . urlencode('画面') . '&action=recept-confirmation&step=picture',
				),
				array(
					'type'=>'view',
					'name'=>'物料签收',
					'url'=>site_url() . '/site_decoration/?decoration_tag=' . urlencode('器架') . '&action=recept-confirmation&step=frame',
				),
				array(
					'type'=>'view',
					'name'=>'实景图上传',
					'url'=>site_url() . '/site_decoration/?action=result-upload'
				),
			)
		),
		array(
			'name'=>'汇总',
			'sub_button'=>array(
				array(
					'name'=>'汇总',
					'type'=>'view',
					'url'=>site_url() . '/decoration/?action=result',
				),
				array(
					'name'=>'报障',
					'type'=>'click',
					'key'=>'ERROR_REPORT',
				),
			),
		),
	)
);

$wx->create_menu($data);

header('Content-Type: application/json');
echo json_encode($wx->get_menu());