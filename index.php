<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package v5imraan
 */

get_header();
?>

<section class="card-box">
    <div class="container">
        <div class="cards-heading">
            <h2>Tech Insights From My Blog</h2>
            <!-- <span class="cards-smtext">sub title goes here</span> -->
        </div>
        <ul class="flexbox-col3">
            <?php
            $args = array(
                'posts_per_page' => 9, 
                'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
            );

            $query = new WP_Query($args);
            if ($query->have_posts()) :
                while ($query->have_posts()) :
                    $query->the_post();
                    ?>
                    <li>
                        <div class="flexbox-col3-box">
                         
                            <h4><?php the_title(); ?></h4>
                            <p class="list-para"><?php echo wp_trim_words( get_the_excerpt(), 20, '...' ); ?></p>
                            <a class="cards-cta" href="<?php the_permalink(); ?>">
                                <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                </svg> Read More
                            </a>
                        </div>
                    </li>
                    <?php
                endwhile;
            endif;
            wp_reset_postdata();
            ?>
        </ul>

        <div class="pagination">
            <?php

            echo paginate_links(array(
                'total' => $query->max_num_pages, 
                'current' => max(1, get_query_var('paged')), 
                'format' => '?paged=%#%', 
                'prev_text' => '&laquo; Prev',
                'next_text' => 'Next &raquo;',
            ));
            ?>
        </div>
    </div>
</section>


<?php
get_footer();
