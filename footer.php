<?php
/**
 * The template for displaying the footer.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package v5imraan
 */

?>

<?php
$v5_expertise = array(
	'AI Systems'             => '/ai/',
	'Platform Architecture'  => '/system-design/platform-engineering/',
	'Engineering Leadership' => '/engineering-leadership/',
	'System Design'          => '/system-design/',
);
$v5_social_icons = array(
	'X (Twitter)' => array( 'X', '<path d="M4 4l16 16M20 4L4 20" />' ),
	'Instagram'   => array( 'Instagram', '<rect x="3.5" y="3.5" width="17" height="17" rx="5" /><circle cx="12" cy="12" r="4" /><circle cx="17.3" cy="6.7" r="0.6" fill="currentColor" />' ),
	'Quora'       => array( 'Quora', '<circle cx="11.5" cy="11" r="6.5" /><path d="M14.5 15.5l4 4.5" />' ),
);
$v5_privacy_url = function_exists( 'get_privacy_policy_url' ) ? get_privacy_policy_url() : '';
?>
<footer id="colophon" class="site-footer sf">
	<div class="sf__glow" aria-hidden="true"></div>
	<div class="container sf__inner">
		<div class="sf__main">
			<div class="sf__brand">
				<p class="sf__wordmark" aria-hidden="true">Imraan</p>
				<p class="sf__tagline">Engineering leader, builder, writer and a father. Curious by default. Always building, breaking, building better. Could use a 48-hour day.</p>
			</div>
			<div class="sf__cols">
				<nav class="sf__col" aria-label="<?php esc_attr_e( 'Expertise', 'v5imraan' ); ?>">
					<h2 class="sf__heading">Expertise</h2>
					<ul>
						<?php foreach ( $v5_expertise as $v5_label => $v5_path ) : ?>
							<li>
								<?php if ( $v5_path ) : ?>
									<a href="<?php echo esc_url( home_url( $v5_path ) ); ?>"><?php echo esc_html( $v5_label ); ?></a>
								<?php else : ?>
									<span><?php echo esc_html( $v5_label ); ?></span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>
				<div class="sf__col">
					<h2 class="sf__heading">Connect</h2>
					<ul>
						<?php foreach ( v5imraan_social_profiles() as $v5_network => $v5_url ) : ?>
							<?php $v5_icon = isset( $v5_social_icons[ $v5_network ] ) ? $v5_social_icons[ $v5_network ] : array( $v5_network, '<circle cx="12" cy="12" r="8" />' ); ?>
							<li>
								<a class="sf__social" href="<?php echo esc_url( $v5_url ); ?>" rel="me noopener" target="_blank">
									<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><?php echo $v5_icon[1]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></svg>
									<span><?php echo esc_html( $v5_icon[0] ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
		<div class="sf__bar">
			<p class="sf__copy">&copy; 2010 &ndash; <?php echo esc_html( gmdate( 'Y' ) ); ?> <span>imraan.in</span></p>
			<ul class="sf__legal">
				<?php if ( $v5_privacy_url ) : ?>
					<li><a href="<?php echo esc_url( $v5_privacy_url ); ?>">Privacy</a></li>
				<?php endif; ?>
				<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact</a></li>
			</ul>
		</div>
	</div>
</footer>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>