<?php

/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package v5imraan
 */

get_header();
?>

<section>
    <div class="zwvf-blogpost__hb">
        <div class="container">
            <div class="zwvf-blogpost__hbflexbox">
                <div class="zwvf-blogpost__hbflexboxleft">
                    <div class="zwvf-blogpost__hbcontent">
                        <div class="breadcrumb-block">
                            <a href="/blog">
                                <img width="6" height="10" src="https://imraan.in/wp-content/uploads/2025/01/white-arrow-right.svg" alt="Arrow">
                                Blog
                            </a>
                        </div>
                        <h1><?php the_title(); ?></h1>
                        <ul>
                            <?php
                            $categories = get_the_category();
                            if ($categories) {
                                foreach ($categories as $category) {
                                    echo '<li><a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a></li>';
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="zwvf-blogpost__hbflexboxright">
                    <?php if (has_post_thumbnail()) : ?>
                        <img class="img-responsive" width="544" height="512" src="<?php the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>" fetchpriority="high">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bloghub-body__cntr">
    <div class="container">
        <div class="bloghub-container">
            <div class="bloghub-sticky-nav">
                <article class="bloghub-article__tag">
                    <?php
                    $author_id = get_the_author_meta('ID');
                    $author_name = get_the_author_meta('display_name', $author_id);
                    ?>
                    <h5><?php echo get_the_date(); ?></h5> 
                    <p>By <a href="<?php echo get_author_posts_url($author_id); ?>" title="<?php echo esc_attr($author_name); ?>">
                            <?php echo $author_name; ?>
                        </a></p> <time><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?></time> <!-- Display the time ago -->
                </article>

            </div>
            <div class="bloghub-content">
                <section class="bloghub-thecontent__block">
                    <?php the_content(); ?> <!-- Dynamically display the post content -->
                </section>

                <!-- <section>
                    <div class="blogpost-tags__group">
                        Tags: 
                        <?php
                        $tags = get_the_tags();
                        if ($tags) {
                            foreach ($tags as $tag) {
                                echo '<span><a href="' . esc_url(get_tag_link($tag->term_id)) . '">' . esc_html($tag->name) . '</a></span>';
                            }
                        }
                        ?>
                    </div>
                </section> -->

                <!-- <section>
                    <div class="blogpost-aboutauthor__group">
                        <div class="blogpost-aboutauthor__flexbox">
                            <div class="blogpost-aboutauthor__flexboxleft">
                                <img width="120" height="120" src="<?php echo get_avatar_url(get_the_author_meta('ID')); ?>" alt="Author Image" loading="lazy">
                                <h4><?php the_author(); ?></h4>
                                <p><?php the_author_meta('description'); ?></p>
                            </div>
                            <div class="blogpost-aboutauthor__flexboxright">
                                <h4 class="blogpost-aboutauthor__title">About Author</h4> 
                                <p><?php the_author_meta('description'); ?></p> 
                            </div>
                        </div>
                    </div>
                </section> -->
            </div>
        </div>
    </div>
</section>


<?php
get_footer();
