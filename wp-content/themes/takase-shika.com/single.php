<?php
$post_type = get_my_post_type();
function page_title()
{
  if (function_exists('disp_page_title')) {
    $post_type = get_my_post_type();
    if ($post_type === 'post') {
      disp_page_title('normal', 'single');
    } else {
      disp_page_title('normal', 'custom', $post_type);
    }
  }
}
add_action('page_title', 'page_title');

get_header();

if (have_posts()) : while (have_posts()) : the_post();
if($post_type === 'post'){
	$cat_arr = get_the_category();
	$cat_output = show_post_categories($post->ID, 'category', 'category');//inc/disp_taxonomy_list
}
if($post_type === 'blog'){
	$cat_output = show_post_categories($post->ID, 'blog_category', 'category');//inc/disp_taxonomy_list
}

$thumb_url = get_the_post_thumbnail_url($post->ID, 'medium');
$thumb_output = '';
if ($thumb_url) {
  $thumb_output = '<figure class="tac mb50-30"><img src="'.esc_url($thumb_url).'" alt=""></figure>';
}
?>
<main>
	<div class="u-contents">
		<section class="tall">
			<div class="container">
				<div class="l-post-single">
					<div class="post-data">
						<time datetime="<?php the_time('c');?>"><?php the_time('Y.m.d');?></time>
						<?php echo $cat_output;?>
					</div>
					<h3 class="l-ttl"><?php the_title();?></h3>
					<?php echo $thumb_output;?>
					<div class="postdata">
						<?php the_content();?>
					</div>
				</div>
				<nav class="post-number-single">
					<?php
					if ($post_type === 'post') {
						next_post_link('%link', '', true);
						echo '<a href="'.link_by_term($cat_arr[0], 'category').'" class="all">ALL</a>';
						previous_post_link('%link', '', true);
					} else {
						next_post_link('%link', '');
						echo '<a href="'.link_by_path($post_type, 'custom').'" class="all">ALL</a>';
						previous_post_link('%link', '');
					}
					?>
				</nav>
			</div>
		</section>
		<?php
    endwhile;
    else:
    echo '<h3 class="u-h3">現在、表示する内容はありません。</h3>';
    endif;
    ?>
	</div>
</main>
<?php get_footer(); ?>
