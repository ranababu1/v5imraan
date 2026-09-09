<?php
/**
 * The header for our theme.
 *
 * Displays the <head> section and the site navigation.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package v5imraan
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#0e0d0d">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
	<link rel="icon" href="https://imraan.in/wp-content/uploads/2020/12/cropped-fav-32x32.png" sizes="32x32">
	<link rel="icon" href="https://imraan.in/wp-content/uploads/2020/12/cropped-fav-192x192.png" sizes="192x192">
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
					<label for="close-btn" class="btn close-btn"><i class="fas fa-times"></i></label>
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
					<li>
						<a href="#">My Apps</a>
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
										<li><a href="https://imraan.in/backend-development/java/">Java</a></li>
										<li><a href="https://imraan.in/backend-development/springboot/">Springboot</a></li>
										<li><a href="https://imraan.in/backend-development/postgres/">Postgres</a></li>
										<li><a href="https://imraan.in/backend-development/mongodb/">MongoDB</a></li>
										<li><a href="https://imraan.in/backend-development/web-development/nodejs/">NodeJS</a></li>
										<li><a href="https://imraan.in/backend-development/microservices/">Microservices</a></li>
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
									<header>AI &amp; Automation</header>
									<ul class="mega-links">
										<li><a href="https://imraan.in/ai/">AI</a></li>
										<li><a href="https://imraan.in/ai/llm/">LLMs</a></li>
										<li><a href="https://imraan.in/ai/prompt-engineering/">Prompt Engineering</a></li>
										<li><a href="https://imraan.in/ai/agentic/">AI Agents</a></li>
										<li><a href="https://imraan.in/ai/n8n/">n8n</a></li>
										<li><a href="https://imraan.in/ai/langchain/">LangChain</a></li>
									</ul>
								</div>
								<div class="row">
									<header>AI Tools</header>
									<ul class="mega-links">
										<li><a href="https://imraan.in/ai/chatgpt/">ChatGPT</a></li>
										<li><a href="https://imraan.in/ai/github-copilot/">Github Copilot</a></li>
										<li><a href="https://imraan.in/ai/gemini/">Gemini</a></li>
										<li><a href="https://imraan.in/ai/claude/">Claude</a></li>
										<li><a href="https://imraan.in/ai/claude-code/">Claude Code</a></li>
										<li><a href="https://imraan.in/ai/ollama/">Ollama</a></li>
									</ul>
								</div>
							</div>
						</div>
					</li>
					<li>
						<a href="#" class="desktop-item">Chat With Me</a>
						<input type="checkbox" id="showDropChat">
						<label for="showDropChat" class="mobile-item">Chat With Me</label>
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