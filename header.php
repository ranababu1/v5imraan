<?php

/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package v5imraan
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap"
		rel="stylesheet">
		<link rel="icon" href="https://imraan.in/wp-content/uploads/2020/12/cropped-fav-32x32.png" sizes="32x32" />
		<link rel="icon" href="https://imraan.in/wp-content/uploads/2020/12/cropped-fav-192x192.png" sizes="192x192" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php wp_body_open(); ?>

	<div id="page" class="site">
		<header>
			<nav>
				<div class="wrapper">
					<div class="logo"><a href="/">Imraan</a></div>
					<input type="radio" name="slider" id="menu-btn">
					<input type="radio" name="slider" id="close-btn">
					<ul class="nav-links">
						<label for="close-btn" class="btn close-btn"><i class="fas fa-times"></i></label>
						<li><a href="/">Home</a></li>
						<li><a href="#">My Apps</a></li>

						<li>
							<a href="/blog" class="desktop-item">My Blog</a>
							<input type="checkbox" id="showMega">
							<label for="showMega" class="mobile-item">My Blog</label>
							<div class="mega-box">
								<div class="content">

									<div class="row">
										<header>Frontend</header>
										<ul class="mega-links">
											<li><a href="https://imraan.in/web-development/html5/">HTML5</a></li>
											<li><a href="https://imraan.in/web-development/css/">CSS</a></li>
											<li><a href="https://imraan.in/web-development/javascript/">Javascript</a></li>
											<li><a href="https://imraan.in/web-development/jquery/">jQuery</a></li>
											<li><a href="https://imraan.in/reactjs/">ReactJS</a></li>
										</ul>
									</div>
									<div class="row">
										<header>Backend + Db</header>
										<ul class="mega-links">
											<li><a href="https://imraan.in/java/">Java</a></li>
											<li><a href="https://imraan.in/springboot/">Springboot</a></li>
											<li><a href="https://imraan.in/postgres/">Postgres</a></li>
											<li><a href="https://imraan.in/mongodb/">MongoDB</a></li>
											<li><a href="https://imraan.in/web-development/nodejs/">NodeJS</a></li>
											<li><a href="https://imraan.in/microservices/">Microservices</a></li>
										</ul>
									</div>
									<div class="row">
										<header>DevOps</header>
										<ul class="mega-links">
											<li><a href="https://imraan.in/aws/">AWS</a></li>
											<li><a href="https://imraan.in/azure/">Azure</a></li>
											<li><a href="https://imraan.in/cicd/">CI/CD</a></li>
											<li><a href="https://imraan.in/docker/">Docker</a></li>
											<li><a href="https://imraan.in/kubernetes/">Kubernetes</a></li>
											<li><a href="https://imraan.in/jenkins/">Jenkins</a></li>

										</ul>
									</div>

									<div class="row">
										<header>Digital</header>
										<ul class="mega-links">
											<li><a href="https://imraan.in/seo/">SEO</a></li>
											<li><a href="https://imraan.in/cro/">CRO</a></li>
											<li><a href="https://imraan.in/analytics/">Analytics</a></li>
											<li><a href="https://imraan.in/martech/">Martech</a></li>
											<li><a href="https://imraan.in/crm/">CRM</a></li>
										</ul>
									</div>
								</div>
							</div>
						</li>
						<li>
							<a href="#" class="desktop-item">Chat With Me</a>
							<input type="checkbox" id="showDrop">
							<label for="showDrop" class="mobile-item">Chat With Me</label>
							<ul class="drop-menu">
								<li><a href="https://wa.me/9854082826">via Whatsapp</a></li>
								<li><a href="https://t.me/i18587">via Telegram</a></li>
							</ul>
						</li>
					</ul>
					<label for="menu-btn" class="btn menu-btn"><i class="fas fa-bars"></i></label>
				</div>
			</nav>
		</header>