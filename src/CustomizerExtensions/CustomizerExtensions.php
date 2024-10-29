<?php

namespace AlgolWishlist\CustomizerExtensions;

use AlgolWishlist\Context;
use AlgolWishlist\Repositories\ThemeOptions\ThemeModificationsRepository;
use WP_Customize_Manager;

defined('ABSPATH') or exit;

class CustomizerExtensions
{
    const PANEL_KEY = 'awl_wishlist';

    protected $options = array();

    /**
     * @var ThemeModificationsRepository
     */
    protected $themeModificationsRepository;

    /**
     * @var Context
     */
    protected $context;

    public function __construct($deprecated = null)
    {
        $this->context = awlContext();
        $this->init();
        $this->themeModificationsRepository = new ThemeModificationsRepository();
    }

    public function withContext(Context $context)
    {
        $this->context = $context;
    }

    public function withThemeModificationsRepository(ThemeModificationsRepository $themeModificationsRepository)
    {
        $this->themeModificationsRepository = $themeModificationsRepository;
    }

    public function register()
    {
        add_action('customize_register', array($this, 'add_sections'));
        add_action('customize_controls_enqueue_scripts', array($this, 'customizerControlsScripts'), 999);
//        add_action('customize_preview_init', array($this, 'customizePreviewInit'));
//
        // style customize
        add_action('wp_head', function () {
            $this->customizeCss();
        });
    }

    public function customizerControlsScripts()
    {
        wp_enqueue_style('awl-customizer-control-css', ALGOL_WISHLIST_PLUGIN_URL . '/src/assets/css/customize-controls.css',
            array(), WC_ADP_VERSION);
        wp_enqueue_script(
            'awl-customizer-control-js',
            ALGOL_WISHLIST_PLUGIN_URL . '/src/assets/js/customize-controls.js',
            ['jquery'],
            WC_ADP_VERSION
        );
        wp_localize_script(
            'awl-customizer-control-js',
            'algolWishlistCustomizerData',
            [
                'labels' => array(
                    'custom' => __('Custom', 'wc-wishlist'),
                    'selectIcon' => __('Select icon', 'wc-wishlist'),
                )
            ]
        );
    }

    protected function init()
    {
        $this->options[self::PANEL_KEY] = array(
            'title' => __('Wishlist', 'wc-wishlist'),
            'priority' => 200,
            'options' => $this->getWishlistOptions(),
        );
    }

    protected function getWishlistOptions() {
        $options = array_merge($this->getShopLoopButtonOptions(), $this->getProductPageButtonOptions(),
            $this->getExactWishlistOptions());

        return $options;
    }

    protected function getShopLoopButtonOptions()
    {
        $options = array(
            ShopLoopButtonProperties::KEY => array(
                'title' => __('Wishlist button shop loop', 'wc-wishlist'),
                'priority' => 10,
                'options' => array(
                    'shop_loop_button_action' => array(
                        'label' => __('Shop loop button position', 'wc-wishlist'),
                        'default' => 'woocommerce_after_shop_loop_item:15',
                        'control_type' => 'select',
                        'choices' => apply_filters('awl_shop_loop_button_places', array(
                            'woocommerce_before_shop_loop_item:5' => __('On top of the image', 'wc-wishlist'),
                            'woocommerce_after_shop_loop_item:7' => __('Before "Add to cart" button', 'wc-wishlist'),
                            'woocommerce_after_shop_loop_item:15' => __('After "Add to cart" button', 'wc-wishlist'),
                        )),
                        'priority' => 10,

                        'apply_type' => 'filter',
                        'hook' => "awl_shop_loop_button_action",
                        'layout' => 'any',
                    ),
                    'shop_loop_button_type' => array(
                        'label' => __('Shop loop button type', 'wc-wishlist'),
                        'default' => 'type_link',
                        'control_type' => 'select',
                        'choices' => apply_filters('awl_shop_loop_button_types', array(
                            'type_button' => __('Button',
                                'wc-wishlist'),
                            'type_link' => __('Link',
                                'wc-wishlist'),
                        )),
                        'priority' => 10,

                        'apply_type' => 'filter',
                        'hook' => "awl_shop_loop_button_type",
                        'layout' => 'any',
                    ),
                    'shop_loop_button_add_text' => array(
                        'label' => __('"Add to wishlist" button text', 'wc-wishlist'),
                        'default' => __("Add to wishlist", 'wc-wishlist'),
                        'priority' => 10,

                        'apply_type' => 'filter',
                    ),
                    'shop_loop_button_view_text' => array(
                        'label' => __('"View wishlist" button text', 'wc-wishlist'),
                        'default' => __("View wishlist", 'wc-wishlist'),
                        'priority' => 10,

                        'apply_type' => 'filter',
                    ),
                    'shop_loop_button_remove_text' => array(
                        'label' => __('"Remove from wishlist" button text', 'wc-wishlist'),
                        'default' => __("Remove from wishlist", 'wc-wishlist'),
                        'priority' => 10,

                        'apply_type' => 'filter',
                    ),
                    "shop_loop_button_text_color" => array(
                        'label' => __('Text color', 'wc-wishlist'),
                        'default' => '#333333',
                        'sanitize_callback' => 'sanitize_hex_color',
                        'control_class' => '\WP_Customize_Color_Control',
                        'priority' => 10,

                        'apply_type' => 'css',
                        'selector' => '.algol-add-to-wishlist-btn, .algol-view-wishlist-btn,
                            .algol-remove-from-wishlist-btn',
                        'css_option_name' => 'color',
                        'layout' => 'any',
                    ),
                    "shop_loop_button_hover_color" => array(
                        'label' => __('Hover color', 'wc-wishlist'),
                        'default' => '#333333',
                        'sanitize_callback' => 'sanitize_hex_color',
                        'control_class' => '\WP_Customize_Color_Control',
                        'priority' => 10,

                        'apply_type' => 'css',
                        'selector' => '.algol-add-to-wishlist-btn:hover, .algol-view-wishlist-btn:hover,
                            .algol-remove-from-wishlist-btn:hover, .algol-add-to-wishlist-btn:hover::before,
                            .algol-view-wishlist-btn:hover::before, .algol-remove-from-wishlist-btn:hover::before',
                        'css_option_name' => 'color',
                        'layout' => 'any',
                    ),
                    "shop_loop_button_add_icon" => array(
                        'label' => __('"Add to wishlist" button icon', 'wc-wishlist'),
                        'control_class' => 'AlgolWishlist\CustomizerExtensions\Controls\IconPicker',
                        'priority' => 10,
                        'apply_type' => 'filter',
                        'default' => 'heart',
                        'additional_settings' => array(
                            'no_icon_allowed' => true,
                        )
                    ),
                    "shop_loop_button_add_icon_custom" => array(
                        'label' => __('"Add to wishlist" button custom icon', 'wc-wishlist'),
                        'control_class' => '\WP_Customize_Cropped_Image_Control',
                        'priority' => 10,
                        'apply_type' => 'filter',
                        'default' => null,
                        'additional_settings' => array(
                            'height' => 30,
                            'width' => 30,
                            'flex_height' => false,
                            'flex_width' => false,
                        ),
                    ),
                    "shop_loop_button_add_icon_color" => array(
                        'label' => __('"Add to wishlist" button icon color', 'wc-wishlist'),
                        'default' => '#333333',
                        'sanitize_callback' => 'sanitize_hex_color',
                        'control_class' => '\WP_Customize_Color_Control',
                        'priority' => 10,

                        'apply_type' => 'css',
                        'selector' => '.algol-add-to-wishlist-btn::before',
                        'css_option_name' => 'color',
                        'layout' => 'any',
                    ),
                    "shop_loop_button_view_icon" => array(
                        'label' => __('"View wishlist" button icon', 'wc-wishlist'),
                        'control_class' => 'AlgolWishlist\CustomizerExtensions\Controls\IconPicker',
                        'priority' => 10,
                        'apply_type' => 'filter',
                        'default' => 'heart',
                        'additional_settings' => array(
                            'no_icon_allowed' => true,
                        )
                    ),
                    "shop_loop_button_view_icon_custom" => array(
                        'label' => __('"View wishlist" button custom icon', 'wc-wishlist'),
                        'control_class' => '\WP_Customize_Cropped_Image_Control',
                        'priority' => 10,
                        'apply_type' => 'filter',
                        'default' => null,
                        'additional_settings' => array(
                            'height' => 30,
                            'width' => 30,
                            'flex_height' => false,
                            'flex_width' => false,
                        ),
                    ),
                    "shop_loop_button_view_icon_color" => array(
                        'label' => __('"View wishlist" button icon color', 'wc-wishlist'),
                        'default' => '#333333',
                        'sanitize_callback' => 'sanitize_hex_color',
                        'control_class' => '\WP_Customize_Color_Control',
                        'priority' => 10,

                        'apply_type' => 'css',
                        'selector' => '.algol-view-wishlist-btn::before',
                        'css_option_name' => 'color',
                        'layout' => 'any',
                    ),
                    "shop_loop_button_remove_icon" => array(
                        'label' => __('"Remove from wishlist" button icon', 'wc-wishlist'),
                        'control_class' => 'AlgolWishlist\CustomizerExtensions\Controls\IconPicker',
                        'priority' => 10,
                        'apply_type' => 'filter',
                        'default' => 'heart',
                        'additional_settings' => array(
                            'no_icon_allowed' => true,
                        )
                    ),
                    "shop_loop_button_remove_icon_custom" => array(
                        'label' => __('"Remove from wishlist" button custom icon', 'wc-wishlist'),
                        'control_class' => '\WP_Customize_Cropped_Image_Control',
                        'priority' => 10,
                        'apply_type' => 'filter',
                        'default' => null,
                        'additional_settings' => array(
                            'height' => 30,
                            'width' => 30,
                            'flex_height' => false,
                            'flex_width' => false,
                        ),
                    ),
                    "shop_loop_button_remove_icon_color" => array(
                        'label' => __('"Remove from wishlist" button icon color', 'wc-wishlist'),
                        'default' => '#333333',
                        'sanitize_callback' => 'sanitize_hex_color',
                        'control_class' => '\WP_Customize_Color_Control',
                        'priority' => 10,

                        'apply_type' => 'css',
                        'selector' => '.algol-remove-from-wishlist-btn::before',
                        'css_option_name' => 'color',
                        'layout' => 'any',
                    ),
                ),
            ),
        );

        return $options;
    }

    protected function getProductPageButtonOptions()
    {
        $options = array(
            ProductPageButtonProperties::KEY => array(
                'title' => __('Wishlist button product page', 'wc-wishlist'),
                'priority' => 10,
                'options' => array(
                    'product_page_button_action' => array(
                        'label' => __('Product page button position', 'wc-wishlist'),
                        'default' => 'woocommerce_after_add_to_cart_form',
                        'control_type' => 'select',
                        'choices' => apply_filters('awl_product_page_button_places', array(
                            'woocommerce_before_single_product_summary' => __('Above product summary',
                                'wc-wishlist'),
                            'woocommerce_after_single_product_summary' => __('Below product summary',
                                'wc-wishlist'),
                            'woocommerce_before_single_product' => __('Above product',
                                'wc-wishlist'),
                            'woocommerce_after_single_product' => __('Below product',
                                'wc-wishlist'),
                            'woocommerce_before_add_to_cart_form' => __('Above add to cart',
                                'wc-wishlist'),
                            'woocommerce_after_add_to_cart_form' => __('Below add to cart',
                                'wc-wishlist'),
                            'woocommerce_product_meta_start' => __('Above product meta',
                                'wc-wishlist'),
                            'woocommerce_product_meta_end' => __('Below product meta',
                                'wc-wishlist'),
                        )),
                        'priority' => 10,

                        'apply_type' => 'filter',
                        'hook' => "awl_product_page_button_action",
                        'layout' => 'any',
                    ),
                    'product_page_button_type' => array(
                        'label' => __('Product page button type', 'wc-wishlist'),
                        'default' => 'type_link',
                        'control_type' => 'select',
                        'choices' => apply_filters('awl_product_page_button_types', array(
                            'type_button' => __('Button',
                                'wc-wishlist'),
                            'type_link' => __('Link',
                                'wc-wishlist'),
                        )),
                        'priority' => 10,

                        'apply_type' => 'filter',
                        'hook' => "awl_product_page_button_type",
                        'layout' => 'any',
                    ),
                    'product_page_button_add_text' => array(
                        'label' => __('"Add to wishlist" button text', 'wc-wishlist'),
                        'default' => __("Add to wishlist", 'wc-wishlist'),
                        'priority' => 10,

                        'apply_type' => 'filter',
                    ),
                    'product_page_button_view_text' => array(
                        'label' => __('"View wishlist" button text', 'wc-wishlist'),
                        'default' => __("View wishlist", 'wc-wishlist'),
                        'priority' => 10,

                        'apply_type' => 'filter',
                    ),
                    'product_page_button_remove_text' => array(
                        'label' => __('"Remove from wishlist" button text', 'wc-wishlist'),
                        'default' => __("Remove from wishlist", 'wc-wishlist'),
                        'priority' => 10,

                        'apply_type' => 'filter',
                    ),
                    "product_page_button_text_color" => array(
                        'label' => __('Text color', 'wc-wishlist'),
                        'default' => '#333333',
                        'sanitize_callback' => 'sanitize_hex_color',
                        'control_class' => '\WP_Customize_Color_Control',
                        'priority' => 10,

                        'apply_type' => 'css',
                        'selector' => '.algol-add-to-wishlist-btn, .algol-view-wishlist-btn,
                            .algol-remove-from-wishlist-btn',
                        'css_option_name' => 'color',
                        'layout' => 'any',
                    ),
                    "product_page_button_hover_color" => array(
                        'label' => __('Hover color', 'wc-wishlist'),
                        'default' => '#333333',
                        'sanitize_callback' => 'sanitize_hex_color',
                        'control_class' => '\WP_Customize_Color_Control',
                        'priority' => 10,

                        'apply_type' => 'css',
                        'selector' => '.algol-add-to-wishlist-btn:hover, .algol-view-wishlist-btn:hover,
                            .algol-remove-from-wishlist-btn:hover, .algol-add-to-wishlist-btn:hover::before,
                            .algol-view-wishlist-btn:hover::before, .algol-remove-from-wishlist-btn:hover::before',
                        'css_option_name' => 'color',
                        'layout' => 'any',
                    ),
                    "product_page_button_add_icon" => array(
                        'label' => __('"Add to wishlist" button icon', 'wc-wishlist'),
                        'control_class' => 'AlgolWishlist\CustomizerExtensions\Controls\IconPicker',
                        'priority' => 10,
                        'apply_type' => 'filter',
                        'default' => 'heart',
                        'additional_settings' => array(
                            'no_icon_allowed' => true,
                        )
                    ),
                    "product_page_button_add_icon_custom" => array(
                        'label' => __('"Add to wishlist" button custom icon', 'wc-wishlist'),
                        'control_class' => '\WP_Customize_Cropped_Image_Control',
                        'priority' => 10,
                        'apply_type' => 'filter',
                        'default' => null,
                        'additional_settings' => array(
                            'height' => 30,
                            'width' => 30,
                            'flex_height' => false,
                            'flex_width' => false,
                        ),
                    ),
                    "product_page_button_add_icon_color" => array(
                        'label' => __('"Add to wishlist" button icon color', 'wc-wishlist'),
                        'default' => '#333333',
                        'sanitize_callback' => 'sanitize_hex_color',
                        'control_class' => '\WP_Customize_Color_Control',
                        'priority' => 10,

                        'apply_type' => 'css',
                        'selector' => '.algol-add-to-wishlist-btn::before',
                        'css_option_name' => 'color',
                        'layout' => 'any',
                    ),
                    "product_page_button_view_icon" => array(
                        'label' => __('"View wishlist" button icon', 'wc-wishlist'),
                        'control_class' => 'AlgolWishlist\CustomizerExtensions\Controls\IconPicker',
                        'priority' => 10,
                        'apply_type' => 'filter',
                        'default' => 'heart',
                        'additional_settings' => array(
                            'no_icon_allowed' => true,
                        )
                    ),
                    "product_page_button_view_icon_custom" => array(
                        'label' => __('"View wishlist" button custom icon', 'wc-wishlist'),
                        'control_class' => '\WP_Customize_Cropped_Image_Control',
                        'priority' => 10,
                        'apply_type' => 'filter',
                        'default' => null,
                        'additional_settings' => array(
                            'height' => 30,
                            'width' => 30,
                            'flex_height' => false,
                            'flex_width' => false,
                        ),
                    ),
                    "product_page_button_view_icon_color" => array(
                        'label' => __('"View wishlist" button icon color', 'wc-wishlist'),
                        'default' => '#333333',
                        'sanitize_callback' => 'sanitize_hex_color',
                        'control_class' => '\WP_Customize_Color_Control',
                        'priority' => 10,

                        'apply_type' => 'css',
                        'selector' => '.algol-view-wishlist-btn::before',
                        'css_option_name' => 'color',
                        'layout' => 'any',
                    ),
                    "product_page_button_remove_icon" => array(
                        'label' => __('"Remove from wishlist" button icon', 'wc-wishlist'),
                        'control_class' => 'AlgolWishlist\CustomizerExtensions\Controls\IconPicker',
                        'priority' => 10,
                        'apply_type' => 'filter',
                        'default' => 'heart',
                        'additional_settings' => array(
                            'no_icon_allowed' => true,
                        )
                    ),
                    "product_page_button_remove_icon_custom" => array(
                        'label' => __('"Remove from wishlist" button custom icon', 'wc-wishlist'),
                        'control_class' => '\WP_Customize_Cropped_Image_Control',
                        'priority' => 10,
                        'apply_type' => 'filter',
                        'default' => null,
                        'additional_settings' => array(
                            'height' => 30,
                            'width' => 30,
                            'flex_height' => false,
                            'flex_width' => false,
                        ),
                    ),
                    "product_page_button_remove_icon_color" => array(
                        'label' => __('"Remove from wishlist" button icon color', 'wc-wishlist'),
                        'default' => '#333333',
                        'sanitize_callback' => 'sanitize_hex_color',
                        'control_class' => '\WP_Customize_Color_Control',
                        'priority' => 10,

                        'apply_type' => 'css',
                        'selector' => '.algol-remove-from-wishlist-btn::before',
                        'css_option_name' => 'color',
                        'layout' => 'any',
                    ),
                ),
            ),
        );

        return $options;
    }

    protected function getExactWishlistOptions() {
        $options = array(
            ExactWishlistProperties::KEY => array(
                'title' => __('Wishlist page', 'wc-wishlist'),
                'priority' => 10,
                'options' => array(
                    'wishlist_show_icon_column'     => array(
                        'label'             => __('Show "Icon" column', 'wc-wishlist'),
                        'default'           => true,
                        'priority'          => 5,
                        'control_type'      => 'checkbox',
                        'sanitize_callback' => 'wc_string_to_bool',

                        'apply_type' => 'filter',
                        'hook'       => "awl_exact_wishlist_show_icon_column",
                    ),
                    'wishlist_show_price_column'     => array(
                        'label'             => __('Show "Unit Price" column', 'wc-wishlist'),
                        'default'           => true,
                        'priority'          => 10,
                        'control_type'      => 'checkbox',
                        'sanitize_callback' => 'wc_string_to_bool',

                        'apply_type' => 'filter',
                        'hook'       => "awl_exact_wishlist_show_price_column",
                    ),
                    'wishlist_show_stock_column'     => array(
                        'label'             => __('Show "Stock status" column', 'wc-wishlist'),
                        'default'           => true,
                        'priority'          => 20,
                        'control_type'      => 'checkbox',
                        'sanitize_callback' => 'wc_string_to_bool',

                        'apply_type' => 'filter',
                        'hook'       => "awl_exact_wishlist_show_stock_column",
                    ),
                    'wishlist_show_actions_column'     => array(
                        'label'             => __('Show "Actions" column', 'wc-wishlist'),
                        'default'           => true,
                        'priority'          => 25,
                        'control_type'      => 'checkbox',
                        'sanitize_callback' => 'wc_string_to_bool',

                        'apply_type' => 'filter',
                        'hook'       => "awl_exact_wishlist_show_actions_column",
                    ),
                    'wishlist_share_block_title' => array(
                        'label' => __('Share block title', 'wc-wishlist'),
                        'default' => __("Share on", 'wc-wishlist'),
                        'priority' => 30,
                        'hook' => "awl_wishlist_share_block_title",
                        'apply_type' => 'filter',
                    ),
                    "wishlist_share_block_position" => array(
                        'label' => __('Share block position', 'wc-wishlist'),
                        'default' => 'after_bulk_actions',
                        'control_type' => 'select',
                        'priority' => 35,
                        'choices' => array(
                            'before_wishlist' => __('Above wishlist', 'wc-wishlist'),
                            'after_wishlist' => __('Below wishlist', 'wc-wishlist'),
                            'after_bulk_actions' => __('Below bulk actions', 'wc-wishlist'),
                        ),
                        'apply_type' => 'filter',
                        'hook' => 'awl_wishlist_share_block_position',
                    ),
                    "wishlist_share_block_horizontal_alignment" => array(
                        'label' => __('Share block horizontal alignment', 'wc-wishlist'),
                        'default' => 'start',
                        'control_type' => 'select',
                        'priority' => 40,
                        'choices' => array(
                            'start' => __('Left', 'wc-wishlist'),
                            'center' => __('Center', 'wc-wishlist'),
                            'end' => __('Right', 'wc-wishlist'),
                        ),
                        'apply_type' => 'css',
                        'selector' => '.MuiBox-root.awl-share-block',
                        'css_option_name' => 'justify-content',
                        'layout' => 'any',
                    ),
                    "wishlist_facebook_share_icon" => array(
                        'label' => __('Facebook share icon', 'wc-wishlist'),
                        'control_class' => 'AlgolWishlist\CustomizerExtensions\Controls\IconPicker',
                        'priority' => 45,
                        'apply_type' => 'filter',
                        'default' => 'facebook',
                    ),
                    "wishlist_facebook_share_icon_custom" => array(
                        'label' => __('Facebook share custom icon', 'wc-wishlist'),
                        'control_class' => '\WP_Customize_Cropped_Image_Control',
                        'priority' => 50,
                        'apply_type' => 'filter',
                        'default' => null,
                        'additional_settings' => array(
                            'height' => 30,
                            'width' => 30,
                            'flex_height' => false,
                            'flex_width' => false,
                        ),
                    ),
                    "wishlist_facebook_icon_color" => array(
                        'label' => __('Facebook icon color', 'wc-wishlist'),
                        'default' => '#4267B2',
                        'sanitize_callback' => 'sanitize_hex_color',
                        'control_class' => '\WP_Customize_Color_Control',
                        'priority' => 55,

                        'apply_type' => 'css',
                        'selector' => '.awl-facebook-link::before',
                        'css_option_name' => 'color',
                        'layout' => 'any',
                    ),
                    "wishlist_twitter_share_icon" => array(
                        'label' => __('Twitter share icon', 'wc-wishlist'),
                        'control_class' => 'AlgolWishlist\CustomizerExtensions\Controls\IconPicker',
                        'priority' => 60,
                        'apply_type' => 'filter',
                        'default' => 'twitter',
                    ),
                    "wishlist_twitter_share_icon_custom" => array(
                        'label' => __('Twitter share custom icon', 'wc-wishlist'),
                        'control_class' => '\WP_Customize_Cropped_Image_Control',
                        'priority' => 65,
                        'apply_type' => 'filter',
                        'default' => null,
                        'additional_settings' => array(
                            'height' => 30,
                            'width' => 30,
                            'flex_height' => false,
                            'flex_width' => false,
                        ),
                    ),
                    "wishlist_twitter_icon_color" => array(
                        'label' => __('Twitter icon color', 'wc-wishlist'),
                        'default' => '#00ACEE',
                        'sanitize_callback' => 'sanitize_hex_color',
                        'control_class' => '\WP_Customize_Color_Control',
                        'priority' => 70,

                        'apply_type' => 'css',
                        'selector' => '.awl-twitter-link::before',
                        'css_option_name' => 'color',
                        'layout' => 'any',
                    ),
                    "wishlist_pinterest_share_icon" => array(
                        'label' => __('Pinterest share icon', 'wc-wishlist'),
                        'control_class' => 'AlgolWishlist\CustomizerExtensions\Controls\IconPicker',
                        'priority' => 75,
                        'apply_type' => 'filter',
                        'default' => 'pinterest',
                    ),
                    "wishlist_pinterest_share_icon_custom" => array(
                        'label' => __('Pinterest share custom icon', 'wc-wishlist'),
                        'control_class' => '\WP_Customize_Cropped_Image_Control',
                        'priority' => 80,
                        'apply_type' => 'filter',
                        'default' => null,
                        'additional_settings' => array(
                            'height' => 30,
                            'width' => 30,
                            'flex_height' => false,
                            'flex_width' => false,
                        ),
                    ),
                    "wishlist_pinterest_icon_color" => array(
                        'label' => __('Pinterest icon color', 'wc-wishlist'),
                        'default' => '#E60023',
                        'sanitize_callback' => 'sanitize_hex_color',
                        'control_class' => '\WP_Customize_Color_Control',
                        'priority' => 85,

                        'apply_type' => 'css',
                        'selector' => '.awl-pinterest-link::before',
                        'css_option_name' => 'color',
                        'layout' => 'any',
                    ),
                    "wishlist_email_share_icon" => array(
                        'label' => __('Email share icon', 'wc-wishlist'),
                        'control_class' => 'AlgolWishlist\CustomizerExtensions\Controls\IconPicker',
                        'priority' => 90,
                        'apply_type' => 'filter',
                        'default' => 'email',
                    ),
                    "wishlist_email_share_icon_custom" => array(
                        'label' => __('Email share custom icon', 'wc-wishlist'),
                        'control_class' => '\WP_Customize_Cropped_Image_Control',
                        'priority' => 95,
                        'apply_type' => 'filter',
                        'default' => null,
                        'additional_settings' => array(
                            'height' => 30,
                            'width' => 30,
                            'flex_height' => false,
                            'flex_width' => false,
                        ),
                    ),
                    "wishlist_email_icon_color" => array(
                        'label' => __('Email icon color', 'wc-wishlist'),
                        'default' => '#000000',
                        'sanitize_callback' => 'sanitize_hex_color',
                        'control_class' => '\WP_Customize_Color_Control',
                        'priority' => 100,

                        'apply_type' => 'css',
                        'selector' => '.awl-email-link::before',
                        'css_option_name' => 'color',
                        'layout' => 'any',
                    ),
                    "wishlist_whatsapp_share_icon" => array(
                        'label' => __('WhatsApp share icon', 'wc-wishlist'),
                        'control_class' => 'AlgolWishlist\CustomizerExtensions\Controls\IconPicker',
                        'priority' => 105,
                        'apply_type' => 'filter',
                        'default' => 'whatsapp',
                    ),
                    "wishlist_whatsapp_share_icon_custom" => array(
                        'label' => __('WhatsApp share custom icon', 'wc-wishlist'),
                        'control_class' => '\WP_Customize_Cropped_Image_Control',
                        'priority' => 110,
                        'apply_type' => 'filter',
                        'default' => null,
                        'additional_settings' => array(
                            'height' => 30,
                            'width' => 30,
                            'flex_height' => false,
                            'flex_width' => false,
                        ),
                    ),
                    "wishlist_whatsapp_icon_color" => array(
                        'label' => __('WhatsApp icon color', 'wc-wishlist'),
                        'default' => '#075E54',
                        'sanitize_callback' => 'sanitize_hex_color',
                        'control_class' => '\WP_Customize_Color_Control',
                        'priority' => 115,

                        'apply_type' => 'css',
                        'selector' => '.awl-whatsapp-link::before',
                        'css_option_name' => 'color',
                        'layout' => 'any',
                    ),
                ),
            ),
        );

        return $options;
    }

    /**
     * @param WP_Customize_Manager $wpCustomize Theme Customizer object.
     */
    public function add_sections(WP_Customize_Manager $wpCustomize)
    {
        foreach ($this->options as $panel_id => $panel_data) {
            $panel_title = !empty($panel_data['title']) ? $panel_data['title'] : null;
            $panel_options = !empty($panel_data['options']) ? $panel_data['options'] : null;

            if (!$panel_title || !$panel_options) {
                continue;
            }

            $wpCustomize->add_panel($panel_id, array(
                'title' => $panel_title,
                'priority' => !empty($panel_data['priority']) ? $panel_data['priority'] : 200,
            ));

            foreach ($panel_options as $section_id => $section_settings) {
                $this->add_section($wpCustomize, $section_id, $section_settings, $panel_id);
            }
        }

    }

    /**
     * @param WP_Customize_Manager $wp_customize Theme Customizer object.
     * @param string $section_id Parent menu id
     * @param array $sectionSettings (See above)
     * @param string $panelId
     */
    protected function add_section(
        WP_Customize_Manager $wp_customize,
        string               $section_id,
        array                $sectionSettings,
        string               $panelId
    )
    {
        if (!empty($sectionSettings['options'])) {
            $wp_customize->add_section($section_id, array(
                'title' => $sectionSettings['title'],
                'priority' => $sectionSettings['priority'] ?? 20,
                'panel' => $panelId,
            ));

            uasort($sectionSettings['options'], function ($item1, $item2) {
                if ($item1['priority'] == $item2['priority']) {
                    return 0;
                }

                return $item1['priority'] < $item2['priority'] ? -1 : 1;
            });

            foreach ($sectionSettings['options'] as $option_id => $data) {
                $setting = sprintf(
                    '%s[%s][%s][%s]',
                    $this->themeModificationsRepository::OPTION_NAME,
                    $panelId,
                    $section_id,
                    $option_id
                );
                $this->add_option($wp_customize, $setting, $section_id, $data);
            }
        }
    }

    /**
     * @param WP_Customize_Manager $wpCustomize Theme Customizer object.
     * @param string $setting Option id
     * @param string $sectionId Parent menu id
     * @param array $data Option data
     */
    protected function add_option(WP_Customize_Manager $wpCustomize, $setting, $sectionId, $data)
    {
        $priority = !empty($data['priority']) ? $data['priority'] : 20;
        $description = !empty($data['description']) ? $data['description'] : "";

        $transport = 'refresh';
        if ($data['apply_type'] == 'css') {
            $transport = 'postMessage';
        }

        $wpCustomize->add_setting($setting, array(
            'default' => $data['default'],
            'capability' => 'edit_theme_options',
            'transport' => $transport,
            'priority' => $priority,
        ));


        if (!empty($data['control_class']) && class_exists($data['control_class'])) {
            $class = $data['control_class'];
            $control = new $class($wpCustomize, $setting, array_merge(array(
                'label' => $data['label'],
                'description' => $description,
                'section' => $sectionId,
                'settings' => $setting,
                'priority' => $priority,
            ), isset($data['additional_settings']) ? $data['additional_settings'] : array()));
            $wpCustomize->add_control($control);
        } else {
            $wpCustomize->add_control($setting, array(
                'label' => $data['label'],
                'description' => $description,
                'section' => $sectionId,
                'settings' => $setting,
                'type' => $data['control_type'] ?? 'text',
                'choices' => $data['choices'] ?? array(),
            ));
        }
    }

    /**
     * @return ThemeProperties|null
     */
    public function getThemeOptions()
    {
        if (!did_action('wp_loaded')) {
            _doing_it_wrong(__FUNCTION__,
                sprintf(__('%1$s should not be called before the %2$s action.', 'woocommerce'),
                    __NAMESPACE__ . '/Customizer::getThemeOptions', 'wp_loaded'), '2.2.2');

            return null;
        }

        $result = array();
        $attrOptions = $this->themeModificationsRepository->getModifications();

        foreach ($this->options as $panelId => $panelData) {
            if (empty($panelData['options'])) {
                continue;
            }

//            $key = $panelData['key'];
            $key = $panelId;

            $sectionOptions = array();
            foreach ($panelData['options'] as $sectionId => $sectionSettings) {
                if (!isset($sectionSettings['options'])) {
                    continue;
                }

//                $sectionKey = str_replace($panelId . '-', "", $sectionId);
                $sectionKey = $sectionId;

                $options = array();
                foreach ($sectionSettings['options'] as $optionId => $optionData) {
                    if (empty($optionData['apply_type'])) {
                        continue;
                    }

                    // font options
                    $optionKey = str_replace($panelId . '-', "", $optionId);

                    $default = $optionData['default'];
                    if (!isset($attrOptions[$panelId][$sectionId][$optionId])) {
                        $attrOption = $default;
                    } else {
                        $attrOption = $attrOptions[$panelId][$sectionId][$optionId];
                    }

                    /**
                     * Do not apply saved value which not in choices
                     * e.g. delete add_action
                     */
                    $choices = $optionData['choices'] ?? array();
                    if ($choices && empty($choices[$attrOption])) {
                        $attrOption = $default;
                    }

                    $options[$optionKey] = $attrOption;
                }

                $sectionOptions[$sectionKey] = $options;
            }

            $result[$key] = $sectionOptions;
        }

        return $this->convertToThemeProperties($result);
    }

    /**
     * @param array $props
     *
     * @return ThemeProperties
     */
    protected function convertToThemeProperties(array $props)
    {
        $themeProperties = new ThemeProperties();

        $obj = $themeProperties->shopLoopButton;
        $data = $props[self::PANEL_KEY][$obj::KEY];
        $obj->buttonPositionAction = $data['shop_loop_button_action'];
        $obj->controlType = $data['shop_loop_button_type'];
        $obj->addToWishlistText = $data['shop_loop_button_add_text'];
        $obj->viewWishlistText = $data['shop_loop_button_view_text'];
        $obj->removeFromWishlistText = $data['shop_loop_button_remove_text'];
        $obj->textColor = $data['shop_loop_button_text_color'];
        $obj->hoverColor = $data['shop_loop_button_hover_color'];
        $obj->addToWishlistIcon = $data['shop_loop_button_add_icon'];
        $obj->addToWishlistIconCustom = $data['shop_loop_button_add_icon_custom'];
        $obj->viewWishlistIcon = $data['shop_loop_button_view_icon'];
        $obj->viewWishlistIconCustom = $data['shop_loop_button_view_icon_custom'];
        $obj->removeFromWishlistIcon = $data['shop_loop_button_remove_icon'];
        $obj->removeFromWishlistIconCustom = $data['shop_loop_button_remove_icon_custom'];

        $obj = $themeProperties->productPageButton;
        $data = $props[self::PANEL_KEY][$obj::KEY];
        $obj->buttonPositionAction = $data['product_page_button_action'];
        $obj->controlType = $data['product_page_button_type'];
        $obj->addToWishlistText = $data['product_page_button_add_text'];
        $obj->viewWishlistText = $data['product_page_button_view_text'];
        $obj->removeFromWishlistText = $data['product_page_button_remove_text'];
        $obj->textColor = $data['product_page_button_text_color'];
        $obj->hoverColor = $data['product_page_button_hover_color'];
        $obj->addToWishlistIcon = $data['product_page_button_add_icon'];
        $obj->addToWishlistIconCustom = $data['product_page_button_add_icon_custom'];
        $obj->viewWishlistIcon = $data['product_page_button_view_icon'];
        $obj->viewWishlistIconCustom = $data['product_page_button_view_icon_custom'];
        $obj->removeFromWishlistIcon = $data['product_page_button_remove_icon'];
        $obj->removeFromWishlistIconCustom = $data['product_page_button_remove_icon_custom'];

        $obj = $themeProperties->exactWishlist;
        $data = $props[self::PANEL_KEY][$obj::KEY];
        $obj->showIconColumn = $data['wishlist_show_icon_column'];
        $obj->showPriceColumn = $data['wishlist_show_price_column'];
        $obj->showStockColumn = $data['wishlist_show_stock_column'];
        $obj->showActionsColumn = $data['wishlist_show_actions_column'];
        $obj->shareBlockTitle = $data['wishlist_share_block_title'];
        $obj->shareBlockPosition = $data['wishlist_share_block_position'];
        $obj->facebookShareIcon = $data['wishlist_facebook_share_icon'];
        $obj->facebookShareIconCustom = $data['wishlist_facebook_share_icon_custom'];
        $obj->twitterShareIcon = $data['wishlist_twitter_share_icon'];
        $obj->twitterShareIconCustom = $data['wishlist_twitter_share_icon_custom'];
        $obj->pinterestShareIcon = $data['wishlist_pinterest_share_icon'];
        $obj->pinterestShareIconCustom = $data['wishlist_pinterest_share_icon_custom'];
        $obj->emailShareIcon = $data['wishlist_email_share_icon'];
        $obj->emailShareIconCustom = $data['wishlist_email_share_icon_custom'];
        $obj->whatsappShareIcon = $data['wishlist_whatsapp_share_icon'];
        $obj->whatsappShareIconCustom = $data['wishlist_whatsapp_share_icon_custom'];

        return $themeProperties;
    }

    public function customizeCss()
    {
        $css = array();
        $attrOptions = $this->themeModificationsRepository->getModifications();
        $attrOptions = $attrOptions[self::PANEL_KEY];
        $important   = is_customize_preview() ? '! important' : "";
        $isProduct   = is_product();

        global $post;
        $isWishlist = $post && has_shortcode($post->post_content, 'awl_products_of_wishlist');

        if ($isWishlist) {
            $sectionId = ExactWishlistProperties::KEY;
        } else {
            $sectionId = $isProduct ? ProductPageButtonProperties::KEY : ShopLoopButtonProperties::KEY;
        }

        $wishlistOptions = $this->options[self::PANEL_KEY]['options'];

        if (empty($sectionId) || empty($wishlistOptions[$sectionId])) {
            return;
        }
        $sectionData = $wishlistOptions[$sectionId];

        if (empty($sectionData['options']) && ! is_array($sectionData['options'])) {
            return;
        }

        foreach ($sectionData['options'] as $optionId => $optionData) {
            if (empty($optionData['apply_type'])) {
                continue;
            }
            if ('css' == $optionData['apply_type'] && $optionData['selector']) {
                $default = $optionData['default'];
                if (!isset($attrOptions[$sectionId][$optionId])) {
                    $optionValue = $default;
                } else {
                    $optionValue = $attrOptions[$sectionId][$optionId];
                }
                if (!empty($optionData['css_option_value'])) {
                    if ($optionValue) {
                        $css[] = sprintf("%s { %s: %s ! important}", $optionData['selector'],
                            $optionData['css_option_name'], $optionData['css_option_value']);
                    }
                } else {
                    if ($optionValue) {
                        $css[] = sprintf("%s { %s: %s %s}", $optionData['selector'],
                            $optionData['css_option_name'], $optionValue, $important);
                    }
                }
            }
        }
        ?>
        <style type="text/css">
            <?php echo join(' ', $css); ?>
        </style>
        <?php

    }
}
