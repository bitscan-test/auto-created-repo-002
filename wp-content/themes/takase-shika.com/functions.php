<?php
// ファイルディレクトリ
$theme_inc_dir = dirname(__FILE__).'/inc/';

// 各サイト共通の処理
$common_functions_file_name = $theme_inc_dir.'common_functions.php';
if ( is_file( $common_functions_file_name ) ) {
  require_once($common_functions_file_name);
}

// 記事内やテンプレート内でURLを返す関数
$get_url_short_code_file_name = $theme_inc_dir.'get_url_short_code.php';
if ( is_file( $get_url_short_code_file_name ) ) {
  require_once($get_url_short_code_file_name);
}

// ページング
$cms_pagination_file_name = $theme_inc_dir.'cms_pagination.php';
if ( is_file( $cms_pagination_file_name ) ) {
  require_once($cms_pagination_file_name);
}

// タクソノミーの出力
$disp_taxonomy_list_file_name = $theme_inc_dir.'disp_taxonomy_list.php';
if ( is_file( $disp_taxonomy_list_file_name ) ) {
  require_once($disp_taxonomy_list_file_name);
}

// タイトル文字の出力
$disp_page_title_file_name = $theme_inc_dir.'disp_page_title.php';
if ( is_file( $disp_page_title_file_name ) ) {
  require_once($disp_page_title_file_name);
}

// スクリプト、スタイルシートの読み込み
$read_scripts_and_css_files_file_name = $theme_inc_dir.'read_scripts_and_css_files.php';
if ( is_file( $read_scripts_and_css_files_file_name ) ) {
  require_once($read_scripts_and_css_files_file_name);
}

// 画像サイズ設定
$baw_theme_setup_file_name = $theme_inc_dir.'thumb_setup.php';
if ( is_file( $baw_theme_setup_file_name ) ) {
  require_once($baw_theme_setup_file_name);
}

// 抜粋
$create_post_type_file_name = $theme_inc_dir.'new_excerpt.php';
if ( is_file( $create_post_type_file_name ) ) {
  require_once($create_post_type_file_name);
}

// 日付書式設定
$date_type_file_name = $theme_inc_dir.'date_type.php';
if ( is_file( $date_type_file_name ) ) {
  require_once($date_type_file_name);
}


// 新着記事、ループする記事の出力
$news_list_file_name = $theme_inc_dir.'news_list.php';
if ( is_file( $news_list_file_name ) ) {
  require_once($news_list_file_name);
}

// ショートコード登録
$shortcode_file_name = $theme_inc_dir.'shortcode.php';
if ( is_file( $shortcode_file_name ) ) {
  require_once($shortcode_file_name);
}


// // カスタム投稿の追加
 $create_post_type_file_name = $theme_inc_dir.'add-functions/create_post_type.php';
 if ( is_file( $create_post_type_file_name ) ) {
   require_once($create_post_type_file_name);
 }
