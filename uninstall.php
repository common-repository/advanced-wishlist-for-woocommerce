<?php

if (defined('WP_UNINSTALL_PLUGIN')) {
    include_once "vendor/autoload.php";
    include_once "awlContext.php";

    $path = trailingslashit(dirname(__FILE__));

    $pluginActions = (\AlgolWishlist\Factory::get("PluginActions", $path));
    $pluginActions->uninstall();
}
