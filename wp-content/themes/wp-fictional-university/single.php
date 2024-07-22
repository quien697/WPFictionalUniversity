<?php
/**
 * The template for displaying all single post.
 *
 */
?>

<?php

get_header();

while(have_posts()) {
    the_post();
    echo '<h2>'.the_title().'</h2>';
    the_content();

}

get_footer();