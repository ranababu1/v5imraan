<?php
/* Template Name: Homepage
*
*
*/
get_header(); ?>

<main>

  <section>
    <div class="container">
      <p class="hp-eyebrow">Engineering Leader. AI Systems. Platform Architecture.</p>
      <h1 class="ourfeatures-heading">
        <?php
        echo 'I build AI-driven systems, scalable platforms, and high-performance engineering teams.';
        ?>
      </h1>
      <div class="ourfeatures-dflex">
        <div class="ourfeatures-dflex__left">
          <div class="ourfeatures-box">
            <span class="ourfeatures-gradient"><small></small> Imranul Haque Mazumder</span>
            <h4>I build end-to-end AI systems</h4>
            <p>
              That transform business operations driving growth, efficiency, and innovation through AI-powered platforms, intelligent automation, and engineering excellence. Scalable systems that deliver measurable enterprise impact.
            </p>
            <img class="img-responsive ourfeatures-img" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/img/imrn.png" alt="Portrait of Imranul Haque Mazumder" loading="lazy" decoding="async">
          </div>
        </div>
        <div class="ourfeatures-dflex__right">
          <div class="ourfeatures-sm__dflex">
            <div class="ourfeatures-sm__dflexleft">
              <div class="ourfeatures-sm__box">
                <h3 class="pdt-0">14+ yrs</h3>
                <h4 class="pdt-0">of Experience</h4>
                <p>
                  AI Systems, Full Stack, DevOps, Platform Architecture, Security, CRO, Analytics, SEO, Martech, CRM and CMS.
                </p>
              </div>
            </div>
            <div class="ourfeatures-sm__dflexright">
              <div class="ourfeatures-sm__box">
                <h3 class="pdt-0">~80%</h3>
                <h4 class="pdt-0">Cloud Cost Reduction</h4>
                <p>
                  through cloud architectural and operational optimizations.
                </p>
              </div>
            </div>
          </div>
          <div class="ourfeatures-box green-gradient mg-25">
            <h3>150+</h3>
            <h4>Projects Delivered</h4>
            <p>
              Scalable solutions for 150+ projects across enterprise and Fortune 500 domains - finance, hospitality, technology, and e-commerce.
              Led cross-functional teams, built delivery systems, and drove measurable outcomes for global clients.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Trusted collaboration / logo marquee -->
  <section class="hp-trusted">
    <div class="container">
      <p class="hp-trusted__kicker">Trusted collaboration</p>
      <h3 class="hp-trusted__heading">I&rsquo;ve had the pleasure to work with</h3>
    </div>
    <div class="hp-marquee">
      <div class="hp-marquee__track">
        <?php
        $logos = array(
          array( 'amex.png', 'Amex' ),
          array( 'fox-sports.png', 'Fox Sports' ),
          array( 'delta.png', 'Delta' ),
          array( 'hilton.png', 'Hilton' ),
          array( 'marriott.png', 'Marriott' ),
          array( 'zeta.png', 'Zeta' ),
        );
        $logo_uri = get_stylesheet_directory_uri() . '/img/';
        foreach ( array( 1, 2 ) as $pass ) :
          foreach ( $logos as $logo ) :
        ?>
            <img src="<?php echo esc_url( $logo_uri . $logo[0] ); ?>" alt="<?php echo esc_attr( $logo[1] ); ?>" loading="lazy" decoding="async">
        <?php
          endforeach;
        endforeach;
        ?>
      </div>
    </div>
  </section>

  <!-- Flagship Systems -->
  <section id="projects" class="card-box hp-dark">
    <div class="container">
      <div class="hp-section-head">
        <p class="hp-eyebrow">Selected work</p>
        <h2 class="hp-heading hp-heading--gradient">Flagship Systems</h2>
        <p class="hp-subtext">AI-driven systems and platforms built for enterprise scale and measurable outcomes.</p>
      </div>
      <ul class="flagship-grid">
        <?php
        $projects = [
          [
            'title' => 'Product Solution Architect Agent',
            'text' => 'Architected and delivered an AI-powered strategic advisory platform that autonomously researches target financial institutions, synthesizes business intelligence from public and enterprise sources, identifies strategic opportunities and risks, and recommends solution pathways to support consultative sales and account growth initiatives.',
            'link' => '#'
          ],
          [
            'title' => 'Content Localization Agent',
            'text' => 'AI-powered localization platform integrated directly into the CMS, to enable asset creation on the fly, processing thousands of content pages and automatically generating market-specific versions through translation, transcreation, and contextual adaptation. Ensures linguistic accuracy, brand consistency, and user experience parity while enabling large-scale multilingual content delivery.',
            'link' => '#'
          ],
          [
            'title' => 'Cloud Infrastructure Optimization',
            'text' => 'Led a strategic infrastructure modernization initiative that reduced cloud operating costs by over 80% through platform rationalization, architectural redesign, automated scaling, and operational excellence practices. Improved system performance, resilience, and scalability while establishing a sustainable foundation for future growth.',
            'link' => '#'
          ],
          [
            'title' => 'Engineering Intelligence Agent',
            'text' => 'Engineering intelligence agent that ingests software delivery telemetry from source control systems, continuously analyzes execution patterns, identifies delivery risks and team bottlenecks, and generates leadership-ready insights and recommendations through real-time dashboards.',
            'link' => '#'
          ],
          [
            'title' => 'Composable Content Platform',
            'text' => 'Architected a composable content platform that empowers business teams to independently create, manage, and launch targeted digital experiences without engineering intervention. Accelerated campaign delivery from days to minutes while improving governance, content reuse, and operational efficiency across digital channels.',
            'link' => '#'
          ],
          [
            'title' => 'AI Website & Experience Optimizer',
            'text' => 'Built an AI-driven digital experience optimization platform that audits websites, analyzes performance bottlenecks, and recommends + applies intelligent optimizations across assets and delivery layers. Improved page performance, user experience, and operational efficiency through automated optimization workflows.',
            'link' => '#'
          ],
        ];
        foreach ($projects as $i => $proj) :
          $parts      = array_map( 'trim', explode( '|', $proj['text'], 2 ) );
          $desc       = isset( $parts[1] ) ? $parts[1] : $parts[0];
          $stack      = isset( $parts[1] ) ? $parts[0] : '';
          $stack_tags = $stack ? array_map( 'trim', explode( '+', $stack ) ) : [];
        ?>
          <li>
            <div class="flagship-card">
              <span class="flagship-card__index" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
              <h3 class="flagship-card__title"><?php echo esc_html($proj['title']); ?></h3>
              <p class="flagship-card__desc"><?php echo esc_html($desc); ?></p>
              <?php if ( $stack_tags ) : ?>
                <ul class="flagship-card__stack">
                  <?php foreach ( $stack_tags as $tag ) : ?>
                    <li><?php echo esc_html( $tag ); ?></li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
              <?php if ($proj['link'] !== '#') : ?>
                <a href="<?php echo esc_url($proj['link']); ?>" class="flagship-card__cta">View Project</a>
              <?php endif; ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <!-- Awards & Recognition -->
  <section id="awards" class="hp-awards">
    <div class="container">
      <div class="hp-section-head">
        <p class="hp-eyebrow">Recognition</p>
        <h2 class="hp-heading hp-heading--gradient">Awards &amp; Promotions</h2>
      </div>
      <div class="awards-layout">
        <ul class="awards-list">
          <li>
            <button type="button" class="award-row js-award" data-award="a1" aria-pressed="true">
              <span class="award-row__year">2025</span>
              <span class="award-row__title">Promoted to Associate Director</span>
            </button>
          </li>
          <li>
            <button type="button" class="award-row js-award" data-award="a2" aria-pressed="false">
              <span class="award-row__year">2024</span>
              <span class="award-row__title">Shining Star Award</span>
            </button>
          </li>
          <li>
            <button type="button" class="award-row js-award" data-award="a3" aria-pressed="false">
              <span class="award-row__year">2023</span>
              <span class="award-row__title">Ultimate Team Award</span>
            </button>
          </li>
          <li>
            <button type="button" class="award-row js-award" data-award="a4" aria-pressed="false">
              <span class="award-row__year">2022</span>
              <span class="award-row__title">Promoted to Head of SW Dev</span>
            </button>
          </li>
          <li>
            <button type="button" class="award-row js-award" data-award="a5" aria-pressed="false">
              <span class="award-row__year">2021</span>
              <span class="award-row__title">Trailblazer Award</span>
            </button>
          </li>
          <li>
            <button type="button" class="award-row js-award" data-award="a6" aria-pressed="false">
              <span class="award-row__year">2019</span>
              <span class="award-row__title">Promoted to Team Lead</span>
            </button>
          </li>
          <li>
            <button type="button" class="award-row js-award" data-award="a7" aria-pressed="false">
              <span class="award-row__year">2018</span>
              <span class="award-row__title">iLead Award</span>
            </button>
          </li>
        </ul>

        <div class="award-panel">
          <div class="award-details" id="a1details" style="display: block;">
            <img class="award-panel__icon" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/img/promotions.webp" alt="" loading="lazy" decoding="async">
            <span class="award-panel__year">2025</span>
            <h3 class="award-panel__title">Promoted to Associate Director</h3>
            <p class="award-panel__desc">Promoted to Associate Director in recognition of sustained engineering leadership, AI systems delivery, and measurable impact across platform, team, and business outcomes.</p>
          </div>

          <div class="award-details" id="a2details" style="display: none;">
            <img class="award-panel__icon" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/img/awards.webp" alt="" loading="lazy" decoding="async">
            <span class="award-panel__year">2024</span>
            <h3 class="award-panel__title">Shining Star Award</h3>
            <p class="award-panel__desc">Recognized for exceptional performance and outstanding contributions to engineering outcomes, delivering high-impact systems that moved the needle on business results.</p>
          </div>

          <div class="award-details" id="a3details" style="display: none;">
            <img class="award-panel__icon" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/img/awards.webp" alt="" loading="lazy" decoding="async">
            <span class="award-panel__year">2023</span>
            <h3 class="award-panel__title">Ultimate Team Award</h3>
            <p class="award-panel__desc">Awarded for driving exceptional cross-functional collaboration that brought a critical, high-stakes project to completion under tight deadlines and complex stakeholder dynamics.</p>
          </div>

          <div class="award-details" id="a4details" style="display: none;">
            <img class="award-panel__icon" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/img/promotions.webp" alt="" loading="lazy" decoding="async">
            <span class="award-panel__year">2022</span>
            <h3 class="award-panel__title">Promoted to Head of SW Dev</h3>
            <p class="award-panel__desc">Promoted to Head of Software Development, recognizing engineering leadership, platform delivery excellence, and a consistent track record of mentoring and growing high-performing teams.</p>
          </div>

          <div class="award-details" id="a5details" style="display: none;">
            <img class="award-panel__icon" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/img/awards.webp" alt="" loading="lazy" decoding="async">
            <span class="award-panel__year">2021</span>
            <h3 class="award-panel__title">Trailblazer Award</h3>
            <p class="award-panel__desc">Awarded for pioneering AI-driven approaches and platform innovations that set new delivery standards across the organization.</p>
          </div>

          <div class="award-details" id="a6details" style="display: none;">
            <img class="award-panel__icon" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/img/promotions.webp" alt="" loading="lazy" decoding="async">
            <span class="award-panel__year">2019</span>
            <h3 class="award-panel__title">Promoted to Team Lead</h3>
            <p class="award-panel__desc">First promotion to Team Lead, recognizing demonstrated leadership potential, technical depth, and consistent high performance as an individual contributor.</p>
          </div>

          <div class="award-details" id="a7details" style="display: none;">
            <img class="award-panel__icon" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/img/awards.webp" alt="" loading="lazy" decoding="async">
            <span class="award-panel__year">2018</span>
            <h3 class="award-panel__title">iLead Award</h3>
            <p class="award-panel__desc">Successfully retained a critical account by rapidly acquiring and delivering niche MarTech expertise. With no available replacement, stepped up, excelled, and earned direct accolades from the client.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Award details switching is handled by js/main.js (js-award / data-award hooks). -->

  <!-- Tech Insights From My Blog -->
  <section id="tech-insights" class="card-box hp-dark">
    <div class="container">
      <div class="hp-section-head">
        <p class="hp-eyebrow">From the blog</p>
        <h2 class="hp-heading hp-heading--gradient">Tech Insights From My Blog</h2>
        <p class="hp-subtext">Stay ahead with the latest on AI systems, platform engineering, and scalable architecture.</p>
      </div>
      <ul class="post-grid hp-post-grid">
        <?php
        $featured_query = new WP_Query(array(
          'category_name' => 'featured',
          'posts_per_page' => 3,
          'post_status' => 'publish',
          'no_found_rows' => true,
        ));
        if ($featured_query->have_posts()) :
          while ($featured_query->have_posts()) :
            $featured_query->the_post();
        ?>
            <li class="post-grid__item">
              <?php get_template_part('template-parts/post-card', null, array('heading' => 'h3')); ?>
            </li>
        <?php
          endwhile;
        endif;
        wp_reset_postdata();
        ?>
      </ul>
      <div class="hp-view-all">
        <a href="/blog" class="hp-view-all__link">View all articles <span aria-hidden="true">&rarr;</span></a>
      </div>
    </div>
  </section>

  <!-- Contact CTA -->
  <section class="hp-cta">
    <div class="container hp-cta__inner">
      <p class="hp-cta__kicker">Have an AI system or platform challenge you&rsquo;re working through?</p>
      <h2 class="hp-cta__heading">Let&rsquo;s <a href="https://wa.me/9854082826">connect</a> and build something that scales</h2>
      <div class="hp-cta__actions">
        <a href="https://wa.me/9854082826" class="hp-cta__btn">Start the conversation</a>
        <a href="https://t.me/i18587" class="hp-cta__secondary">or message on Telegram</a>
      </div>
    </div>
  </section>

</main>

<?php get_footer(); ?>
