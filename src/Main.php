<?php

namespace AlgolWishlist;

use AlgolWishlist\AdminExtensions\AdminPage;

use AlgolWishlist\Compatibility\SwaggerUICompatibility;
use AlgolWishlist\VersionFree\LoadStrategies\AdminAjax;
use AlgolWishlist\VersionFree\LoadStrategies\AdminCommon;
use AlgolWishlist\VersionFree\LoadStrategies\ClientCommon;
use AlgolWishlist\VersionFree\LoadStrategies\CustomizePreview;
use AlgolWishlist\VersionFree\LoadStrategies\LoadStrategy;
use AlgolWishlist\VersionFree\LoadStrategies\PhpUnit;
use AlgolWishlist\VersionFree\LoadStrategies\RestApi;
use AlgolWishlist\VersionFree\LoadStrategies\WpCron;
use AlgolWishlist\SessionHandler\SessionHandlerRegister;

class Main
{
    public function startUp()
    {
        add_action('init', array($this, 'initPlugin'), 5); // do not change the priority of the action
    }

    public function initPlugin()
    {
        if (!$this->checkRequirements()) {
            return;
        }

        if (!($loader = Factory::get("Loader"))) {
            return;
        }

        (new SwaggerUICompatibility())->replaceSchema();
        (new AdminPage())->registerPage();

        $wpRewrite = new WordpressRewrite();
        $loader->installRewriteRules($wpRewrite);
        $wpRewrite->register();

        (new SessionHandlerRegister())->register();

        load_plugin_textdomain('wc-wishlist', false, ALGOL_WISHLIST_PLUGIN_PATH . '/languages/');
        $loader->initModules();

        $strategy = $this->selectLoadStrategy();
        $strategy->start();
    }

    public function checkRequirements(): bool
    {
        $state = true;
        if (version_compare(phpversion(), ALGOL_WISHLIST_MIN_PHP_VERSION, '<')) {
            add_action('admin_notices', function () {
                echo '<div class="notice notice-error is-dismissible"><p>'
                    . sprintf(
                        esc_html__('Advanced Wishlist for WooCommerce requires PHP version %s or later.', 'wc-wishlist'),
                        ALGOL_WISHLIST_MIN_PHP_VERSION
                    )
                    . '</p></div>';
            });
            $state = false;
        } elseif (!class_exists('WooCommerce')) {
            add_action('admin_notices', function () {
                echo '<div class="notice notice-error is-dismissible"><p>'
                    . esc_html__('Advanced Wishlist for WooCommerce requires active WooCommerce!', 'wc-wishlist')
                    . '</p></div>';
            });
            $state = false;
        } elseif (version_compare(WC_VERSION, ALGOL_WISHLIST_MIN_WC_VERSION, '<')) {
            add_action('admin_notices', function () {
                echo '<div class="notice notice-error is-dismissible"><p>'
                    . sprintf(
                        esc_html__('Advanced Wishlist for WooCommerce requires WooCommerce version %s or later.',
                            'wc-wishlist'),
                        ALGOL_WISHLIST_MIN_WC_VERSION
                    )
                    . '</p></div>';
            });
            $state = false;
        }

        return $state;
    }

    /**
     * @return LoadStrategy
     */
    protected function selectLoadStrategy()
    {
        $queryContext = awlContext()->getQueryContext();

        if ($queryContext->is($queryContext::CUSTOMIZER)) {
            /** @var $strategy CustomizePreview */
            $strategy = Factory::get("LoadStrategies_CustomizePreview");
        } elseif ($queryContext->is($queryContext::WP_CRON)) {
            /** @var $strategy WpCron */
            $strategy = Factory::get("LoadStrategies_WpCron");
        } elseif ($queryContext->is($queryContext::REST_API)) {
            /** @var $strategy RestApi */
            $strategy = Factory::get("LoadStrategies_RestApi");
        } elseif ($queryContext->is($queryContext::AJAX)) {
            /** @var $strategy AdminAjax */
            $strategy = Factory::get("LoadStrategies_AdminAjax");
        } elseif ($queryContext->is($queryContext::ADMIN)) {
            /** @var $strategy AdminCommon */
            $strategy = Factory::get("LoadStrategies_AdminCommon");
        } elseif ($queryContext->is($queryContext::PHPUNIT)) {
            /** @var $strategy PhpUnit */
            $strategy = Factory::get("LoadStrategies_PhpUnit");
        } else {
            /** @var $strategy ClientCommon */
            $strategy = Factory::get("LoadStrategies_ClientCommon");
        }

        return $strategy;
    }
}
