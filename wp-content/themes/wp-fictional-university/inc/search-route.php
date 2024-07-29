<?php
/**
 * The file to handle search.
 *
 */

/**
 *
 * @throws Exception
 */
function wp_fictional_university_search_results($data): array {
    $mainQuery = new WP_Query(array(
        'post_type' => array('post', 'page', 'professor', 'program', 'campus', 'event'),
        's' => sanitize_text_field($data['term'])
    ));

    $results = array(
        'generalInfo' => array(),
        'professors' => array(),
        'programs' => array(),
        'events' => array(),
        'campuses' => array()
    );

    while($mainQuery->have_posts()) {
        $mainQuery->the_post();

        if (get_post_type() == 'post' OR get_post_type() == 'page') {
            $results['generalInfo'][] = array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'postType' => get_post_type(),
                'authorName' => get_the_author()
            );
        }

        if (get_post_type() == 'professor') {
            $results['professors'][] = array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'image' => get_the_post_thumbnail_url(0, 'professorLandscape')
            );
        }

        if (get_post_type() == 'program') {
            $relatedCampuses = get_field('related_campus');

            if ($relatedCampuses) {
                foreach($relatedCampuses as $campus) {
                    $results['campuses'][] = array(
                        'title' => get_the_title($campus),
                        'permalink' => get_the_permalink($campus)
                    );
                }
            }

            $results['programs'][] = array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'id' => get_the_id()
            );
        }

        if (get_post_type() == 'campus') {
            $results['campuses'][] = array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink()
            );
        }

        if (get_post_type() == 'event') {
            $eventDate = new DateTime(get_field('event_date'));
            if (has_excerpt()) {
                $description = get_the_excerpt();
            } else {
                $description = wp_trim_words(get_the_content(), 18);
            }

            $results['events'][] = array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'month' => $eventDate->format('M'),
                'day' => $eventDate->format('d'),
                'description' => $description
            );
        }
    }

    if ($results['programs']) {
        $programsMetaQuery = array('relation' => 'OR');

        foreach($results['programs'] as $item) {
            $programsMetaQuery[] = array(
                'key' => 'related_programs',
                'compare' => 'LIKE',
                'value' => '"' . $item['id'] . '"'
            );
        }

        $programRelationshipQuery = new WP_Query(array(
            'post_type' => array('professor', 'event'),
            'meta_query' => $programsMetaQuery
        ));

        while($programRelationshipQuery->have_posts()) {
            $programRelationshipQuery->the_post();

            if (get_post_type() === 'event') {
                $eventDate = new DateTime(get_field('event_date'));
                if (has_excerpt()) {
                    $description = get_the_excerpt();
                } else {
                    $description = wp_trim_words(get_the_content(), 18);
                }

                $results['events'][] = array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'month' => $eventDate->format('M'),
                    'day' => $eventDate->format('d'),
                    'description' => $description
                );
            }

            if (get_post_type() == 'professor') {
                $results['professors'][] = array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'image' => get_the_post_thumbnail_url(0, 'professorLandscape')
                );
            }
        }

        $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
        $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
    }

    return $results;
}

/**
 *
 */
function wp_fictional_university_register_search(): void {
    register_rest_route('wp_fictional_university/v1', 'search', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'wp_fictional_university_search_results'
    ));
}
add_action('rest_api_init', 'wp_fictional_university_register_search');