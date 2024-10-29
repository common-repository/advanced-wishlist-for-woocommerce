<?php

namespace AlgolWishlist\Compatibility;

class SwaggerUICompatibility
{
    public function replaceSchema()
    {
        add_action('wp', function () {
            if (get_query_var('swagger_api') !== 'schema') {
                return;
            }

            if ( trim(get_option('swagger_api_basepath', '/wp/v2'), '/') !== "algol-wishlist/v1" ) {
                return;
            }

            if (!class_exists("\WP_API_SwaggerUI")) {
                return;
            }

            $swaggerUiInstance = new \WP_API_SwaggerUI();

            $config = file_get_contents(ALGOL_WISHLIST_PLUGIN_PATH . "/swagger.json");
            $config = json_decode($config);
            $config->servers[0]->url = str_replace("localhost", $swaggerUiInstance->getHost(), $config->servers[0]->url);
            $config->servers[1]->url = str_replace("localhost", $swaggerUiInstance->getHost(), $config->servers[1]->url);

            wp_send_json($config);
        }, 1);
    }
}
