<?php

namespace AlgolWishlist\VersionFree\WishlistPageApp;

use AlgolWishlist\Context;
use AlgolWishlist\CustomizerExtensions\CustomizerExtensions;
use AlgolWishlist\Repositories\ItemsOfWishlist\ItemsOfWishlistRepositoryWordpress;
use AlgolWishlist\Repositories\Wishlists\WishlistEntity;
use AlgolWishlist\Repositories\Wishlists\WishlistsRepositoryWordpress;
use AlgolWishlist\UrlBuilder;
use AlgolWishlist\WordpressCurrentRequest;

class Shortcode
{
    protected $name = "awl_products_of_wishlist";

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var CustomizerExtensions
     */
    protected $customizer;

    public function __construct()
    {
        $this->context = awlContext();
        $this->customizer = new CustomizerExtensions();
    }

    public function register()
    {
        add_shortcode($this->name, [$this, 'getContent']);
        add_action("wp_enqueue_scripts", [$this, "registerScriptsAndStyles"]);
    }

    public function getName()
    {
        return $this->name;
    }

    public function registerScriptsAndStyles()
    {
        global $wpdb;

        wp_register_script(
            'algol_wishlist_client_app',
            ALGOL_WISHLIST_PLUGIN_URL . '/src/assets/client-app/client-build-app.js',
            [],
            ALGOL_WISHLIST_VERSION,
            true
        );
        $themeOptions = $this->customizer->getThemeOptions();
        $wishlist = $this->getWishlistEntity();
        if ($wishlist) {
            $itemsOfWishlistRepositoryWordpress = new ItemsOfWishlistRepositoryWordpress($wpdb);
            $wishlistProducts = $itemsOfWishlistRepositoryWordpress->getAllByWishlistId($wishlist->id);
            $firstProductImageUrl = '';
            foreach ($wishlistProducts as $wishlistProduct) {
                if ($thumbnailId = get_post_thumbnail_id($wishlistProduct->productId)) {
                    $firstProductImageUrl = wp_get_attachment_image_url($thumbnailId);
                    break;
                }
            }
        }
        $share = [
            'url' => $wishlist ? (new UrlBuilder())->getUrlToWishlist($wishlist) : "",
            'facebookUrl' => '',
            'twitterUrl' => '',
            'pinterestUrl' => '',
            'emailUrl' => '',
            'whatsappUrl' => '',
            'shareBlockTitle' => $themeOptions->exactWishlist->shareBlockTitle,
            'shareBlockPosition' => $themeOptions->exactWishlist->shareBlockPosition,
            'facebookShareIcon' => $themeOptions->exactWishlist->facebookShareIcon,
            'facebookShareIconCustom' => wp_get_attachment_image_url($themeOptions->exactWishlist->facebookShareIconCustom),
            'twitterShareIcon' => $themeOptions->exactWishlist->twitterShareIcon,
            'twitterShareIconCustom' => wp_get_attachment_image_url($themeOptions->exactWishlist->twitterShareIconCustom),
            'pinterestShareIcon' => $themeOptions->exactWishlist->pinterestShareIcon,
            'pinterestShareIconCustom' => wp_get_attachment_image_url($themeOptions->exactWishlist->pinterestShareIconCustom),
            'emailShareIcon' => $themeOptions->exactWishlist->emailShareIcon,
            'emailShareIconCustom' => wp_get_attachment_image_url($themeOptions->exactWishlist->emailShareIconCustom),
            'whatsappShareIcon' => $themeOptions->exactWishlist->whatsappShareIcon,
            'whatsappShareIconCustom' => wp_get_attachment_image_url($themeOptions->exactWishlist->whatsappShareIconCustom),
        ];
        if ($wishlist) {
            $title = __('My wishlist', 'wc-wishlist');
            $url = (new UrlBuilder())->getUrlToWishlist($wishlist);
            $share['facebookUrl'] = 'https://www.facebook.com/sharer.php?u=' . urlencode($url) . '&p[title]=' . esc_attr($title);
            $share['twitterUrl'] = 'https://twitter.com/share?url=' . urlencode($url) . '&text=' . esc_attr($title);
            $share['emailUrl'] = 'mailto:?subject=' . esc_attr($title) . '&body=' . urlencode($url) . '&title=' . esc_attr($title);
            $share['pinterestUrl'] = 'https://pinterest.com/pin/create/button/?url=' . urlencode($url) . '&description=' . esc_attr($title) .
                '&amp;media=' . esc_attr($firstProductImageUrl);
            if (wp_is_mobile()) {
                $share['whatsappUrl'] = 'whatsapp://send?text=' . $title . ' - ' . urlencode($url);
            } else {
                $share['whatsappUrl'] = 'https://web.whatsapp.com/send?text=' . $title . ' - ' . urlencode($url);
            }
        }

        wp_localize_script(
            'algol_wishlist_client_app',
            'algolWishlistClientAppData',
            [
                'host' => trailingslashit(get_site_url()),

                // Yes, you need 2 nonces, first for WordPress authorization and second for guests that WooCommerce handles
                'wpNonce' => wp_create_nonce('wp_rest'),
                'wcNonce' => wp_create_nonce('woocommerce_rest'),

                'productsOfWishlist' => [
                    'itemsPerPage' => 5,
                ],
                'price' => [
                    'decimals' => wc_get_price_decimals(),
                    'format' => html_entity_decode(get_woocommerce_price_format()),
                ],
                'currency' => [
                    'code' => get_woocommerce_currency(),
                    'symbol' => html_entity_decode(get_woocommerce_currency_symbol(get_woocommerce_currency())),
                ],
                'settings' => $this->context->getSettings()->getOptions(),
                'urls' => [
                    'cart' => wc_get_cart_url(),
                ],
                'columns' => [
                    'icon' => $themeOptions->exactWishlist->showIconColumn,
                    'price' => $themeOptions->exactWishlist->showPriceColumn,
                    'stock' => $themeOptions->exactWishlist->showStockColumn,
                    'actions' => $themeOptions->exactWishlist->showActionsColumn,
                ],
                'share' => $share,
            ]
        );
        wp_register_style(
            'algol_wishlist_client_app_styles',
            ALGOL_WISHLIST_PLUGIN_URL . '/src/assets/client-app/client-bundle.css',
            [],
            ALGOL_WISHLIST_VERSION
        );
    }

    public function getContent($args)
    {
        global $wpdb;

        wp_enqueue_script('algol_wishlist_client_app');
        wp_enqueue_style('algol_wishlist_client_app_styles');

        $wishlist = $this->getWishlistEntity();
        $user = $this->context->getCurrentUser();

        if ($wishlist) {
            if ( $user->isGuest() ) {
                $mutable = $user->getSessionKey() === $wishlist->sessionKey;
            } else {
                $mutable = $user->getUserId() === $wishlist->ownerId;
            }

            return "<div id='awl-client-app' data-wishlist-id='{$wishlist->id}' data-mutable='{$mutable}'></div>";
        } else {
            return "";
        }
    }

    protected function getWishlistEntity(): ?WishlistEntity
    {
        global $wpdb;

        $wishlistToken = ((new WordpressCurrentRequest())->getWishlistTokenIfPresent());

        /**
         * $wishlist Token equals 'null' only when you open preview at the admin side.
         * For some reason WP executes shortcode regardless the fact you cannot see the content.
         */
        if ($wishlistToken === null) {
            return null;
        } else {
            if ($wishlistToken === "") {
                $wishlist = $this->context->getCurrentUser() ? $this->context->getCurrentUser()->getDefaultWishlist() : null;
            } else {
                try {
                    $wishlist = (new WishlistsRepositoryWordpress($wpdb))->getByToken($wishlistToken);
                } catch (\Exception $e) {
                    $this->context->getLogger()->critical("Cannot show [$this->name] shortcode! Reason: {$e->getMessage()}");
                    return null;
                }
            }
        }

        return $wishlist;
    }
}
