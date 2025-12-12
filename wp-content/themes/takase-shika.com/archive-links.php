<?php
function page_title() {
  if (function_exists('disp_page_title')) {
    disp_page_title('normal', 'custom', 'links');
  }
}
add_action('page_title', 'page_title');
get_header();

?>

<main>
	<div class="u-contents">
		<section class="tall">
			<div class="container">
				<ul class="list-post-type01">
					<?php if (have_posts()) : while (have_posts()) : the_post();
					?>
					<li>
						<h3 class="list-ttl"><?php the_title(); ?></h3>
						<div class="list-desc postdata">
							<?php the_content(); ?>
						</div>
					</li>
					<?php
					endwhile;
					else:
						echo '<p>現在、表示する内容はありません。</p>';
					endif;
					?>

				</ul>
				<?php echo cms_pagination();?>
			</div>
		</section>
	</div>
</main>

<?php
get_footer();
