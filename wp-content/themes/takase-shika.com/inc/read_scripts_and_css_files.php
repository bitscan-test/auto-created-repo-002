<?php

function add_filedate($filename)
{
  $file_path = get_template_directory().'/'.$filename;
  if (file_exists($file_path)) {
    $file_url = get_template_directory_uri().'/'.$filename;
    $put_query = date('ymdHis', filemtime($file_path));
    $file_href_output = $file_url.'?'.$put_query;
    return $file_href_output;
  }
}

/*
  read_scripts_and_css_files_file_name
    スクリプトとスタイルシートの読み込み
*/
function read_scripts_and_css_files()
{

  wp_enqueue_script('lazysizes', get_template_directory_uri().'/js/lazysizes.min.js', array(), null);
  // Wordpressが読み込むjQueryをリセット
  // if (!is_admin()) {
  //   wp_deregister_script('jquery');
  //   wp_enqueue_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js', array(), null);
  // }
  if(is_page(['staff', 'facility'])){
  	wp_enqueue_script('slick', get_template_directory_uri().'/js/slick.min.js', array('jquery-core'), null);
  }

  wp_enqueue_script('ScrollMagic', get_theme_file_uri().'/js/ScrollMagic.min.js', array('jquery-core'), null);
  // wp_enqueue_script('ScrollMagicDebug', '//cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.7/plugins/debug.addIndicators.min.js', array('jquery'), null, true);

  // wp_enqueue_script('stickyfill', get_template_directory_uri().'/js/stickyfill.min.js', array('jquery-core'), null);
  wp_enqueue_script('behavior', get_template_directory_uri().'/js/behavior.js', array('jquery-core'), null);

//  if (is_page('contact')) {
//    wp_enqueue_script('formUI', get_template_directory_uri().'/js/formUI.js', array('jquery'), null, true);
//  }
	//Chrome CSS Transiton bug対策
	wp_add_inline_script('jquery-core', 'console.log()');

  wp_enqueue_style('theme', get_template_directory_uri().'/css/theme.css', null, 'all');
  wp_enqueue_style('style', add_filedate('css/style.css'), null, 'all');
}

add_action('wp_enqueue_scripts', 'read_scripts_and_css_files');

//----------------------------------------------
// gutenbergのcssを無効化
//----------------------------------------------
function remove_gutenberg_styles() {
	wp_dequeue_style( 'wp-block-library' );
}
add_action( 'wp_enqueue_scripts', 'remove_gutenberg_styles', 100 );


add_filter( 'script_loader_tag', function ($tag, $handle) {
    if( is_admin() ) return $tag;
    if( !preg_match( '/¥b(async|defer)¥b/', $tag ) ) {
        return str_replace( ' src', ' defer src', $tag );
    }
    return $tag;
} , 10, 2 );
