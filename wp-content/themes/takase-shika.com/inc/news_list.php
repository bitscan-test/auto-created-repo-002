<?php

function top_news_list(WP_Query $the_query) {
  $output = '';

  if ($the_query->have_posts()) : while ($the_query->have_posts()) : $the_query->the_post();

	$post_id = $the_query->post->ID;
	$output .= '
	<li>
		<a href="'. get_the_permalink($post_id).'">
			<time class="time" datetime="'.get_the_time('c', $post_id).'">'. get_the_time('Y.m.d', $post_id) .'</time>
			<span class="ttl">'. get_the_title($post_id) .'</span>
		</a>
	</li>';

  endwhile;
  else:
  return '<li><span class="ttl">現在、該当のお知らせはございません。</span></li>';
  endif;

  wp_reset_postdata();
  return $output;
}


function top_blog_list(WP_Query $the_query) {
	$output = '';

	if ($the_query->have_posts()) : while ($the_query->have_posts()) : $the_query->the_post();

	$post_id = $the_query->post->ID;

	$thumb_url = output_img_src($post_id);
	if(!$thumb_url){
	  $thumb_url = '<img src="'.get_template_directory_uri().'/images/share/no_images.jpg" alt="" width="464" width="330" loading="lazy">';
	}

	$output .= '
	<li>
		<a href="'.get_the_permalink($post_id).'">
			<figure class="list-img">
				'.$thumb_url.'
			</figure>
			<time class="time" datetime="'.get_the_time('c', $post_id).'">'. get_the_time('Y.m.d', $post_id) .'</time>
			<h3 class="list-ttl">'. get_my_excerpt(get_the_title($post_id), 24) .'</h3>
		</a>
	</li>
	';

  endwhile;
  else:
	  return '<div class="single"><span class="txt">現在、該当の記事はございません。</span></div>';
  endif;

  wp_reset_postdata();
  return $output;
}
