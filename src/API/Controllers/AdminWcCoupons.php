<?php

namespace AlgolWishlist\API\Controllers;

use AlgolWishlist\API\DTO\WcCoupon;
use OpenApi\Annotations as OA;
use WC_Coupon;
use WP_Http;
use WP_REST_Request;
use WP_REST_Response;

class AdminWcCoupons
{
    /**
     * @var string
     */
    protected $namespace;

    public function __construct()
    {
        $this->namespace = 'algol-wishlist/v1/admin';
    }

    public function registerRoutes()
    {
        register_rest_route($this->namespace, '/wcCoupons', array(
            [
                'methods' => "GET",
                'callback' => array($this, 'getCoupons'),
                'permission_callback' => [$this, 'canManageWoocommerce'],
            ],
        ));
    }

    public function canManageWoocommerce(): bool
    {
        return current_user_can('manage_woocommerce');
    }

    /**
     * @OA\Get(
     *     path="/admin/wcCoupons",
     *     tags={"Admin WC Coupons"},
     *     description="Returns coupons based on query",
     *     operationId="adminGetWcCoupons",
     *     @OA\Parameter(
     *         name="term",
     *         in="query",
     *         description="Limit results to those matching a string",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Maximum number of items to be returned in result set",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="list", type="array", @OA\Items(ref="#/components/schemas/WcCoupon"))
     *          ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Error",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     security={
     *        {"apiKey": {}}
     *     }
     * )
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function getCoupons(WP_REST_Request $request): WP_REST_Response
    {
        $term = sanitize_text_field(wp_unslash($request->get_param("term")));

        if (!empty($request->get_param("limit"))) {
            $limit = absint($request->get_param("limit"));
        } else {
            $limit = absint(apply_filters('woocommerce_json_search_limit', 30));
        }

        $couponObjects = array();
        $ids = get_posts(
            array(
                's' => $term,
                'post_type' => 'shop_coupon',
                'posts_per_page' => $limit,
                'fields' => 'ids',
            )
        );

        if (!empty($ids)) {
            foreach ($ids as $coupon_id) {
                $couponObjects[] = new WC_Coupon($coupon_id);
            }
        }

        $dtoCoupons = [];
        foreach ($couponObjects as $coupon_object) {
            $formatted_name = $coupon_object->get_code();
            $dtoCoupons[] = new WcCoupon($coupon_object->get_code(), rawurldecode($formatted_name));
        }

        return new WP_REST_Response(['list' => $dtoCoupons], WP_Http::OK);
    }
}
