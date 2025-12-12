<?php
$postid = -1;
if (get_option('page_for_posts') != 0) {
  $nowurl = $_SERVER[ 'REQUEST_URI' ];
  $nowurl = str_replace("/web_index", "", $nowurl);
  $pagedata = get_page(get_option('page_for_posts'));
  if (strstr($nowurl, $pagedata->post_name)) {
    $postid = get_option('page_for_posts');
  } else {
    $postid = $post->ID;
  }
} else {
  $postid = $post->ID;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<!-- Google Tag Manager -->
	<script>
		(function(w, d, s, l, i) {
			w[l] = w[l] || [];
			w[l].push({
				'gtm.start': new Date().getTime(),
				event: 'gtm.js'
			});
			var f = d.getElementsByTagName(s)[0],
				j = d.createElement(s),
				dl = l != 'dataLayer' ? '&l=' + l : '';
			j.async = true;
			j.src =
				'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
			f.parentNode.insertBefore(j, f);
		})(window, document, 'script', 'dataLayer', 'GTM-KM9L356');
	</script>
	<!-- End Google Tag Manager -->
	<meta charset="UTF-8">
	<?php if (preg_match("/Android|iPhone/", $_SERVER[ "HTTP_USER_AGENT" ])) : ?>
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, shrink-to-fit=no">
	<?php else: ?>
	<meta name="viewport" content="width=1200">
	<?php endif; ?>
	<meta name="format-detection" content="telephone=no" />
	<title><?php echo do_shortcode('[ISS-ttl data_id='.$postid.']'); ?></title>
	<meta name="keywords" content="<?php echo do_shortcode('[ISS-key data_id='.$postid.']'); ?>">
	<meta name="description" content="<?php echo do_shortcode('[ISS-desc data_id='.$postid.']'); ?>">
	<link rel="icon" type="image/png" href="<?php echo get_template_directory_uri();?>/images/favicon.png" sizes="64x64">
	<?php wp_head();?>
	<?php if(is_front_page()):?>
	<link href="<?php echo get_theme_file_uri();?>/images/concentration_line_pc.png" as="image" rel="preload">
	<link href="<?php echo get_theme_file_uri();?>/images/concentration_line_sp.png" as="image" rel="preload">
	<?php endif;?>
</head>

<body>
	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KM9L356" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
	<div class="bg_dot"></div>
	<header class="header">
		<h1><?php echo do_shortcode('[ISS-h1 data_id='.$postid.']'); ?></h1>
		<div class="h-logo"><a href="<?php echo link_by_path();?>"><img width="224" height="65" src="<?php echo get_template_directory_uri();?>/images/share/logo.png" alt="高瀬歯科"></a></div>
		<div class="humberger"><img width="208" height="208" src="<?php echo get_template_directory_uri();?>/images/share/humberger.png" alt="メニューを開く"></div>
		<div class="g-navi-dg"></div>
		<nav class="g-navi">
			<div class="g-navi-close"></div>
			<div class="g-navi-contents">
				<ul class="g-navi-list">
					<li><a href="<?php echo link_by_path('clinic');?>">高瀬歯科について</a></li>
					<li><a href="<?php echo link_by_path('message');?>">歯科治療を有意義なものにするために</a></li>
					<li><a href="<?php echo link_by_path('staff');?>">スタッフ紹介</a></li>
					<li class="dropdown" ontouchstart="">
						<a class="no-link">治療について</a>
						<div class="child">
							<ul>
								<li><a href="<?php echo link_by_path('treatment');?>">治療について</a></li>
								<li><a href="<?php echo link_by_path('treatment/general');?>">一般歯科</a></li>
								<li><a href="<?php echo link_by_path('treatment/preventive');?>">予防歯科</a></li>
								<li><a href="<?php echo link_by_path('treatment/child');?>">小児歯科</a></li>
								<li><a href="<?php echo link_by_path('treatment/cosmetic');?>">審美歯科</a></li>
								<!--<li><a href="<?php echo link_by_path('treatment/implant');?>">インプラント</a></li>-->
								<li><a href="<?php echo link_by_path('treatment/denture');?>">義歯</a></li>
								<!--<li><a href="<?php echo link_by_path('treatment/wisdom');?>">口腔外科</a></li>-->
								<li><a href="<?php echo link_by_path('treatment/periodontal-disease');?>">歯周病治療</a></li>
								<li><a href="<?php echo link_by_path('treatment/clench');?>">食いしばり</a></li>
							</ul>
						</div>
					</li>
					<li><a href="<?php echo link_by_path('facility');?>">設備・院内紹介</a></li>
					<li><a href="<?php echo link_by_path('access');?>">医院案内・アクセス</a></li>
					<li><a href="<?php echo link_by_path('news');?>">お知らせ</a></li>
					<li><a href="<?php echo link_by_path("blog","custom"); ?>">ブログ</a></li>
					<li><a href="<?php echo link_by_path("links","custom"); ?>">リンク集</a></li>
					<li><a href="<?php echo link_by_path('covid-19');?>">感染症対策</a></li>
				</ul>
				<div class="g-navi-other">
					<a href="https://www.takase-shika.com/senior-citizen/" target="_blank" class="g-navi-btn"><i><img loading="lazy" width="47" height="47" src="<?php echo get_template_directory_uri();?>/images/share/ico_pc.png" alt=""></i><em>50代からの歯科治療</em>特設サイト</a>
					<a href="https://www.instagram.com/takase_shika" target="_blank" class="g-navi-btn"><i><img loading="lazy" width="47" height="47" src="<?php echo get_template_directory_uri();?>/images/share/ico_pc.png" alt=""></i><em>高瀬歯科</em>インスタグラム</a>					
					<a href="http://www.haisyano489.ne.jp/takase-shika/" target="_blank" class="g-navi-btn"><i><img loading="lazy" width="47" height="47" src="<?php echo get_template_directory_uri();?>/images/share/ico_pc.png" alt=""></i><em>PC・スマホで簡単</em>ネット予約</a>
					<a href="tel:0298790082" class="g-navi-tel">Tel.029-879-0082</a>
					<p class="g-navi-time"><span class="blue">電話受付時間</span> 10：00~19：30<br>※13:00~15:00はお昼休みです。</p>
				</div>
			</div>
		</nav>
		<div class="sp-fixed-menu">
			<a href="tel:0298790082">Tel.029-879-0082</a>
			<a href="https://www.haisyano489.ne.jp/takase-shika/pc/" target="_blank">簡単ネット予約</a>
		</div>
	</header>
	<?php if (!is_front_page()): ?>
	<div class="page-ttl">
		<h2><?php do_action('page_title') ?></h2>
	</div>
	<?php if (function_exists('bcn_display')): ?>
	<nav class="breadcrumb">
		<ul>
			<?php bcn_display_list();?>
		</ul>
	</nav>
	<?php endif; endif;?>
