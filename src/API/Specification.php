<?php

namespace AlgolWishlist\API;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     openapi="3.0.0",
 * )
 * @OA\Info(
 *     version="1.0",
 *     title="Algol WC Wishlist",
 *     description="Algol WC Wishlist",
 * )
 * @OA\Server(
 *     url="http://localhost/wp-json/algol-wishlist/v1",
 *     description="HTTP Wishlist plugin API server",
 * )
 * @OA\Server(
 *     url="https://localhost/wp-json/algol-wishlist/v1",
 *     description="HTTPS Wishlist plugin API server",
 * )
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     name="X-WP-Nonce",
 *     in="header",
 *     securityScheme="apiKey"
 * )
 */
class Specification
{

}
