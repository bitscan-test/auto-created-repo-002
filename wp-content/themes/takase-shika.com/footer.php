<div class="pagetop"><a href="#"><img loading="lazy" width="136" height="136" src="<?php echo get_template_directory_uri();?>/images/share/pagetop.png" alt="ページトップへ"></a></div>
<footer class="footer">
	<div class="f-01">
		<div class="container">
			<div class="f-logo"><a href="<?php echo link_by_path();?>"><img loading="lazy" width="224" height="65" src="<?php echo get_template_directory_uri();?>/images/share/logo.png" alt="高瀬歯科"></a></div>
			<p class="f-address">茨城県つくば市筑穂1-12-7<br><span class="red mbL">※ 当院は敷地内禁煙になります。</span></p>
			<nav class="site-map">
				<ul>
					<li><a href="<?php echo link_by_path('clinic');?>">高瀬歯科について</a></li>
					<li><a href="<?php echo link_by_path('message');?>">歯科治療を有意義なものにするために</a></li>
					<li><a href="<?php echo link_by_path('staff');?>">スタッフ紹介</a></li>
					<li><a href="<?php echo link_by_path('access');?>">医院案内・アクセス</a></li>
					<li><a href="<?php echo link_by_path('news');?>">お知らせ</a></li>
					<li><a href="<?php echo link_by_path("blog","custom"); ?>">ブログ</a></li>
					<li><a href="<?php echo link_by_path("links","custom"); ?>">リンク集</a></li>
					<li><a href="<?php echo link_by_path('covid-19');?>">感染症対策</a></li>
					<li class="mr0"><a href="<?php echo link_by_path('privacy');?>">プライバシーポリシー</a></li>
					<li class="w100">
						<a href="<?php echo link_by_path('treatment');?>">治療について</a>
						<ul>
							<li><a href="<?php echo link_by_path('treatment/general');?>">一般歯科</a></li>
							<li><a href="<?php echo link_by_path('treatment/preventive');?>">予防歯科</a></li>
							<li><a href="<?php echo link_by_path('treatment/child');?>">小児歯科</a></li>
							<li><a href="<?php echo link_by_path('treatment/cosmetic');?>">審美歯科</a></li>
							<!--<li><a href="<?php echo link_by_path('treatment/implant');?>">インプラント</a></li>-->
							<li><a href="<?php echo link_by_path('treatment/denture');?>">義歯</a></li>
							<!--<li><a href="<?php echo link_by_path('treatment/wisdom');?>">口腔外科</a></li>-->
							<li><a href="<?php echo link_by_path('treatment/periodontal-disease');?>">歯周病治療</a></li>
							<li><a href="<?php echo link_by_path('treatment/clench');?>">食いしばり</a></li>
						</ul>
					</li>
					<li class="w100">
						<a href="<?php echo link_by_path('facility');?>">設備・院内紹介</a>
						<ul>
							<li><a href="<?php echo link_by_path('facility/yag');?>">Yagレーザー</a></li>
							<li><a href="<?php echo link_by_path('facility/co2');?>">CO₂レーザー</a></li>
							<li><a href="<?php echo link_by_path('facility/extraoral_vacuum');?>">口腔外バキューム</a></li>
							<li><a href="<?php echo link_by_path('facility/diagnodent');?>">ダイアグノデント</a></li>
							<li><a href="<?php echo link_by_path('facility/microscope');?>">マイクロスコープ</a></li>
						</ul>
					</li>
				</ul>
			</nav>
			<p class="f-message">STAND ALONE COMPLEX<br>No retreats! No pleas! No regrets! An emperor never runs!</p>
		</div>
	</div>
	<p class="copyright"><small>&copy;2020 takase-shika.</small></p>
</footer>
<?php wp_footer(); ?>
<script type="text/javascript" defer src="//webfont.fontplus.jp/accessor/script/fontplus.js?go8Z-skfnuc%3D&box=7oeZB0O4jdU%3D&timeout=3&aa=1&ab=2&cm=80" charset="utf-8"></script>
<script language="javascript" type="text/javascript">
	document.addEventListener('DOMContentLoaded', function() {
		try {
			const fontplus_loaded = setInterval(function() {
				if (FONTPLUS.isloading() === false) {
					FONTPLUS.start();
					clearInterval(fontplus_loaded);
				}
			}, 100);
			setTimeout(clearInterval, 3000, fontplus_loaded);
			FONTPLUS.async();
		} catch (error) {
			// console.error(error);
		}
	}, false)
</script>
</body>

</html>
