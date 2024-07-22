<?php
/**
 * The template for displaying all pages.
 *
 */
?>

<?php

get_header();

while(have_posts()) {
    the_post();
    echo '<h1>This is a page not a post</h1>';
    echo '<h2>'.the_title().'</h2>';
    the_content();
}

get_footer();