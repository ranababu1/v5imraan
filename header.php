<?php
/**
 * The header for our theme.
 *
 * Displays the <head> section and the site navigation.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package v5imraan
 */

$v5_nav_current = is_front_page() ? 'home' : ( ( is_home() || is_singular( 'post' ) || is_archive() || is_search() ) ? 'blog' : '' );
$v5_chat_icon   = '<svg class="nav-chat__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M7 18.5H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v8.5a3 3 0 0 1-3 3h-5.5L8 21.5v-3z"/></svg>';
?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#0f172a">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link rel="icon" href="<?php echo esc_url( home_url( '/wp-content/uploads/2020/12/cropped-fav-32x32.png' ) ); ?>" sizes="32x32">
	<link rel="icon" href="<?php echo esc_url( home_url( '/wp-content/uploads/2020/12/cropped-fav-192x192.png' ) ); ?>" sizes="192x192">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'v5imraan' ); ?></a>

<div id="page" class="site">
	<header id="masthead" class="site-header">
		<nav aria-label="<?php esc_attr_e( 'Primary menu', 'v5imraan' ); ?>">
			<div class="wrapper">
				<div class="logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Imraan</a></div>
				<input type="radio" name="slider" id="menu-btn">
				<input type="radio" name="slider" id="close-btn">
				<ul class="nav-links">
					<label for="close-btn" class="btn close-btn" aria-label="<?php esc_attr_e( 'Close menu', 'v5imraan' ); ?>"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18"/></svg></label>
					<li<?php echo 'home' === $v5_nav_current ? ' class="is-current"' : ''; ?>><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-home"<?php echo 'home' === $v5_nav_current ? ' aria-current="page"' : ''; ?>>Home</a></li>
					<li>
						<a href="#" class="desktop-item">My Apps</a>
						<input type="checkbox" id="showDropApps">
						<label for="showDropApps" class="mobile-item">My Apps</label>
						<ul class="drop-menu">
							<li><a href="https://crawler.imrn.dev/?ref=imraan.in">Website Crawler</a></li>
							<li><a href="https://audit.imrn.dev">Website Optimizer</a></li>
							<li><a href="https://pixfix.imrn.dev/?ref=imraan.in">Image Optimizer</a></li>
							<li><a href="https://ical.imrn.dev/">Calendar Invite Builder</a></li>
							<li><a href="#pch">Programmable Content Hub</a></li>
							<li><a href="#irp">Intelligent Resource Portal</a></li>
							<li><a href="#agentic-ai-analyst">Agentic AI Analyst</a></li>
							<li><a href="#ai-fitness-app">AI Fitness App</a></li>
							<li><a href="#developer-efficiency-portal">Developer Productivity</a></li>
							<li><a href="#com.advancedjavascript18">Programming Quiz</a></li>
						</ul>
					</li>
					<li<?php echo 'blog' === $v5_nav_current ? ' class="is-current"' : ''; ?>>
						<a href="/blog" class="desktop-item">My Blog</a>
						<input type="checkbox" id="showMega">
						<label for="showMega" class="mobile-item">My Blog</label>
						<div class="mega-box">
							<div class="content mega-grid">
								<div class="row">
									<header><a href="https://imraan.in/system-design/">System Design</a></header>
									<ul class="mega-links">
										<li><a href="https://imraan.in/system-design/distributed-systems/">Distributed Systems</a></li>
										<li><a href="https://imraan.in/system-design/caching/">Caching</a></li>
										<li><a href="https://imraan.in/system-design/networking/">Networking</a></li>
										<li><a href="https://imraan.in/system-design/observability/">Observability</a></li>
										<li><a href="https://imraan.in/system-design/reliability/">Reliability</a></li>
									</ul>
								</div>
								<div class="row">
									<header><a href="https://imraan.in/ai/">AI &amp; Automation</a></header>
									<ul class="mega-links">
										<li><a href="https://imraan.in/ai/claude/">Claude</a></li>
										<li><a href="https://imraan.in/ai/ai-agents/">AI Agents</a></li>
										<li><a href="https://imraan.in/ai/mcp/">MCP</a></li>
										<li><a href="https://imraan.in/ai/n8n/">n8n</a></li>
										<li><a href="https://imraan.in/ai/langchain/">LangChain</a></li>
									</ul>
								</div>
								<div class="row">
									<header><a href="https://imraan.in/security/">Security &amp; Compliance</a></header>
									<ul class="mega-links">
										<li><a href="https://imraan.in/security/ai-security/">AI Security</a></li>
										<li><a href="https://imraan.in/security/application-security/">Application Security</a></li>
										<li><a href="https://imraan.in/security/cloud-security/">Cloud Security</a></li>
										<li><a href="https://imraan.in/compliance/cookie-compliance/">Cookie Compliance</a></li>
										<li><a href="https://imraan.in/compliance/data-privacy-user-rights/">Data Privacy &amp; User Rights</a></li>
										<li><a href="https://imraan.in/compliance/sensitive-data-protection/">Sensitive Data Protection</a></li>
									</ul>
								</div>
								<div class="row">
									<header><a href="https://imraan.in/engineering-leadership/">Engineering Leadership</a></header>
									<ul class="mega-links">
										<li><a href="https://imraan.in/engineering-leadership/leading-teams/">Leading Teams</a></li>
										<li><a href="https://imraan.in/engineering-leadership/stakeholder-management/">Stakeholder Management</a></li>
										<li><a href="https://imraan.in/engineering-leadership/delivery-process/">Delivery &amp; Process</a></li>
										<li><a href="https://imraan.in/engineering-leadership/communication/">Communication</a></li>
									</ul>
								</div>
								<div class="row">
									<header><a href="https://imraan.in/backend-development/">Backend + Db</a></header>
									<ul class="mega-links">
										<li><a href="https://imraan.in/backend-development/java/">Java</a></li>
										<li><a href="https://imraan.in/backend-development/springboot/">Springboot</a></li>
										<li><a href="https://imraan.in/backend-development/postgres/">Postgres</a></li>
										<li><a href="https://imraan.in/backend-development/mongodb/">MongoDB</a></li>
										<li><a href="https://imraan.in/backend-development/nodejs/">NodeJS</a></li>
										<li><a href="https://imraan.in/backend-development/microservices/">Microservices</a></li>
									</ul>
								</div>
								<div class="row">
									<header><a href="https://imraan.in/frontend-development/">Frontend</a></header>
									<ul class="mega-links">
										<li><a href="https://imraan.in/frontend-development/html5/">HTML5</a></li>
										<li><a href="https://imraan.in/frontend-development/css/">CSS</a></li>
										<li><a href="https://imraan.in/frontend-development/javascript/">Javascript</a></li>
										<li><a href="https://imraan.in/frontend-development/jquery/">jQuery</a></li>
										<li><a href="https://imraan.in/frontend-development/reactjs/">ReactJS</a></li>
									</ul>
								</div>
								<div class="row">
									<header><a href="https://imraan.in/devops/">DevOps</a></header>
									<ul class="mega-links">
										<li><a href="https://imraan.in/devops/aws/">AWS</a></li>
										<li><a href="https://imraan.in/devops/azure/">Azure</a></li>
										<li><a href="https://imraan.in/devops/cicd/">CI/CD</a></li>
										<li><a href="https://imraan.in/devops/docker/">Docker</a></li>
										<li><a href="https://imraan.in/devops/kubernetes/">Kubernetes</a></li>
									</ul>
								</div>
							</div>
						</div>
					</li>
					<li class="nav-chat">
						<a href="#" class="desktop-item nav-chat__pill"><?php echo $v5_chat_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><span>Chat With Me</span></a>
						<input type="checkbox" id="showDropChat">
						<label for="showDropChat" class="mobile-item">Chat With Me</label>
						<ul class="drop-menu">
							<li><a href="https://wa.me/9854082826">via Whatsapp</a></li>
							<li><a href="https://t.me/i18587">via Telegram</a></li>
						</ul>
					</li>
				</ul>
				<details class="nav-chat-mobile">
					<summary class="nav-chat__pill"><?php echo $v5_chat_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><span>Chat With Me</span></summary>
					<ul class="nav-chat-mobile__menu">
						<li><a href="https://wa.me/9854082826">via Whatsapp</a></li>
						<li><a href="https://t.me/i18587">via Telegram</a></li>
					</ul>
				</details>
				<label for="menu-btn" class="btn menu-btn" aria-label="<?php esc_attr_e( 'Open menu', 'v5imraan' ); ?>"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M4 7h16M4 12h16M4 17h16"/></svg></label>
			</div>
		</nav>
	</header>