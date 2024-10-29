<?php

namespace AlgolWishlist\API\Controllers;

use AlgolWishlist\API\DTO\Date;
use AlgolWishlist\API\DTO\Error;
use AlgolWishlist\API\DTO\GetPopularItemsRequest;
use AlgolWishlist\API\DTO\GetUsersOfPopularItemRequest;
use AlgolWishlist\API\DTO\PopularItemsResponse;
use AlgolWishlist\API\DTO\PopularItemsResponse\CategoryOfPopularItem;
use AlgolWishlist\API\DTO\PopularItemsResponse\PopularItem;
use AlgolWishlist\API\DTO\UserOfPopularItemResponse\UserOfPopularItem;
use AlgolWishlist\API\DTO\UsersOfPopularItemResponse;
use AlgolWishlist\Repositories\ItemsOfWishlist\ItemsOfWishlistRepositoryWordpress;
use AlgolWishlist\Repositories\ItemsOfWishlist\PopularProductsOrderBy;
use AlgolWishlist\Repositories\ItemsOfWishlist\UsersOfPopularProductOrderBy;
use AlgolWishlist\Repositories\OrderByModifier;
use AlgolWishlist\Repositories\PreparedPagination;
use AlgolWishlist\Repositories\Wishlists\WishlistsRepositoryWordpress;
use DateTime;
use DateTimeZone;
use Exception;
use OpenApi\Annotations as OA;
use WP_Http;
use WP_REST_Request;
use WP_REST_Response;

class AdminPopularItems
{
    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var WishlistsRepositoryWordpress
     */
    protected $wishlistRepository;

    /**
     * @var ItemsOfWishlistRepositoryWordpress
     */
    protected $itemsOfWishlistRepositoryWordpress;

    public function __construct()
    {
        $this->namespace = 'algol-wishlist/v1';

        global $wpdb;
        $this->wishlistRepository = new WishlistsRepositoryWordpress($wpdb);
        $this->itemsOfWishlistRepositoryWordpress = new ItemsOfWishlistRepositoryWordpress($wpdb);
    }

    public function registerRoutes()
    {
        register_rest_route($this->namespace, '/admin/wishlists/popular-items', [
            [
                'methods' => "POST",
                'callback' => [$this, 'getItems'],
                'permission_callback' => [$this, 'canManageWoocommerce'],
            ],
        ]);

        register_rest_route($this->namespace, '/admin/wishlists/popular-items/users', [
            [
                'methods' => "POST",
                'callback' => [$this, 'getUsersOfPopularProducts'],
                'permission_callback' => [$this, 'canManageWoocommerce'],
            ],
        ]);

        register_rest_route($this->namespace, '/admin/wishlists/popular-items/users/file', [
            [
                'methods' => "POST",
                'callback' => [$this, 'getUsersOfPopularProductsAsFile'],
                'permission_callback' => [$this, 'canManageWoocommerce'],
            ],
        ]);
    }

    public function canManageWoocommerce(): bool
    {
        return current_user_can('manage_woocommerce');
    }

    /**
     * @OA\Post(
     *     path="/admin/wishlists/popular-items",
     *     tags={"Admin popular products"},
     *     operationId="adminGetPopularProducts",
     *     @OA\RequestBody(
     *         description="Fetch request body",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/GetPopularItemsRequest"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/PopularItemsResponse"),
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
    public function getItems(WP_REST_Request $request): WP_REST_Response
    {
        $getPopularItemsRequest = GetPopularItemsRequest::fromBody($request->get_json_params());

        try {
            $since = new DateTime('now', new DateTimeZone('UTC'));
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_date", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        switch ($getPopularItemsRequest->timeRange) {
            case "last_day":
            {
                $since->modify('-1 day');
                break;
            }
            case "last_week":
            {
                $since->modify('-1 week');
                break;
            }
            case "last_month":
            {
                $since->modify('-1 month');
                break;
            }
            case "all_time":
            {
                $since->setTimestamp(0);
                break;
            }
            default:
            {
                return new WP_REST_Response(
                    new Error("unable_to_get_resource", "Unsupported time range"),
                    WP_Http::BAD_REQUEST
                );
            }
        }

        $stringQuery = $getPopularItemsRequest->searchByText ?: "";

        $pagination = $getPopularItemsRequest->pagination;
        if ($pagination === null || !$pagination->isValid() || $pagination->currentPage < 1) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", "Incorrect 'pagination' parameter"),
                WP_Http::BAD_REQUEST
            );
        }
        $preparedPagination = new PreparedPagination($pagination->perPage, $pagination->currentPage);


        $ordination = new PopularProductsOrderBy();
        try {
            foreach ($getPopularItemsRequest->sorting as $item) {
                $ordination->add($item->field, new OrderByModifier(strtoupper($item->sort)));
            }
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        try {
            $results = $this->itemsOfWishlistRepositoryWordpress->getAllByTextQueryAndSinceDate(
                $stringQuery,
                $since,
                $ordination,
                $preparedPagination
            );

            $total = $this->itemsOfWishlistRepositoryWordpress->getLastFoundRowsCount();
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        /** @var array<int, PopularItem> $items */
        $items = [];

        foreach ($results as $result) {
            $id = (int)$result->productId;
            $parentId = (int)$result->parentId;

            $categories = [];
            foreach (array_unique(wc_get_product_cat_ids($parentId ?: $id)) as $categoryId) {
                $category = get_term($categoryId);

                if (is_wp_error($category)) {
                    continue;
                }

                $categories[] = new CategoryOfPopularItem(
                    $category->term_id,
                    $category->name
                );
            }

            $items[] = new PopularItem(
                $id,
                $result->name,
                json_decode($result->variation, true),
                json_decode($result->cartItemData, true),
                $categories,
                (int)$result->counter,
                get_permalink($id)
            );
        }

        return new WP_REST_Response(new PopularItemsResponse($items, $total), WP_Http::OK);
    }

    /**
     * @OA\Post(
     *     path="/admin/wishlists/popular-items/users",
     *     tags={"Admin popular products"},
     *     operationId="adminGetUsersOfPopularItem",
     *     @OA\RequestBody(
     *         description="Fetch request body",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/GetUsersOfPopularItemRequest"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/UsersOfPopularItemResponse"),
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
    public function getUsersOfPopularProducts(WP_REST_Request $request): WP_REST_Response
    {
        $getUsersOfPopularItemRequest = GetUsersOfPopularItemRequest::fromBody($request->get_json_params());

        if ($getUsersOfPopularItemRequest->productId === null) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", "Missing 'productId' parameter"),
                WP_Http::BAD_REQUEST
            );
        }

        $pagination = $getUsersOfPopularItemRequest->pagination;
        if ($pagination === null || !$pagination->isValid() || $pagination->currentPage < 1) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", "Incorrect 'pagination' parameter"),
                WP_Http::BAD_REQUEST
            );
        }
        $preparedPagination = new PreparedPagination($pagination->perPage, $pagination->currentPage);

        $ordination = new UsersOfPopularProductOrderBy();
        try {
            foreach ($getUsersOfPopularItemRequest->sorting as $item) {
                $ordination->add($item->field, new OrderByModifier(strtoupper($item->sort)));
            }
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        try {
            $results = $this->itemsOfWishlistRepositoryWordpress->getUsersByWishlistedProductId(
                $getUsersOfPopularItemRequest->productId,
                $ordination,
                $preparedPagination
            );

            $total = $this->itemsOfWishlistRepositoryWordpress->getLastFoundRowsCount();
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        /** @var array<int, UserOfPopularItem> $items */
        $items = [];

        foreach ($results as $result) {
            $id = (int)$result->userId;
            $editLink = get_edit_user_link($id);
            $thumbnail = get_avatar($id, 32);

            $items[] = new UserOfPopularItem(
                $id,
                "<a href='$editLink'>$thumbnail</a>",
                $result->name,
                Date::fromDateTime(
                    DateTime::createFromFormat(
                        "Y-m-d H:i:s",
                        $result->addedOn,
                        new DateTimeZone('UTC')
                    )
                )
            );
        }

        return new WP_REST_Response(new UsersOfPopularItemResponse($items, $total), WP_Http::OK);
    }

    /**
     * @OA\Post(
     *     path="/admin/wishlists/popular-items/users/file",
     *     tags={"Admin popular products"},
     *     operationId="adminGetUsersOfPopularItemAsFile",
     *     @OA\RequestBody(
     *         description="Fetch request body",
     *         required=true,
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="productId", type="integer"),
     *              @OA\Property(property="variation", type="object"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\MediaType(mediaType="text/csv"),
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
    public function getUsersOfPopularProductsAsFile(WP_REST_Request $request): WP_REST_Response
    {
        $productId = $request->get_param("productId");
        $productId = $productId ?: 0;

        $variation = $request->get_param("variation");
        $variation = $variation?: [];

        if ( ! $productId ) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", "Missing 'productId' parameter"),
                WP_Http::BAD_REQUEST
            );
        }

        try {
            $results = $this->itemsOfWishlistRepositoryWordpress->getUsersByWishlistedProductIdAndWishlistedVariation(
                $productId,
                $variation
            );
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        $items = [];
        $items[] = ["User ID", "User Email", "User First Name", "User Last Name"];
        foreach ($results as $result) {
            $userId = (int)$result->userId;
            $user = get_user_by( 'id', $userId );
            if ( $user ) {
                $items[] = [$userId, $user->user_email, $user->first_name, $user->last_name];
            }
        }

        $product = wc_get_product($productId);
        if (!$product) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", "Product doesn't exist"),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }
        $productName = $product->get_name();
        $fileName = "$productName - Users.csv";

        header('Content-type: text/csv');
        header('Expires: 0');
        header("Content-Disposition: attachment; filename=\"$fileName\";");

        $fp = fopen('php://output', 'w');
        foreach ($items as $item) {
            fputcsv($fp, $item);
        }
        fclose($fp);

        return new WP_REST_Response(null, WP_Http::OK);
    }

}
