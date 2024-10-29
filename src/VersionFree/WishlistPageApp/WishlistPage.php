<?php

namespace AlgolWishlist\VersionFree\WishlistPageApp;

use AlgolWishlist\Context;

class WishlistPage
{
    /**
     * @var Context
     */
    protected $context;

    public function __construct()
    {
        $this->context = awlContext();
    }

    public function install()
    {
        $pageId = $this->getPageId();
        $page = get_post($pageId);

        if ($page === null) {
            $pageSlug = "wishlist";

            $admins = (new \WP_User_Query([
                'number' => 1,
                'role' => "administrator",
                'fields' => 'ID',
            ]))->get_results();

            if (count($admins) === 0) {
                $this->context->getLogger()->critical(
                    "Wishlist page was not created. Reason: An admin user is missing!"
                );
                return;
            }

            global $wpdb;
            $shortcodeName = (new Shortcode())->getName();
            $pageContent = "<!-- wp:shortcode -->[$shortcodeName]<!-- /wp:shortcode -->";
            if (strlen($pageContent) > 0) {
                $shortcode = str_replace(array('<!-- wp:shortcode -->', '<!-- /wp:shortcode -->'), '', $pageContent);
                $validPageFound = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;",
                        "%{$shortcode}%"
                    )
                );
            } else {
                $validPageFound = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;",
                        $pageSlug
                    )
                );
            }

            if ($validPageFound) {
                $pageId = (int)$validPageFound;
                wp_update_post(
                    [
                        'ID' => $pageId,
                        'post_status' => 'publish',
                    ]
                );

                $this->context->getSettings()->set("wishlist_page", $pageId);
                $this->context->getSettings()->save();
                return;
            }

            // Search for a matching valid trashed page.
            if (strlen($pageContent) > 0) {
                // Search for an existing page with the specified page content (typically a shortcode).
                $trashedPageFound = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;",
                        "%{$pageContent}%"
                    )
                );
            } else {
                // Search for an existing page with the specified page slug.
                $trashedPageFound = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;",
                        $pageSlug
                    )
                );
            }

            if ($trashedPageFound) {
                $pageId = (int)$trashedPageFound;

                wp_update_post(
                    [
                        'ID' => $pageId,
                        'post_status' => 'publish',
                    ]
                );
            } else {
                $pageId = wp_insert_post([
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_author' => (int)$admins[0],
                    'post_name' => sanitize_title_with_dashes(_x("$pageSlug", 'page_slug', 'wc-wishlist')),
                    'post_title' => __('Wishlist', 'wc-wishlist'),
                    'post_content' => $pageContent,
                    'post_parent' => 0,
                    'comment_status' => 'closed',
                ]);

                if ($pageId instanceof \WP_Error) {
                    $this->context->getLogger()->critical(
                        "Wishlist page was not created. Reason: " . $pageId->get_error_message()
                    );
                }
            }

            $this->context->getSettings()->set("wishlist_page", (int)$pageId);
            $this->context->getSettings()->save();
        } else {
            wp_update_post(
                [
                    'ID' => $page->ID,
                    'post_status' => 'publish',
                ]
            );
        }
    }

    public function uninstall()
    {
        wp_delete_post($this->getPageId());
    }

    public function getPageId()
    {
        return (int)($this->context->getSettings()->getOption("wishlist_page"));
    }

    public function getPageSlug()
    {
        $page = get_post($this->getPageId());
        return $page ? $page->post_name : '';
    }
}
