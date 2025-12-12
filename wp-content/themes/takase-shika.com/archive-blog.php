<?php
function page_title() {
  if (function_exists('disp_page_title')) {
    disp_page_title('normal', 'custom','blog');
  }
}
add_action('page_title', 'page_title');
get_header();

?>

<main>
	<div class="u-contents">
		<section class="tall">
			<div class="container">
				<?php get_template_part('template-parts/blog-list');?>
			</div>
		</section>
	</div>
</main>

<?php
get_footer();
