<?php
defined('ABSPATH') or exit;

/**
 * @var $display boolean
 * @var $type string
 * @var $text string
 * @var $icon string
 * @var $iconCustom string
 * @var $isProduct boolean
 */
?>

<div style="display: block;">
    <button class="algol-remove-from-wishlist-btn <?php if ($type === 'type_link') echo ' algol-wishlist-button-link';
            if ($icon !== 'none' && !$iconCustom) esc_attr_e(" dashicons-before dashicons-$icon");
            echo $isProduct ? ' algol-product-page-btn' : ' algol-shop-loop-btn' ?>"
            style="<?php if (!$display) echo 'display: none;';
            if ($iconCustom) esc_attr_e("background: url('$iconCustom') left center no-repeat; padding-left: 30px;"); ?>">
        <?php if ($text) : ?>
            <div style="display: inline-block; margin-left: 5px;">
                <?php esc_html_e($text, 'wc-wishlist'); ?>
            </div>
        <?php endif; ?>
    </button>
</div>
