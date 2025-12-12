<?php
function create_post_type()  { 
  $labels0 = array(
    'name' => 'ブログ',
    'singular_name' => 'ブログ',
    'add_new' => 'ブログの追加',
    'add_new_item' => 'ブログを追加する',
    'edit_item' => 'ブログを編集する',
    'new_item' => '新しいブログ',
    'view_item' => 'ブログ表示',
    'search_items' => 'ブログ検索',
    'not_found' => 'ブログが見つかりません',
    'not_found_in_trash' => 'ゴミ箱にブログはありません',
  );
  $args0 = array(
    'labels' => $labels0,
    'public' => true,
    'has_archive' => true,
    'menu_position' => 5,
    'rewrite' => true,
    'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'revisions', 'page-attributes' ),
  );
  register_post_type( 'blog', $args0 );
  // category
  $tax_args0 = array(
    'label' => 'ブログのカテゴリー',
    'hierarchical' => true,
    'rewrite' => array( 'slug' => 'blog_category' ),
  );
  register_taxonomy( 'blog_category', array( 'blog' ), $tax_args0 );
  $labels1 = array(
    'name' => 'リンク集',
    'singular_name' => 'リンク集',
    'add_new' => 'リンク集の追加',
    'add_new_item' => 'リンク集を追加する',
    'edit_item' => 'リンク集を編集する',
    'new_item' => '新しいリンク集',
    'view_item' => 'リンク集表示',
    'search_items' => 'リンク集検索',
    'not_found' => 'リンク集が見つかりません',
    'not_found_in_trash' => 'ゴミ箱にリンク集はありません',
  );
  $args1 = array(
    'labels' => $labels1,
    'public' => true,
    'has_archive' => true,
    'menu_position' => 5,
    'rewrite' => true,
    'supports' => array( 'title', 'editor', 'author', 'revisions', 'page-attributes' ),
  );
  register_post_type( 'links', $args1 );
}
add_action( 'init', 'create_post_type' );
