<?php
/**
 * The template for displaying the footer.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package v5imraan
 */

?>

<footer id="colophon" class="site-footer">
	<div class="footer-block">
		<div class="container">
			<ul class="footer-dflex">
				<li>imraan.in &copy; 2010 - <?php echo esc_html( gmdate( 'Y' ) ); ?></li>
				<?php foreach ( v5imraan_social_profiles() as $network_label => $network_url ) : ?>
					<li><a href="<?php echo esc_url( $network_url ); ?>"><?php echo esc_html( $network_label ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</footer>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>