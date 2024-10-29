<?php

namespace AlgolWishlist\CustomizerExtensions\Controls;

use WP_Customize_Control;

defined('ABSPATH') or exit;

class IconPicker extends WP_Customize_Control
{
    public $type = 'awl_icon_picker';

    public $no_icon_allowed = false;

    public function render_content()
    {
        $value = $this->value();
        $iconsData = json_decode(file_get_contents(ALGOL_WISHLIST_PLUGIN_URL . '/src/assets/awl-icons.json'));
        $customLabel = __('Custom', 'wc-wishlist');
        $noneLabel = __('None', 'wc-wishlist');

        ?>
        <span class="customize-control-title"><?php esc_html_e($this->label, 'wc-wishlist'); ?></span>
        <fieldset class="awl-icon-picker">
            <a href="#select">
                <span class="arr dashicons dashicons-arrow-down"></span>
                <span class="def">
                    <?php if ($value === 'custom') {
                        esc_html_e($customLabel);
                    } elseif (!empty($value) && $value !== 'none') {
                        echo '<span class="', esc_attr("dashicons dashicons-$value"), '"></span>';
                    } else {
                        esc_html_e('Select icon', 'wc-wishlist');
                    } ?>
                </span>
            </a>

            <ul id="select">
                <?php foreach($iconsData as $key => $name): ?>
                    <li>
                        <input <?php echo $this->get_link(); ?> type="radio" class="visuallyhidden"
                               name="icon-<?php esc_attr_e($this->id); ?>" value="<?php esc_attr_e($key); ?>"
                               id="<?php esc_attr_e("$key-$this->id"); ?>">
                        <label for="<?php esc_attr_e("$key-$this->id"); ?>">
                            <span class="dashicons dashicons-<?php esc_attr_e($key); ?>" title="<?php esc_attr_e($name); ?>"></span>
                            <span class="visuallyhidden"><?php esc_attr_e($name); ?></span>
                        </label>
                    </li>
                <?php endforeach; ?>
                <li>
                    <input <?php echo $this->get_link(); ?> type="radio" class="visuallyhidden"
                           name="icon-<?php esc_attr_e($this->id); ?>" value="custom" id="custom-<?php esc_attr_e($this->id); ?>">
                    <label for="custom-<?php esc_attr_e($this->id); ?>">
                        <span><?php esc_html_e($customLabel); ?></span>
                    </label>
                </li>
                <?php if ($this->no_icon_allowed) : ?>
                    <li>
                        <input <?php echo $this->get_link(); ?> type="radio" class="visuallyhidden"
                               name="icon-<?php esc_attr_e($this->id); ?>" value="none" id="none-<?php esc_attr_e($this->id); ?>">
                        <label for="none-<?php esc_attr_e($this->id); ?>">
                            <span><?php esc_html_e($noneLabel); ?></span>
                        </label>
                    </li>
                <?php endif; ?>
            </ul>
        </fieldset>
        <?php
    }
}
