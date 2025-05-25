<?php
/* Template Name: Homepage
*
*
*/
get_header(); ?>

<main>

  <section>
    <div class="container">
      <h1 class="ourfeatures-heading">Driven by curiosity, solving complex problems</h1>
      <div class="ourfeatures-dflex">
        <div class="ourfeatures-dflex__left">
          <div class="ourfeatures-box">
            <span class="ourfeatures-gradient"><small></small> Imranul Haque Mazumder</span>
            <h4>I build end to end solutions.</h4>
            <p>
              Turning real-world challenges into opportunities, producing tangible business outcomes and improving the bottom line.
            </p>
            <img class="img-responsive ourfeatures-img" src="<?php echo get_template_directory_uri(); ?>/img/imrn.png" alt="imrans image">
          </div>
        </div>
        <div class="ourfeatures-dflex__right">
          <div class="ourfeatures-sm__dflex">
            <div class="ourfeatures-sm__dflexleft">
              <div class="ourfeatures-sm__box">
                <h3 id="pdt--0">12 yrs</h3>
                <h4 id="pdt--0">of Experience</h4>
                <p>
                  Full Stack Dev, DevOps, Orchestration, Security, Complaince, CRO, Analytics, SEO, Martech, CRM, CMS and AI.
                </p>
              </div>
            </div>
            <div class="ourfeatures-sm__dflexright">
              <div class="ourfeatures-sm__box">
                <h3 id="pdt--0">70%</h3>
                <h4 id="pdt--0">Cost Savings</h4>
                <p>
                  Optimized cloud and infrastructure costs, saving 50% while enhancing performance and scalability.
                </p>


              </div>
            </div>
          </div>
          <div class="ourfeatures-box green-gradient mg-25">
            <h3>150+</h3>
            <h4>Successful Projects</h4>
            <p>
              Delivered scalable and impactful solutions for over 150 projects across industries like finance, hospitality, technology, and e-commerce.
              Led cross-functional teams, optimized workflows, and achieved tangible business outcomes for global clients.
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

  <!-- Expertise Section -->
  <!-- <section id="expertise" class="ourfeatures-section">
    <h2 class="ourfeatures-heading">My Expertise</h2>
    <div class="ourfeatures-dflex">
      <div class="ourfeatures-box">
        <h3>Full-Stack Development</h3>
        <p>React, Next.js, Java, Spring Boot, PHP, WordPress</p>
      </div>
      <div class="ourfeatures-box">
        <h3>Cloud &amp; DevOps</h3>
        <p>AWS, Docker, Kubernetes, CI/CD pipelines</p>
      </div>
      <div class="ourfeatures-box">
        <h3>AI &amp; Automation</h3>
        <p>OpenAI API, GPT integration, process automations</p>
      </div>
      <div class="ourfeatures-box">
        <h3>Leadership &amp; PMP®</h3>
        <p>Team leadership, Agile methodologies, PMP certified</p>
      </div>
    </div>
  </section> -->

  <!-- Featured Projects -->
  <!-- <section id="projects" class="card-box">
    <div class="container">
      <div class="cards-heading">
        <h2>Featured Projects</h2>
      </div>
      <ul class="flexbox-col3">
        <?php
        $projects = [
          ['title' => 'Zeta Careers Site', 'text' => 'React + Tailwind + CMS | 200k+ MAUs', 'link' => '#'],
          ['title' => 'AI Document Parser', 'text' => 'Automated workflows, 90% manual effort reduced', 'link' => '#'],
          ['title' => 'Amex MarTech Portal', 'text' => 'Improved lead flow by 25%', 'link' => '#'],
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
  </section> -->

  <!-- Awards & Recognition -->
  <!-- <section id="awards" class="flexbox-col2">
    <div class="container flexy">
      <div class="flexbox-col-left">
        <h2>Awards &amp; Promotions</h2>
        <div class="card-inline-text" id="a1">
          <div class="card-inline-left">
            <img src="<?php echo get_template_directory_uri(); ?>/img/awards.webp" alt="Trailblazer Award">
          </div>
          <div class="card-inline-right">
            <h4>Shining Star Award</h4>
            <p>2024</p>
          </div>
        </div>
        <div class="card-inline-text" id="a2">
          <div class="card-inline-left">
            <img src="<?php echo get_template_directory_uri(); ?>/img/awards.webp" alt="Ultimate Team Award">
          </div>
          <div class="card-inline-right">
            <h4>Ultimate Team Award</h4>
            <p>2023</p>
          </div>
        </div>
        <div class="card-inline-text" id="a3">
          <div class="card-inline-left">
            <img src="<?php echo get_template_directory_uri(); ?>/img/promotions.webp" alt="Trailblazer Award">
          </div>
          <div class="card-inline-right">
            <h4>Promoted to Team Lead</h4>
            <p>2022</p>
          </div>
        </div>
        <div class="card-inline-text" id="a4">
          <div class="card-inline-left">
            <img src="<?php echo get_template_directory_uri(); ?>/img/awards.webp" alt="Trailblazer Award">
          </div>
          <div class="card-inline-right">
            <h4>Mountain Mover Award</h4>
            <p>2022</p>
          </div>
        </div>
        <div class="card-inline-text" id="a5">
          <div class="card-inline-left">
            <img src="<?php echo get_template_directory_uri(); ?>/img/awards.webp" alt="Trailblazer Award">
          </div>
          <div class="card-inline-right">
            <h4>Trailblazer Award</h4>
            <p>2021</p>
          </div>
        </div>
        <div class="card-inline-text" id="a6">
          <div class="card-inline-left">
            <img src="<?php echo get_template_directory_uri(); ?>/img/promotions.webp" alt="Trailblazer Award">
          </div>
          <div class="card-inline-right">
            <h4>Promoted to Team Lead</h4>
            <p>2019</p>
          </div>
        </div>
        <div class="card-inline-text" id="a7">
          <div class="card-inline-left">
            <img src="<?php echo get_template_directory_uri(); ?>/img/awards.webp" alt="Trailblazer Award">
          </div>
          <div class="card-inline-right">
            <h4>iLead Award</h4>
            <p>2018</p>
          </div>
        </div>
      </div>
      <div class="flexbox-col-right">
        <h2>&nbsp;</h2>

        <div class="card-inline-text" id="a7details">
          <p>Successfully retained a critical account by quickly learning and delivering MarTech solutions. Replacement was not available as it a niche skill and availability was very scarce. Excelled at it, continued and got even accolades from the client.</p>
        </div>
      </div>
    </div>
  </section> -->

  <!-- Awards & Recognition -->
<section id="awards" class="flexbox-col2">
  <div class="container flexy">
    <div class="flexbox-col-left">
      <h2>Awards &amp; Promotions</h2>
      <div class="card-inline-text" id="a1" onmouseover="showAwardDetails('a1')">
        <div class="card-inline-left">
          <img src="<?php echo get_template_directory_uri(); ?>/img/awards.webp" alt="Trailblazer Award">
        </div>
        <div class="card-inline-right">
          <h4>Shining Star Award</h4>
          <p>2024</p>
        </div>
      </div>
      <div class="card-inline-text" id="a2" onmouseover="showAwardDetails('a2')">
        <div class="card-inline-left">
          <img src="<?php echo get_template_directory_uri(); ?>/img/awards.webp" alt="Ultimate Team Award">
        </div>
        <div class="card-inline-right">
          <h4>Ultimate Team Award</h4>
          <p>2023</p>
        </div>
      </div>
      <div class="card-inline-text" id="a3" onmouseover="showAwardDetails('a3')">
        <div class="card-inline-left">
          <img src="<?php echo get_template_directory_uri(); ?>/img/promotions.webp" alt="Promotion to Team Lead">
        </div>
        <div class="card-inline-right">
          <h4>Promoted to Team Lead</h4>
          <p>2022</p>
        </div>
      </div>

      <div class="card-inline-text" id="a5" onmouseover="showAwardDetails('a5')">
        <div class="card-inline-left">
          <img src="<?php echo get_template_directory_uri(); ?>/img/awards.webp" alt="Trailblazer Award">
        </div>
        <div class="card-inline-right">
          <h4>Trailblazer Award</h4>
          <p>2021</p>
        </div>
      </div>
      <div class="card-inline-text" id="a6" onmouseover="showAwardDetails('a6')">
        <div class="card-inline-left">
          <img src="<?php echo get_template_directory_uri(); ?>/img/promotions.webp" alt="Promotion to Team Lead">
        </div>
        <div class="card-inline-right">
          <h4>Promoted to Team Lead</h4>
          <p>2019</p>
        </div>
      </div>
      <div class="card-inline-text" id="a7" onmouseover="showAwardDetails('a7')">
        <div class="card-inline-left">
          <img src="<?php echo get_template_directory_uri(); ?>/img/awards.webp" alt="iLead Award">
        </div>
        <div class="card-inline-right">
          <h4>iLead Award</h4>
          <p>2018</p>
        </div>
      </div>
    </div>
    <div class="flexbox-col-right">
      <h2>&nbsp;</h2>

      <!-- Award Details -->
      <div class="award-details" id="a1details" style="display: block;">
        <p>Recognized for exceptional performance and outstanding contributions to the team's success.</p>
      </div>
      
      <div class="award-details" id="a2details" style="display: none;">
        <p>Awarded for demonstrating exceptional teamwork and collaboration that led to the successful completion of a critical project under tight deadlines.</p>
      </div>
      
      <div class="award-details" id="a3details" style="display: none;">
        <p>Promoted to Team Lead in recognition of leadership skills, technical expertise, and ability to mentor junior team members effectively.</p>
      </div>

      
      <div class="award-details" id="a5details" style="display: none;">
        <p>Awarded for pioneering innovative solutions and demonstrating forward-thinking approaches that set new standards in the organization.</p>
      </div>
      
      <div class="award-details" id="a6details" style="display: none;">
        <p>First promotion to Team Lead role, recognizing demonstrated leadership potential and consistent high performance as an individual contributor.</p>
      </div>
      
      <div class="award-details" id="a7details" style="display: none;">
        <p>Successfully retained a critical account by quickly learning and delivering MarTech solutions. Replacement was not available as it a niche skill and availability was very scarce. Excelled at it, continued and got even accolades from the client.</p>
      </div>
    </div>
  </div>
</section>

<script>
  function showAwardDetails(awardId) {
    document.querySelectorAll('.award-details').forEach(detail => {
      detail.style.display = 'none';
    });

    const detailsId = awardId + 'details';
    const detailsElement = document.getElementById(detailsId);
    if (detailsElement) {
      detailsElement.style.display = 'block';
    }
  }
  document.addEventListener('DOMContentLoaded', function() {
    showAwardDetails('a1');
  });
</script>




  <section class="card-box">
    <div class="container">
      <div class="cards-heading">
        <h2>Tech Insights From My Blog</h2>
        <span class="cards-smtext">Stay ahead with the latest trends, tools & strategies in development, AI & beyond.</span>
      </div>
      <ul class="flexbox-col3">
        <?php
        // Fetch featured posts
        $args = array(
          'category_name' => 'featured',
          'posts_per_page' => 3
        );

        $query = new WP_Query($args);
        if ($query->have_posts()) :
          while ($query->have_posts()) :
            $query->the_post();
        ?>
            <li>
              <div class="flexbox-col3-box">
                <?php
                if (has_post_thumbnail()) : ?>
                  <!-- <img width="84" height="84" src="<?php the_post_thumbnail_url('thumbnail'); ?>" alt="<?php the_title_attribute(); ?>"> -->
                <?php else : ?>
                  <!-- <img width="84" height="84" src="<?php echo get_template_directory_uri(); ?>/img/ideas.png" alt="Default Image"> -->
                <?php endif; ?>
                <h4><?php the_title(); ?></h4>
                <p><?php echo get_the_excerpt(); ?></p>
                <a class="cards-cta" href="<?php the_permalink(); ?>"><svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="38" height="38" rx="19" fill="url(#paint0_linear_15_1041)"></rect>
                    <g clip-path="url(#clip0_15_1041)">
                      <path d="M23.6557 16.8139L14.72 25.7497L13.252 24.2817L22.1866 15.3459H14.3119V13.2695H25.7321V24.6897H23.6557V16.8139Z" fill="true"></path>
                    </g>
                    <defs>
                      <linearGradient id="paint0_linear_15_1041" x1="39.3571" y1="5.62961" x2="-3.06271" y2="8.58385" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#22D1EE"></stop>
                        <stop offset="1" stop-color="#C5FF41"></stop>
                      </linearGradient>
                      <clipPath id="clip0_15_1041">
                        <rect width="13" height="13" fill="white" transform="translate(13 13)"></rect>
                      </clipPath>
                    </defs>
                  </svg> Read More</a>
              </div>
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
            <p>Have a concept you’re passionate about?</p>
            <h2>Let’s <a href="https://wa.me/9854082826">connect</a> and make it happen</h2>
            <div class="form-subscribe">

              <!-- <button class="btn-green-linear">Get in Touch
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22 11.0003L18.4791 7.47949V10.3074H0V11.6933H18.4791V14.5213L22 11.0003Z" fill="true"></path>
                            </svg>
                        </button> -->

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

</main>

<?php get_footer(); ?>