<?php

namespace AlgolWishlist;

use AlgolWishlist\AdminExtensions\AdminPage;

class QueryContext
{
    const CUSTOMIZER = 'customizer';
    const ADMIN = 'admin';
    const AJAX = 'ajax';
    const REST_API = 'rest_api';
    const WP_CRON = 'wp_cron';
    const PHPUNIT = 'phpunit';
    const AJAX_REF_ADMIN = 'ajax_ref_admin';
    const PROCESSING_UPDATE = 'processing_upgrade';

    /**
     * Props which can be accessed anyway
     *
     * @var array<int,callable>
     */
    protected $firstBornPropsCallbacks = array();

    const PRODUCT_LOOP = 'product_loop';
    const SHOP_LOOP = 'shop_loop';
    const WC_PRODUCT_PAGE = 'wc_product_page';
    const WC_CATEGORY_PAGE = 'wc_category_page';
    const WC_CART_PAGE = 'wc_cart_page';
    const WC_CHECKOUT_PAGE = 'wc_checkout_page';
    const WC_SHOP_PAGE = 'wc_shop_page';

    const ADP_PLUGIN_PAGE = 'adp_admin_plugin_page';

    /**
     * Props which can be accessed only after parsing the main WordPress query, so
     * in __construct we should wait until it happens (if needed ofc)
     *
     * @var array<int,callable>
     */
    private $queryPropsCallbacks = array();
    private $adminQueryPropsCallbacks = array();

    private $props = array();

    private $changedProps = array();

    public function __construct()
    {
        $this->initProps();
    }

    private function initProps()
    {
        $this->firstBornPropsCallbacks = array(
            self::ADMIN => 'is_admin',
            self::CUSTOMIZER => 'is_customize_preview',
            self::AJAX => 'wp_doing_ajax',
            self::REST_API => array($this, 'isRequestToRestApi'),
            self::WP_CRON => 'wp_doing_cron',
            self::PHPUNIT => array($this, 'isDoingPhpUnit'),
            self::AJAX_REF_ADMIN => array($this, 'isDoingAjaxRefAdmin'),
            self::PROCESSING_UPDATE => array($this, 'isProcessingUpdatePlugin'),
        );

        $this->queryPropsCallbacks = array(
            self::PRODUCT_LOOP => array($this, 'isWoocommerceProductLoop'),
            self::SHOP_LOOP => array($this, 'isWoocommerceShopLoop'),
            self::WC_PRODUCT_PAGE => 'is_product',
            self::WC_CATEGORY_PAGE => 'is_product_category',
            self::WC_CART_PAGE => 'is_cart',
            self::WC_CHECKOUT_PAGE => 'is_checkout',
            self::WC_SHOP_PAGE => 'is_shop',
        );

        $this->adminQueryPropsCallbacks = array(
            self::ADP_PLUGIN_PAGE => array($this, 'isAdpAdminPage'),
        );

        foreach ($this->firstBornPropsCallbacks as $prop => $callback) {
            $this->props[$prop] = $callback();
        }

        if (did_action('wp')) {
            $this->fetchQueryProps();
        } else {
            add_action('wp', array($this, 'fetchQueryProps'), 10, 0);
        }

        if (did_action('admin_init')) {
            $this->fetchAdminQueryProps();
        } else {
            add_action('admin_init', array($this, 'fetchAdminQueryProps'), 10, 0);
        }
    }

    public function fetchQueryProps()
    {
        foreach ($this->queryPropsCallbacks as $prop => $callback) {
            $this->props[$prop] = $callback();
        }
    }

    public function fetchAdminQueryProps()
    {
        foreach ($this->adminQueryPropsCallbacks as $prop => $callback) {
            $this->props[$prop] = $callback();
        }
    }

    protected static function isWoocommerceProductLoop()
    {
        global $wp_query;

        return ($wp_query->current_post + 1 < $wp_query->post_count) || 'products' !== woocommerce_get_loop_display_mode();
    }

    protected static function isWoocommerceShopLoop()
    {
        return !empty($GLOBALS['woocommerce_loop']['name']);
    }

    protected static function isAdpAdminPage()
    {
        global $plugin_page;

        return $plugin_page === AdminPage::SLUG;
    }

    protected static function isProcessingUpdatePlugin()
    {
        return wp_doing_ajax() && isset($_REQUEST['action']) && $_REQUEST['action'] === 'update-plugin';
    }

    protected static function isRequestToRestApi()
    {
        if (empty($_SERVER['REQUEST_URI'])) {
            return false;
        }

        $rest_prefix = trailingslashit(rest_get_url_prefix());
        $request_uri = esc_url_raw(wp_unslash($_SERVER['REQUEST_URI']));
        $wordpress = (false !== strpos($request_uri, $rest_prefix));

        return $wordpress;
    }

    protected static function isDoingPhpUnit()
    {
        return defined("PHPUNIT_COMPOSER_INSTALL");
    }

    /**
     * @return bool
     */
    protected static function isDoingAjaxRefAdmin()
    {
        if (!isset($_SERVER["HTTP_REFERER"])) {
            return false;
        }

        $referer = parse_url($_SERVER["HTTP_REFERER"]);
        $admin = parse_url(admin_url("admin.php"));

        return isset($referer['path'], $admin['path']) && ($referer['path'] === $admin['path']);
    }

    /**
     * @param $newProps array
     *
     * @return self
     */
    public function setProps($newProps)
    {
        foreach ($newProps as $key => $value) {
            $this->changedProps[$key] = $value;
        }

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getProp($key, $default = false)
    {
        $value = $default;

        if (isset($this->props[$key])) {
            $value = $this->props[$key];
        }

        if (isset($this->changedProps[$key])) {
            $value = $this->changedProps[$key];
        }

        return $value;
    }

    public function is($prop)
    {
        return $this->getProp($prop, null);
    }

    public function isCatalog()
    {
        return !$this->getProp(self::WC_PRODUCT_PAGE) || $this->getProp(self::SHOP_LOOP);
    }

    public function isProductPage()
    {
        return $this->getProp(self::WC_PRODUCT_PAGE);
    }

    public function isPluginAdminPage()
    {
        return $this->getProp(self::ADMIN) && isset($_GET['page']) && $_GET['page'] === AdminPage::SLUG;
    }
}
