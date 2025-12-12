<?php
function page_title() {
    if (function_exists('disp_page_title')) {
        disp_page_title('normal', '404');
    }
}
add_action('page_title', 'page_title');

get_header();?>
<main>
	<article class="u-contents">
		<section class="tall">
			<div class="container">
				<?php
				if (have_posts()) : while (have_posts()) : the_post();

				the_content();

				endwhile;
				else:
					?>
					<p>指定されたページは見つかりませんでした。</p>
					<p><a href="<?php echo link_by_path();?>" class="link">トップページへ</a></p>
				<?php endif; ?>
			</div>
		</section>
	</article>
</main>
<?php
// get_sidebar();
get_footer();
