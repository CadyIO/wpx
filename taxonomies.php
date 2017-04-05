<?php
/**
 * Taxonomy functions.
 *
 * @package           WPX
 *
 * @since             1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'wpx_get_first_category' ) ) {

    /**
     * Get the first category associated with the post.
     *
     * @since 1.0.0
     *
     * @param int   $post_id The id of the post.
     * @param array $skip    An array of category slugs to skip when checking.
     *
     * @return WP_Term|null The first category associated with the post.
     */
    function wpx_get_first_category( $post_id, $skip = null ) {
        // Get the categories for the post
        $categories = get_the_terms( $post_id, 'category' );

        // if there are no categories, return null
        if ( ! isset( $categories ) || empty( $categories ) || false == $categories ) {
            return null;
        }

        // Set up the category slug
        $result = null;

        // Iterate the categories
        foreach ( $categories as $category ) {
            // Set the category slug
            $slug = $category->slug;

            // if there are no skip categories, set the category
            if ( ! isset( $skip ) || empty( $skip ) || false == $skip ) {
                $result = $category;

                // We have our category so break out of the loop
                break;
            }
            // Otherwise, iterate the skip categories to check if this is one
            else {
                foreach ( $skip as $skip_category ) {
                    // If the category slug is not featured
                    if ( $skip_category !== $slug ) {
                        $result = $category;

                        // We have our category so break out of the loop
                        break;
                    }
                }
            }
        }

        return $result;
    }

} // wpx_get_first_category

if ( ! function_exists( 'wpx_get_first_category_slug' ) ) {

    /**
     * Get the first category slug associated with the post.
     *
     * @since 1.0.0
     *
     * @param int   $post_id The id of the post.
     * @param array $skip    An array of category slugs to skip when checking.
     *
     * @return string|null The first category slug associated with the post.
     */
    function wpx_get_first_category_slug( $post_id, $skip = null ) {
        // Get the first category
        $category = wpx_get_first_category( $post_id, $skip );

        // Return the slug
        return ( ! isset( $category ) || empty( $category ) || false == $category ) ? null : $category->slug;
    }

} // wpx_get_first_category_slug

if ( ! function_exists( 'wpx_get_featured_posts' ) ) {

    /**
     * Get posts with a category slug of the featured category.
     *
     * @since 1.0.0
     *
     * @param string $post_type              The post type for which to get the featured posts.
     * @param string $featured_category_slug The slug of the 'featured' category.
     *
     * @return array An array of featured posts.
     */
    function wpx_get_featured_posts( $post_type = 'post', $featured_category_slug = 'featured' ) {
        // Get the featured category
        $featured_category = get_category_by_slug( $featured_category_slug );

        // If it doesn't exist, return an empty array
        if ( empty( $featured_category ) || false == $featured_category ) {
            return array();
        }

        // Get the category id
        $category_id = $featured_category->term_id;

        // Set up the post args
        $args = array(
            'post_type'      => $post_type,
            'post_status'    => 'publish',
            'category'       => $category_id,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'numberposts'    => 18
        );

        // Get the posts and return them
        return get_posts( $args );
    }

} // wpx_get_featured_posts

if ( ! function_exists( 'wpx_get_tags_in_use' ) ) {

    /**
     * Get all tags that have posts for the provided type.
     *
     * @since 1.0.0
     *
     * @global wpdb $wpdb WordPress Database Access Abstraction Object.
     *
     * @param string $post_type The post type for which to get the tags in use.
     *
     * @return array An array of WP_Term's for the current tags that have posts.
     */
    function wpx_get_tags_in_use( $post_type = 'post' ) {
        global $wpdb;

        return $wpdb->get_results( $wpdb->prepare(
                    "SELECT COUNT( DISTINCT tr.object_id ) AS count, tt.taxonomy, tt.description, tt.term_taxonomy_id, t.name, t.slug, t.term_id
                    FROM {$wpdb->posts} p
                    INNER JOIN {$wpdb->term_relationships} tr
                        ON p.ID=tr.object_id
                    INNER JOIN {$wpdb->term_taxonomy} tt
                        ON tt.term_taxonomy_id=tr.term_taxonomy_id
                    INNER JOIN {$wpdb->terms} t
                        ON t.term_id=tt.term_taxonomy_id
                    WHERE p.post_type=%s
                        AND tt.taxonomy='post_tag'
                    GROUP BY tt.term_taxonomy_id
                    ORDER BY t.name
                ",
                $post_type
            )
        );
    }

} // wpx_get_tags_in_use

if ( ! function_exists( 'wpx_get_categories_in_use' ) ) {

    /**
     * Get all categories that have posts for the provided type.
     *
     * @since 1.0.0
     *
     * @global wpdb $wpdb WordPress Database Access Abstraction Object.
     *
     * @param string $post_type The post type for which to get the categories in use.
     *
     * @return array An array of WP_Term's for the current categories that have posts.
     */
    function wpx_get_categories_in_use( $post_type = 'post' ) {
        global $wpdb;

        return $wpdb->get_results( $wpdb->prepare(
                    "SELECT COUNT( DISTINCT tr.object_id ) AS count, tt.taxonomy, tt.description, tt.term_taxonomy_id, t.name, t.slug, t.term_id
                    FROM {$wpdb->posts} p
                    INNER JOIN {$wpdb->term_relationships} tr
                        ON p.ID=tr.object_id
                    INNER JOIN {$wpdb->term_taxonomy} tt
                        ON tt.term_taxonomy_id=tr.term_taxonomy_id
                    INNER JOIN {$wpdb->terms} t
                        ON t.term_id=tt.term_taxonomy_id
                    WHERE p.post_type=%s
                        AND tt.taxonomy='category'
                    GROUP BY tt.term_taxonomy_id
                    ORDER BY t.name
                ",
                $post_type
            )
        );
    }

} // wpx_get_categories_in_use

if ( ! function_exists( 'wpx_get_tags_in_use_for_category' ) ) {

    /**
     * Get all tags for the provided category.
     *
     * @since 1.0.0
     *
     * @global wpdb $wpdb WordPress Database Access Abstraction Object.
     *
     * @param int    $category_id The id of the category for which to get the tags.
     * @param string $post_type   The post type for which to get the tags in use.
     *
     * @return array An array of WP_Term's for the provided category.
     */
    function wpx_get_tags_in_use_for_category( $category_id, $post_type = 'post' ) {
        global $wpdb;

        return $wpdb->get_results( $wpdb->prepare(
                    "SELECT DISTINCT
                        terms2.term_id as id,
                        terms2.name as name,
                        terms2.slug as slug,
                        t2.count as count
                    FROM
                    wp_posts as p1
                        LEFT JOIN wp_term_relationships as r1 ON p1.ID = r1.object_ID
                        LEFT JOIN wp_term_taxonomy as t1 ON r1.term_taxonomy_id = t1.term_taxonomy_id
                        LEFT JOIN wp_terms as terms1 ON t1.term_id = terms1.term_id,
                    wp_posts as p2
                        LEFT JOIN wp_term_relationships as r2 ON p2.ID = r2.object_ID
                        LEFT JOIN wp_term_taxonomy as t2 ON r2.term_taxonomy_id = t2.term_taxonomy_id
                        LEFT JOIN wp_terms as terms2 ON t2.term_id = terms2.term_id
                    WHERE (
                        t1.taxonomy = 'category' AND
                        p1.post_status = 'publish' AND
                        p1.post_type = '$post_type' AND
                        terms1.term_id = %s AND
                        t2.taxonomy = 'post_tag' AND
                        p2.post_status = 'publish' AND
                        p1.ID = p2.ID
                    )
                    ORDER by terms2.name
                ",
                $category_id
            )
        );
    }

} // wpx_get_tags_in_use_for_category