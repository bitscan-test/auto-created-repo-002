<?php
$h3_title = 'すべての投稿';
if(is_tax() || is_category()){
	$h3_title = single_cat_title("", false);
}

?>

<div class="list-category">
	<h3 class="list-ttl">カテゴリー</h3>
	<ul>
		<?php wp_list_categories( 'title_li=&taxonomy=blog_category' );?>
	</ul>
</div>
<h3 class="u-h3"><?php echo $h3_title;?></h3>
<ul class="list-post-type01">
	<?php if (have_posts()) : while (have_posts()) : the_post();

	$thumb_url = output_img_src($post->ID);
	if(!$thumb_url){
	  $thumb_url = '<img src="'.get_template_directory_uri().'/images/share/no_images.jpg" alt="" width="464" width="330" loading="lazy">';
	}


	$cat_output = show_post_categories($post->ID, 'blog_category', 'category');//inc/disp_taxonomy_list

	$post_content = apply_filters('the_content', strip_shortcodes($post->post_content));
	?>

	<li>
		<h4 class="list-ttl"><a href="<?php the_permalink();?>"><?php the_title();?></a></h4>
		<figure class="list-img">
			<?php echo $thumb_url;?>
		</figure>
		<div class="list-desc">
			<div class="list-data">
				<time datetime="<?php the_time('c');?>"><?php the_time('Y.m.d');?></time>
				<?php echo $cat_output;?>
			</div>
		</div>
		<p class="list-txt"><?php echo get_my_excerpt($post_content, 120);?></p>
		<p class="list-btn"><a href="<?php the_permalink();?>">続きを読む</a></p>
	</li>

	<?php
	endwhile;
	else:
		echo '<p>現在、表示する内容はありません。</p>';
	endif;
	?>

</ul>
<?php echo cms_pagination();?>
