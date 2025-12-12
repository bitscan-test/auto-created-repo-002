<?php
/*
  抜粋
*/
// the_excerpt（本文の抜粋をするWordpress関数）の表示設定
function new_excerpt_mblength($length) {
  // 抜粋の長さ
  return 130;
}
add_filter('excerpt_more', 'new_excerpt_more');

function new_excerpt_more($more) {
  // 抜粋時に表示される文字列
  return '...';
}
add_filter('excerpt_mblength', 'new_excerpt_mblength');

// 任意のテキストの抜粋を行う
function get_my_excerpt($text, $length = 200){
  if ( $length > 0 ) {
    $text = str_replace(array("\r\n", "\r", "\n", "  "), "", $text);
    $text = str_replace(array("\""), "", $text);
    $text = trim(strip_tags($text));
    $ellipsis = '...';

	return wp_html_excerpt($text, $length,$ellipsis);
  } else {
    return $text;
  }
}
