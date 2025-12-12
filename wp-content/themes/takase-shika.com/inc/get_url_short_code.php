<?php
/*
  記事内やテンプレート内でURLを返す関数
*/

// ■URLを返すショートコード
// ＜使い方＞
// 　投稿と固定ページ  ：[get_url slug=menu]  ※'menu'のところにslugを書く
// 　投稿と固定ページ  ：[get_url path=menu]  ※'menu'のところにパスを書く
// 　投稿と固定ページ  ：[get_url id=1]  ※数字はページID
// 　カスタム投稿アーカイブ  ：[get_url post_type=menu]  ※'menu'のところにカスタム投稿タイプを書く
// 　カテゴリー    ：[get_url type=cat id=1]  ※数字はカテゴリーID
// 　カテゴリー    ：[get_url type=cat slug=staff]  ※slugをカテゴリーのURLに
// 　カスタムポストのカテゴリー（タクソノミー）：[get_url type=tax id=1 tax_name=blog_category]
// 　カスタムポストのカテゴリー（タクソノミー）：[get_url type=tax term_name=cat-1 tax_name=blog_category]
// 　ファイルへのリンク：[get_url type=file name=filename suffix=jpg]  ※filenameはファイル名、suffixはjpg
function get_url_short_code( $attr ) {

  $url = esc_url(get_home_url());

  if ( isset($attr['type']) ) {
    if ( $attr['type'] == 'cat' ) {
      if(isset($attr['slug'])){
        $get_cat = get_category_by_slug($attr['slug']);
        $cat_id = $get_cat->cat_ID;
      }elseif(isset($attr['id'])){
        $cat_id = $attr['id'];
      }else{
        return false;
      }

      $url = get_category_link($cat_id);
    } else if ( $attr['type'] == 'tax' ) {
      if ( isset($attr['id']) && isset($attr['tax_name']) ) {
        $url = link_by_term( (int)$attr['id'], $attr['tax_name'] );
      } else if ( isset($attr['term_name']) && isset($attr['tax_name']) ) {
        $url = link_by_term( $attr['term_name'], $attr['tax_name'] );
      }
    } else if ( $attr['type'] == 'file' ) {
      if ( isset($attr['name']) && isset($attr['suffix']) ) {
        $url = get_template_directory_uri().'/'.$attr['name'].'.'.$attr['suffix'];
      }
    } else if ( $attr['type'] == 'temp_dir' ) {
      $url = get_template_directory_uri();
    }
  } else {
    if (isset($attr['path'])) {
      $url = link_by_path($attr['path']);
    } else if (isset($attr['slug'])) {
      $url = link_by_path($attr['slug']);
    } else if (isset($attr['post_type'])) {
      $url = link_by_path($attr['post_type'],'custom');
    } else if (isset($attr['id'])) {
      $url = get_permalink($attr['id']);
    }
  }

  return esc_url($url);

}
add_shortcode('get_url', 'get_url_short_code');

/*
  テンプレート内でURLを返す関数
*/
// 固定ページ
// echo link_by_path('clinic');
// パスを指定します。親記事がある場合は親から指定します。（例：about/clinic）
//
// カスタム投稿の一覧
// echo link_by_path('case','custom');
// 一つ目にポストタイプを記入、二つ目にcustomと書きます。

function link_by_path($path = NULL, $type = NULL){

  if($path === NULL){
    $url = esc_url(home_url('/'));
    return $url;
  }

  if ( $type == 'custom' ) {
    $url = esc_url(get_post_type_archive_link($path));
    if ( $url == FALSE ) {
      $url = esc_url(get_home_url());
    }
  } else {
    $page_obj = get_page_by_path($path);
    if ( $page_obj != NULL ) {
      $page_id = $page_obj->ID;
      $url = esc_url(get_permalink($page_id));
    } else {
      $url = esc_url(get_home_url());
    }
  }
  return $url;

}

/*
  テンプレート内でURLを返す関数
*/
// カテゴリー（タクソノミー）
// echo link_by_term('voice','category');
// 一つ目にスラッグ、二つ目にタクソノミーのスラッグ。

function link_by_term($term, $tax){

  $result = get_term_link( $term, $tax );
  if ( is_object($result) ) {
    $url = esc_url(get_home_url());
  } else {
    $url = esc_url($result);
  }
  return $url;

}
