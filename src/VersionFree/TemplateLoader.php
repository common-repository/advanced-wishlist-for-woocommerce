<?php

namespace AlgolWishlist\VersionFree;

defined('ABSPATH') or exit;

class TemplateLoader
{
    /**
     * @param string $templateName
     * @param array $args
     * @param string $templatePath
     *
     * @return false|string
     */
    public static function getTemplate($templateName, $args = array(), $templatePath = '')
    {
        if (!empty($args) && is_array($args)) {
            extract($args);
        }

        $fullTemplatePath = trailingslashit(ALGOL_WISHLIST_PLUGIN_PATH . 'src/VersionFree/templates/');

        if ($templatePath) {
            $fullTemplatePath .= trailingslashit($templatePath);
        }

        $fullExternalTemplatePath = locate_template(array(
            'wc-wishlist/' . trailingslashit($templatePath) . $templateName,
            'wc-wishlist/' . $templateName,
        ));

        if ($fullExternalTemplatePath) {
            $fullTemplatePath = $fullExternalTemplatePath;
        } else {
            $fullTemplatePath .= $templateName;
        }

        ob_start();
        include $fullTemplatePath;
        $templateContent = ob_get_clean();

        return $templateContent;
    }
}
