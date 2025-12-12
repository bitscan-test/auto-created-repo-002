<?php
/*
  カテゴリー、アーカイブリスト
*/
// タクソノミー（カテゴリー）リスト表示
function disp_taxonomy_list($tax_name){
?>
          <section class="side_category">
            <h3>カテゴリー</h3>
            <ul>
<?php call_category_list_tag($tax_name); ?>
            </ul>
          </section>
<?php
}

function call_category_list_tag( $tax_name, $parent_id = NULL ){
  // 表示形式を変えるときはここを変更
  $args['taxonomy'] = $tax_name;
  $args['depth'] = 0;
  $args['show_count'] = 0;
  $args['hide_empty'] = 1;
  $args['title_li'] = '';
  $args['echo'] = true;
  $args['orderby'] = 'ID';
  $args['order'] = 'ASC';
  if ($parent_id != NULL) {
    $args['child_of'] = $parent_id;
  }

  wp_list_categories($args);
}

// アーカイブリストの表示
function disp_archive_list( $post_type = NULL ){
?>
          <section class="side_archive">
            <h3>アーカイブ</h3>
            <ul>
<?php call_category_list_tag($post_type); ?>
            </ul>
          </section>
<?php
}

function call_archive_list_tag($post_type){
  // 表示形式を変えるときはここを変更
  $args['type'] = 'monthly';
  $args['limit'] = '';
  $args['format'] = 'html';
  $args['show_post_count'] = false;
  if ( $post_type != NULL ) {
    $args['post_type'] = $post_type;
  }
  $args['echo'] = true;
  $args['order'] = 'DESC';

  wp_get_archives($args);
}

global $my_archives_post_type;

add_filter( 'getarchives_where', 'my_getarchives_where', 10, 2 );
function my_getarchives_where( $where, $r ) {
  global $my_archives_post_type;
  if ( isset($r['post_type']) ) {
    $my_archives_post_type = $r['post_type'];
    $where = str_replace( '\'post\'', '\'' . $r['post_type'] . '\'', $where );
  } else {
    $my_archives_post_type = '';
  }
  return $where;
}

add_filter( 'get_archives_link', 'my_get_archives_link' );
function my_get_archives_link( $link_html ) {
  global $my_archives_post_type;
  $add_link = '';
  if ( '' != $my_archives_post_type )
    $add_link .= '?post_type=' . $my_archives_post_type;
  $link_html = preg_replace("/href=\'(.+)\'/","href='$1".$add_link."'",$link_html);

  return $link_html;
}


//----------------------------------------------
// カテゴリーをプルダウンで表示
//----------------------------------------------
function show_select_box_category($cat_name='category'){
    $cat_all = get_terms($cat_name);
    if($cat_name !== 'category'){
        $current_cat = get_query_var('term');
    }else{
        $current_cat = get_query_var('cat');
    }
    $cat_all_output = '';
    if($cat_all && !is_wp_error($cat_all)){
        foreach($cat_all as $cat_s){
            $selected = '';
            if($cat_name !== 'category'){
                if($cat_s->slug === $current_cat) $selected = ' selected';
            }else{
                if($cat_s->term_id === $current_cat) $selected = ' selected';
            }
            $cat_all_output .= '<option value="'.esc_url(get_term_link($cat_s, $cat_name)).'"'.$selected.'>'.esc_attr($cat_s->name).'</option>';
        }
    }
    return $cat_all_output;
}

//----------------------------------------------
// カテゴリー一覧を出力する関数
//----------------------------------------------
function show_post_categories(int $post_id, string $taxonomy='category', string $span_wrap=""){
    $cat_arr = get_the_terms($post_id, $taxonomy);
    $cat_output = '';
    if($cat_arr && !is_wp_error($cat_arr)){
        foreach($cat_arr as $cat_s){
            if($span_wrap === ""){
				if($cat_output) $cat_output .= '/';
                $cat_output .= esc_html($cat_s->name);//何もwrapしない。
            }else{
                $cat_output .= '<span class="'.$span_wrap.'">'.esc_html($cat_s->name).'</span>';//wrapする場合。クラス名があれば追加
            }
        }
    }
    return $cat_output;
}
