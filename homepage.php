<?php
/* Template Name: Homepage
*
*
*/
get_header(); ?>

<main>

  <section>
    <div class="container">
      <h1 class="ourfeatures-heading">
        <?php 
        echo 'I build AI-driven systems, scalable platforms, and high-performance engineering teams.';
        ?>
      </h1>
      <div class="ourfeatures-dflex">
        <div class="ourfeatures-dflex__left">
          <div class="ourfeatures-box">
            <span class="ourfeatures-gradient"><small></small> Imranul Haque Mazumder</span>
            <h4>I build end-to-end AI-driven systems.</h4>
            <p>
              Delivering measurable business outcomes through scalable platforms, intelligent automation, and high-performance engineering teams.
            </p>
            <img class="img-responsive ourfeatures-img" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/img/imrn.png" alt="Portrait of Imranul Haque Mazumder" loading="lazy" decoding="async">
          </div>
        </div>
        <div class="ourfeatures-dflex__right">
          <div class="ourfeatures-sm__dflex">
            <div class="ourfeatures-sm__dflexleft">
              <div class="ourfeatures-sm__box">
                <h3 class="pdt-0">13+ yrs</h3>
                <h4 class="pdt-0">of Experience</h4>
                <p>
                  AI Systems, Full Stack, DevOps, Platform Architecture, Security, CRO, Analytics, SEO, Martech, CRM and CMS.
                </p>
              </div>
            </div>
            <div class="ourfeatures-sm__dflexright">
              <div class="ourfeatures-sm__box">
                <h3 class="pdt-0">~80%</h3>
                <h4 class="pdt-0">Cost Reduction</h4>
                <p>
                  Reduced cloud infrastructure costs by ~80% through architectural and operational optimizations.
                </p>
              </div>
            </div>
          </div>
          <div class="ourfeatures-box green-gradient mg-25">
            <h3>150+</h3>
            <h4>Projects Delivered</h4>
            <p>
              Scalable, AI-driven solutions for 150+ projects across enterprise and Fortune 500 domains — finance, hospitality, technology, and e-commerce.
              Led cross-functional teams, built delivery systems, and drove measurable outcomes for global clients.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="text-section">
    <div class="container">
      <h3>I had the pleasure to work with</h3>
    </div>
  </section>

  <section class="marquee-box">
    <div class="container logos-marquee">
      <div class="logos-track">
        <img src="<?php echo get_template_directory_uri(); ?>/img/amex.png" alt="amex" class="logo logo-width">
        <img src="<?php echo get_template_directory_uri(); ?>/img/fox-sports.png" alt="fox-sports" class="logo logo-width">
        <img src="<?php echo get_template_directory_uri(); ?>/img/delta.png" alt="delta" class="logo logo-width">
        <img src="<?php echo get_template_directory_uri(); ?>/img/hilton.png" alt="hilton" class="logo logo-width">
        <img src="<?php echo get_template_directory_uri(); ?>/img/marriott.png" alt="marriott" class="logo logo-width">
        <img src="<?php echo get_template_directory_uri(); ?>/img/zeta.png" alt="zeta" class="logo logo-width">
        <img src="<?php echo get_template_directory_uri(); ?>/img/amex.png" alt="amex" class="logo logo-width">
        <img src="<?php echo get_template_directory_uri(); ?>/img/fox-sports.png" alt="fox-sports" class="logo logo-width">
        <img src="<?php echo get_template_directory_uri(); ?>/img/delta.png" alt="delta" class="logo logo-width">
        <img src="<?php echo get_template_directory_uri(); ?>/img/hilton.png" alt="hilton" class="logo logo-width">
        <img src="<?php echo get_template_directory_uri(); ?>/img/marriott.png" alt="marriott" class="logo logo-width">
        <img src="<?php echo get_template_directory_uri(); ?>/img/zeta.png" alt="zeta" class="logo logo-width">
      </div>
    </div>
  </section>

  <!-- Flagship Systems -->
  <section id="projects" class="card-box">
    <div class="container">
      <div class="cards-heading">
        <h2>Flagship Systems</h2>
        <span class="cards-smtext">AI-driven systems and platforms built for enterprise scale and measurable outcomes.</span>
      </div>
      <ul class="flexbox-col3">
        <?php
        $projects = [
          [
            'title' => 'Agentic AI — Analyst',
            'text' => 'LangChain + Tavily + Next.js + TypeScript + Tailwind | Autonomous AI agent that independently surfs the internet, analyzes target bank data (QBRs, ARs, etc.), derives meaningful insights, and recommends internal products to address identified pain points.',
            'link' => '#'
          ],
          [
            'title' => 'Agentic AI — Project Manager',
            'text' => 'Python + FastAPI + OpenAI API + Whisper + JIRA API + MS Graph API + OAuth 2.0 + Supabase + Next.js + Tailwind + Google GenAI | Autonomous agent handling daily standups, meeting transcription, action item tracking, and JIRA updates — reducing PM overhead by 40%.',
            'link' => '#'
          ],
          [
            'title' => 'Infrastructure Optimization',
            'text' => 'AWS + Terraform + Docker + Kubernetes | Reduced cloud infrastructure costs by ~80% through architectural redesign, right-sizing, and operational automation — while improving performance and scalability across production environments.',
            'link' => '#'
          ],
          [
            'title' => 'Developer Productivity Analytics',
            'text' => 'Java + Spring Boot + Maven + PostgreSQL + Metabase + Bitbucket API | Ingests millions of data points from Bitbucket to surface engineering bottlenecks and productivity trends. Delivered to SLT via Metabase dashboard for real-time insight.',
            'link' => '#'
          ],
          [
            'title' => 'Programmable Content Platform',
            'text' => 'PHP + MySQL + JavaScript + WP + ACF | Low-code, no-code system that eliminated developer bottlenecks — enabling business teams to launch targeted content hubs with dynamic messaging. Cut production time from days to under 30 minutes.',
            'link' => '#'
          ],
          [
            'title' => 'AI Website & Image Optimizer',
            'text' => 'Node.js + React + WebAssembly | Automated performance audit and AI-driven image optimization pipeline. Analyzes assets, suggests and applies optimizations — significantly improving page load times and user experience.',
            'link' => '#'
          ],
        ];
        foreach ($projects as $proj) : ?>
          <li>
            <div class="flexbox-col3-box">
              <h4><?php echo esc_html($proj['title']); ?></h4>
              <p><?php echo esc_html($proj['text']); ?></p>
              <?php if ($proj['link'] !== '#') : ?>
                <a href="<?php echo esc_url($proj['link']); ?>" class="cards-cta">View Project</a>
              <?php endif; ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <!-- Awards & Recognition -->
  <section id="awards" class="flexbox-col2">
    <div class="container flexy">
      <div class="flexbox-col-left">
        <h2>Awards &amp; Promotions</h2>
        <div class="card-inline-text js-award" data-award="a1" tabindex="0" role="button" aria-pressed="true">
          <div class="card-inline-left">
            <img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/img/awards.webp" alt="Shining Star Award" loading="lazy" decoding="async">
          </div>
          <div class="card-inline-right">
            <h4>Shining Star Award</h4>
            <p>2024</p>
          </div>
        </div>
        <div class="card-inline-text js-award" data-award="a2" tabindex="0" role="button" aria-pressed="false">
          <div class="card-inline-left">
            <img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/img/awards.webp" alt="Ultimate Team Award" loading="lazy" decoding="async">
          </div>
          <div class="card-inline-right">
            <h4>Ultimate Team Award</h4>
            <p>2023</p>
          </div>
        </div>
        <div class="card-inline-text js-award" data-award="a3" tabindex="0" role="button" aria-pressed="false">
          <div class="card-inline-left">
            <img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/img/promotions.webp" alt="Promotion to Associate Director" loading="lazy" decoding="async">
          </div>
          <div class="card-inline-right">
            <h4>Promoted to Associate Director</h4>
            <p>2022</p>
          </div>
        </div>
        <div class="card-inline-text js-award" data-award="a5" tabindex="0" role="button" aria-pressed="false">
          <div class="card-inline-left">
            <img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/img/awards.webp" alt="Trailblazer Award" loading="lazy" decoding="async">
          </div>
          <div class="card-inline-right">
            <h4>Trailblazer Award</h4>
            <p>2021</p>
          </div>
        </div>
        <div class="card-inline-text js-award" data-award="a6" tabindex="0" role="button" aria-pressed="false">
          <div class="card-inline-left">
            <img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/img/promotions.webp" alt="Promotion to Team Lead" loading="lazy" decoding="async">
          </div>
          <div class="card-inline-right">
            <h4>Promoted to Team Lead</h4>
            <p>2019</p>
          </div>
        </div>
        <div class="card-inline-text js-award" data-award="a7" tabindex="0" role="button" aria-pressed="false">
          <div class="card-inline-left">
            <img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/img/awards.webp" alt="iLead Award" loading="lazy" decoding="async">
          </div>
          <div class="card-inline-right">
            <h4>iLead Award</h4>
            <p>2018</p>
          </div>
        </div>
      </div>
      <div class="flexbox-col-right">
        <div class="award-heading-spacer" aria-hidden="true"></div>

        <div class="award-details" id="a1details" style="display: block;">
          <p>Recognized for exceptional performance and outstanding contributions to engineering outcomes — delivering high-impact systems that moved the needle on business results.</p>
        </div>

        <div class="award-details" id="a2details" style="display: none;">
          <p>Awarded for driving exceptional cross-functional collaboration that brought a critical, high-stakes project to completion under tight deadlines and complex stakeholder dynamics.</p>
        </div>

        <div class="award-details" id="a3details" style="display: none;">
          <p>Promoted in recognition of engineering leadership, platform delivery expertise, and a consistent track record of mentoring and growing high-performing teams.</p>
        </div>

        <div class="award-details" id="a5details" style="display: none;">
          <p>Awarded for pioneering AI-driven approaches and platform innovations that set new delivery standards across the organization.</p>
        </div>

        <div class="award-details" id="a6details" style="display: none;">
          <p>First promotion to Team Lead — recognizing demonstrated leadership potential, technical depth, and consistent high performance as an individual contributor.</p>
        </div>

        <div class="award-details" id="a7details" style="display: none;">
          <p>Successfully retained a critical account by rapidly acquiring and delivering niche MarTech expertise. With no available replacement, stepped up, excelled, and earned direct accolades from the client.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Award details switching is handled by js/main.js (js-award / data-award hooks). -->

  <!-- Blog Section -->
  <section class="card-box">
    <div class="container">
      <div class="cards-heading">
        <h2>Tech Insights From My Blog</h2>
        <span class="cards-smtext">Stay ahead with the latest on AI systems, platform engineering, and scalable architecture.</span>
      </div>
      <ul class="flexbox-col3">
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
            <li>
              <?php get_template_part('template-parts/post-card', null, array('heading' => 'h3')); ?>
            </li>
        <?php
          endwhile;
        endif;
        wp_reset_postdata();
        ?>
      </ul>
    </div>
    <div class="subscribe-section">
      <div class="container">
        <div class="subscribe-block">
          <div class="subscribe-inblock">
            <p>Have an AI system or platform challenge you're working through?</p>
            <h2>Let's <a href="https://wa.me/9854082826">connect</a> and build something that scales</h2>
            <div class="form-subscribe"></div>
          </div>
        </div>
      </div>
    </div>
  </section>

</main>

<?php get_footer(); ?>
