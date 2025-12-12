<?php
/*
  画像サイズ
*/
function thumb_setup()
{
    // 画像サイズの設定
  // add_image_size('thumbnail_top', 310, 175 ,true );
  // add_image_size('thumbnail_list', 400, 300 ,true );
}
add_action('after_setup_theme', 'thumb_setup');



//----------------------------------------------
//     attachment_id からアイキャッチを取り出す
//----------------------------------------------
function get_thumb_url_by_id($thumb_id, $size='medium'){
    $thumb_src = wp_get_attachment_image_src($thumb_id, $size);
    if($thumb_src !== false){
        return $thumb_src[0];
    }
    return false;
}

/**
 * アイキャッチがあればHTML出力する
 *
 * @param int 投稿ID
 * @param string 画像サイズ
 * @return return type
 */
function show_eyecatch_block(int $post_id, string $thumb_size='medium')
{
	$thumb_url = get_the_post_thumbnail_url($post_id, $thumb_size);
	$output = '';
	if(!$thumb_url) return $output;
	$output = '
	<figure class="list-img">
		<a href="'.get_permalink($post_id).'">
			<img src="'.esc_url($thumb_url).'" alt="">
		</a>
	</figure>
	';
	return $output;
}


/**
 * 画像サイズ付きのimgタグを返す
 */
function output_img_src(int $post_id, string $size='medium', $add_class="")
{
	if(!has_post_thumbnail($post_id)) return '';
	$attachment_id = get_post_thumbnail_id($post_id);
	$thumb_arr = wp_get_attachment_image_src($attachment_id, $size);
	return '<img loading="lazy" src="'.esc_url($thumb_arr[0]).'" width="'.esc_html($thumb_arr[1]).'" height="'.esc_html($thumb_arr[2]).'" class="'.$add_class.'" alt="">';
}

/**
 * 画像サイズ付きのimgタグを返す
 */
function output_img_src_by_cf(int $attachment_id, string $size='medium', $add_class="")
{
	$thumb_arr = wp_get_attachment_image_src($attachment_id, $size);
	return '<img loading="lazy" src="'.esc_url($thumb_arr[0]).'" width="'.esc_html($thumb_arr[1]).'" height="'.esc_html($thumb_arr[2]).'" class="'.$add_class.'" alt="">';
}
