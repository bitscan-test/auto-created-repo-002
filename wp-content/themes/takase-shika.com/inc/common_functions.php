<?php
//----------------------------------------------
// プラグインの自動更新を有効化
// メジャーアップグレードの自動更新を有効化
//----------------------------------------------
add_filter( 'auto_update_plugin', '__return_true' );
add_filter( 'allow_major_auto_core_updates', '__return_true' );

//----------------------------------------------
// WordPressの出力内容を制限
//----------------------------------------------
remove_action('wp_head','wp_generator');//wordpressのヴァージョンを表示する
remove_action('wp_head', 'rsd_link');//ブログ投稿ツールを使う場合は必要
remove_action('wp_head', 'wlwmanifest_link');//Windows Live Writer投稿用
remove_action('wp_head', 'feed_links_extra', 3);//その他のフィード（カテゴリー等）へのリンクを表示
remove_action('wp_head', 'index_rel_link' );//現在の文書に対する索引（インデックス）
remove_action('wp_head', 'parent_post_rel_link', 10, 0 );//link rel="parent"
remove_action('wp_head', 'start_post_rel_link', 10, 0 );//link rel="next"
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0 );
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0 );//?p=投稿ID


//----------------------------------------------
//　タグ（post_tag）を無効に
//----------------------------------------------
function remove_default_post_tag()
{
  global $pagenow;
  register_taxonomy('post_tag', []);
  if ($pagenow == 'edit-tags.php' && isset($_GET['taxonomy']) && strpos($_GET['taxonomy'], 'post_tag') !== false) {
    wp_die('Invalid taxonomy');
  }
}
add_action('init', 'remove_default_post_tag');

/*
xmlrpcを使用する場合は、.htaccessも対応する
*/

/*
 * REST APIを無効にする
 * https://nendeb.com/541
 * License: GPLv2 or later
 */
function deny_restapi_except_embed( $result, $wp_rest_server, $request ){

  $namespaces = $request->get_route();

  // /oembed/1.0
  if( strpos( $namespaces, 'oembed/' ) === 1 ){
    return $result;
  }

  //Gutenberg (Ver4.9?～)
  if ( current_user_can( 'edit_posts' ) ) {
    return $result;
  }

  return new WP_Error( 'rest_disabled', __( 'The REST API on this site has been disabled.' ), array( 'status' => rest_authorization_required_code() ) );
}
add_filter( 'rest_pre_dispatch', 'deny_restapi_except_embed', 10, 3 );


//----------------------------------------------
//　編集画面のタブ幅を変更
//----------------------------------------------
function add_admin_custom_style() {
  echo '<style>
	.wp-editor-container textarea {
 	-o-tab-size:2;
 	-moz-tab-size:2;
 	tab-size:2;
 }
 </style>';
}
add_action( 'admin_head', 'add_admin_custom_style' );

//----------------------------------------------
//　固定ページの改行等を制御
//----------------------------------------------
function rm_wpautop($content) {
  global $post;
  // Get the keys and values of the custom fields:
  if(preg_match('|<!--handmade-->|siu',$content) || is_page()){
    remove_filter('the_content', 'wpautop');
  } else {
    add_filter('the_content', 'wpautop');
  }
  return $content;
}
// Hook into the Plugin API
add_filter('the_content', 'rm_wpautop', 9);

//----------------------------------------------
// フッターテキスト変更（左）
//----------------------------------------------
function custom_footer_admin () {
  return '<a href="http://www.zeromedical.tv/">株式会社ゼロ・メディカル</a>&nbsp;-ZERO MEDICAL-';
}
add_filter( 'admin_footer_text', 'custom_footer_admin' );

//----------------------------------------------19/01/09追記
//　先頭固定表示のチェックボックスを非表示
//----------------------------------------------
//投稿一覧ページの先頭固定表示のチェックボックスを非表示
function postlist_quick_hidden_sticky_check_box() {
?>
<script type="text/javascript">
  jQuery(document).ready(function($){
    $(".inline-edit-col-right .inline-edit-group:eq(1) label:eq(1)").css("display","none");
  });
</script>
<?php
}
add_action( 'admin_head-edit.php', 'postlist_quick_hidden_sticky_check_box' );
//投稿詳細ページの先頭固定表示のチェックボックスを非表示
function postsingle_hidden_sticky_check_box() {
  echo '
    <style type="text/css">
        #sticky-span{display:none !important;}
    </style>
    ';
}
add_action( 'admin_print_styles-post.php', 'postsingle_hidden_sticky_check_box' );


//----------------------------------------
// フォントサイズボタン追加
//----------------------------------------
function ilc_mce_buttons( $buttons ) {
  array_push( $buttons, "fontsizeselect" );
  return $buttons;
}
add_filter( "mce_buttons", "ilc_mce_buttons" );

//----------------------------------------
// 不要ボタン非表示（ビジュアルエディタ１行目）
//----------------------------------------
function tinymce_delete_buttons( $array ) {
  $array = array_diff( $array, array( 'strikethrough', 'blockquote', 'hr', 'wp_more' ) );
  return $array;
}
add_filter( 'mce_buttons', 'tinymce_delete_buttons' );

//----------------------------------------
// 不要ボタン非表示（ビジュアルエディタ２行目）
//----------------------------------------
function tinymce_delete_buttons2( $array ) {
  $array = array_diff( $array, array( 'formatselect', 'pastetext', 'removeformat', 'charmap' ) );
  return $array;
}
add_filter( 'mce_buttons_2', 'tinymce_delete_buttons2' );

//----------------------------------------
// 不要ボタン非表示（テキストエディタ）
//----------------------------------------
function et_print_styles() {
  echo '<style TYPE="text/css">
  #qt_content_block,
  #qt_content_del,
  #qt_content_link,
  #qt_content_ins,
  #qt_content_img,
  #qt_content_code,
  #qt_content_more,
  #qt_content_close {
    display:none !important;
  }
  </style>';
}
add_action( 'admin_print_styles', 'et_print_styles', 21 );

/*　アイキャッチをテーマで有効にする*/
add_theme_support('post-thumbnails');

function cms_the_image($post_id) {
  $images = get_children(array(
    'post_parent' => $post_id,
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'order' => 'ASC'));
  if(!empty($images)){
    return wp_get_attachment_url(array_pop(array_keys($images)));
  } else {
    return '';
  }
}

//----------------------------------------------
// 固定ページ内のimage呼び出しを補完
//----------------------------------------------
function replaceImagePath($arg) {
  $content = str_replace('"images/', '"' . get_template_directory_uri() . '/images/', $arg);
  //background-image: url(images/sec04_img01.jpg)のような文字列を変換、url()前後の半角スペース揺れも対応
  if(strpos($content, 'background-image')){
    $content = preg_replace('/url(| )\((| )images/i', 'url(' . get_template_directory_uri() . '/images/', $content);
  }
  return $content;
}
add_action('the_content', 'replaceImagePath');


//----------------------------------------------
// 固定ページのビジュアルモード非表示
//----------------------------------------------
function disable_visual_editor_in_page() {
  global $typenow;
  if( $typenow == 'page' || $typenow == 'mw-wp-form' ) {
    add_filter( 'user_can_richedit', 'disable_visual_editor_filter' );
  }
}
function disable_visual_editor_filter() {
  return false;
}
add_action( 'load-post.php', 'disable_visual_editor_in_page' );
add_action( 'load-post-new.php', 'disable_visual_editor_in_page' );

//----------------------------------------------
// カテゴリーの階層維持
//----------------------------------------------
function solecolor_wp_terms_checklist_args( $args, $post_id ) {
  if ( $args[ 'checked_ontop' ] !== false ) {
    $args[ 'checked_ontop' ] = false;
  }
  return $args;
}
add_filter( 'wp_terms_checklist_args', 'solecolor_wp_terms_checklist_args', 10, 2 );


// ウィジェット　自由エリア追加
// register_sidebar(
//   array(
//   'name'      => 'Free Area',
//   'id'      => 'freearea',
//   'description'   => '自由エリア',
//   'before_widget' => '<div id="%1$s" class="widget %2$s">',
//   'after_widget'  => '</div>',
//   'before_title'  => '<h2 class="widgettitle">',
//   'after_title'   => '</h2>'
//   )
// );

//----------------------------------------------
// ビジュアルエディタでptをpxに変更
//----------------------------------------------
function customize_tinymce_settings($array) {
  $array['fontsize_formats'] = '10px 12px 14px 16px 18px 24px 36px';
  return $array;
}
add_filter( 'tiny_mce_before_init', 'customize_tinymce_settings' );

//----------------------------------------------
// 見出しをh4とh5に制限
//----------------------------------------------
function custom_tiny_mce_block_formats( $settings ){
  $settings[ 'block_formats' ] = '段落=p;見出し4=h4;見出し5=h5;';
  return $settings;
}
add_filter( 'tiny_mce_before_init', 'custom_tiny_mce_block_formats' );


//----------------------------------------------
// 管理画面メニュー「投稿」テキスト変更
//----------------------------------------------
function edit_admin_menus() {
  global $menu;
  global $submenu;

  $menu[5][0] = '新着情報';
  $submenu['edit.php'][5][0] = '新着情報';
}
add_action( 'admin_menu', 'edit_admin_menus' );

//----------------------------------------------
// 編集者ユーザー権限を持たないユーザー（投稿者等）向けに表示を制限
//----------------------------------------------
if (!current_user_can('edit_users')) {
  function remove_menu() {
    remove_menu_page( 'edit-comments.php' ); // コメント
    remove_menu_page( 'link-manager.php' ); // リンク
    remove_menu_page( 'profile.php' ); // プロフィール
    remove_menu_page( 'tools.php' ); // ツール
    remove_menu_page( 'update-core.php' );
    remove_menu_page( 'plugins.php' );
    remove_menu_page( 'functions.php' );
  }
  add_action('admin_menu', 'remove_menu');
}

//----------------------------------------------
// タイトルを空白で登録した場合にタイトルなしで表示する
//----------------------------------------------
function if_empty_title( $title ) {
  if ( empty($title)) {
    $title = 'タイトルなし';
  }
  return $title;
}
add_filter( 'the_title', 'if_empty_title', 10, 2 );

//----------------------------------------------
// 	管理画面内のCSS設定
//----------------------------------------------
// function my_admin_style() {
// 	wp_enqueue_style('admin-styles', get_template_directory_uri().'/css/admin.css');
// }
// add_action('admin_print_styles', 'my_admin_style');


//----------------------------------------------
// 出力件数設定
//----------------------------------------------

function change_posts_per_page($query) {
	/* 管理画面,メインクエリに干渉しないために必須 */
	if ( is_admin() || ! $query->is_main_query() ){
		return;
	}

	if ( $query->is_post_type_archive('links')  ) {
		$query->set( 'posts_per_page', 10 );
		return;
	}
}
add_action( 'pre_get_posts', 'change_posts_per_page' );


//----------------------------------------------
// 管理画面の固定ページ一覧にスラッグを表示
//----------------------------------------------
function add_page_columns_name($columns) {
  $columns['slug'] = "スラッグ";
  return $columns;
}
function add_page_column($column_name, $post_id) {
  if( $column_name == 'slug' ) {
    $post = get_post($post_id);
    $slug = $post->post_name;
    echo esc_attr($slug);
  }
}
add_filter( 'manage_pages_columns', 'add_page_columns_name');
add_action( 'manage_pages_custom_column', 'add_page_column', 10, 2);

//----------------------------------------------
// get_post_typeの拡張ラッパー
//----------------------------------------------
function get_my_post_type(){
  $post_type = get_post_type();
  if($post_type){
    return $post_type;
  }
  if(is_tax()){
    $taxonomy = get_query_var( 'taxonomy' );
    $post_type = get_taxonomy( $taxonomy )->object_type[0];
    return $post_type;
  }
  if(is_category()){
    return 'post';
  }
  if(is_archive()){
    $post_type = get_query_var( 'post_type' );
    return $post_type;
  }
  return false;
}

//----------------------------------------------
// iframeを投稿者権限でも使えるように
//----------------------------------------------
add_filter('content_save_pre','iframe_save_pre');
function iframe_save_pre($content){
  global $allowedposttags;
  // iframeとiframeで使える属性を指定する
  $allowedposttags['iframe'] = array('class' => array () , 'src'=>array() , 'width'=>array(),
                                     'height'=>array() , 'frameborder' => array() , 'scrolling'=>array(),'marginheight'=>array(),
                                     'marginwidth'=>array());
  return $content;
}

//----------------------------------------------
// 添付ファイルページを無効にする
//----------------------------------------------
function cleanup_default_rewrite_rules( $rules ) {
  foreach ( $rules as $regex => $query ) {
    if ( strpos( $regex, 'attachment' ) || strpos( $query, 'attachment' ) ) {
      unset( $rules[ $regex ] );
    }
  }
  return $rules;
}
add_filter( 'rewrite_rules_array', 'cleanup_default_rewrite_rules' );

function disable_admin_attachment_select() {
  echo '<style>.media-sidebar .setting .link-to option[value="post"]{display: none;}</style>';
}
add_action('admin_head', 'disable_admin_attachment_select');

function cleanup_attachment_link( $link ) {
  return;
}
add_filter( 'attachment_link', 'cleanup_attachment_link' );

//----------------------------------------------
// サイト管理者のメールアドレスを定期的に確認する を無効にする
//----------------------------------------------
add_filter( 'admin_email_check_interval', '__return_zero' );


//----------------------------------------------
// authorページをリダイレクトさせない
//----------------------------------------------
function redirect_controle() {
  if( is_author() ) {
    wp_redirect( home_url());
    exit;
  }
  return;
}
add_action('template_redirect', 'redirect_controle');
//----------------------------------------------
// WP REST API対策
//----------------------------------------------
function my_filter_rest_endpoints( $endpoints ) {
  if ( isset( $endpoints['/wp/v2/users'] ) ) {
    unset( $endpoints['/wp/v2/users'] );
  }
  if ( isset( $endpoints['/wp/v2/users/(?P[\d]+)'] ) ) {
    unset( $endpoints['/wp/v2/users/(?P[\d]+)'] );
  }
  return $endpoints;
}
add_filter( 'rest_endpoints', 'my_filter_rest_endpoints', 10, 1 );

//----------------------------------------------
// 詳細ページの前後リンクにclassを付与
//-----------------------------------------------
add_filter( 'previous_post_link', 'add_prev_post_link_class' );
function add_prev_post_link_class($output) {
  return str_replace('<a href=', '<a class="prev" href=', $output);
}
add_filter( 'next_post_link', 'add_next_post_link_class' );
function add_next_post_link_class($output) {
  return str_replace('<a href=', '<a class="next" href=', $output);
}
