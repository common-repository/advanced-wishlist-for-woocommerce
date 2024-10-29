<?php

namespace AlgolWishlist\API\Controllers;

use AlgolWishlist\API\DTO\Date;
use AlgolWishlist\API\DTO\Error;
use AlgolWishlist\API\DTO\ItemOfWishlist;
use AlgolWishlist\API\DTO\ItemOfWishlistRequest;
use AlgolWishlist\Repositories\ItemsOfWishlist\ItemOfWishlistEntity;
use AlgolWishlist\Repositories\ItemsOfWishlist\ItemsOfWishlistRepositoryWordpress;
use AlgolWishlist\Repositories\Wishlists\WishlistsRepositoryWordpress;
use AlgolWishlist\UrlBuilder;
use DateTime;
use DateTimeZone;
use Exception;
use OpenApi\Annotations as OA;
use WP_Http;
use WP_REST_Request;
use WP_REST_Response;

class AdminItemsOfWishlist
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
        register_rest_route($this->namespace, '/wishlists/(?P<wishlistId>\d+)/items/(?P<itemId>\d+)', array(
            [
                'methods' => "GET",
                'callback' => array($this, 'getItem'),
                'permission_callback' => [$this, 'canManageWoocommerce'],
            ],
            [
                'methods' => "DELETE",
                'callback' => array($this, 'deleteItem'),
                'permission_callback' => [$this, 'canManageWoocommerce'],
            ],
        ));

        register_rest_route($this->namespace, '/wishlists/(?P<wishlistId>\d+)/items', array(
            [
                'methods' => "GET",
                'callback' => array($this, 'getItems'),
                'permission_callback' => [$this, 'canManageWoocommerce'],
            ],
            [
                'methods' => "POST",
                'callback' => array($this, 'createItem'),
                'permission_callback' => [$this, 'canManageWoocommerce'],
            ],
            [
                'methods' => "PUT",
                'callback' => array($this, 'updateItem'),
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
     *     path="/admin/wishlists/{wishlistId}/items/{itemId}",
     *     tags={"Admin items of wishlist"},
     *     operationId="adminGetItemOfWishlistById",
     *     @OA\Parameter(
     *         name="wishlistId",
     *         in="path",
     *         description="ID of wishlist",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="itemId",
     *         in="path",
     *         description="ID of item from wishlist to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *              ref="#/components/schemas/ItemOfWishlist"
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
    public function getItem(WP_REST_Request $request): WP_REST_Response
    {
        $wishlistId = $request->get_param("wishlistId");
        $id = $request->get_param("itemId");

        if (!$id || !$wishlistId) {
            return new WP_REST_Response(null, WP_Http::BAD_REQUEST);
        }

        try {
            $wishlistAndProduct = $this->itemsOfWishlistRepositoryWordpress->getById($id);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        if ($wishlistAndProduct === null) {
            return new WP_REST_Response(null, WP_Http::NOT_FOUND);
        }

        return new WP_REST_Response(self::convertItemOfWishlistEntityToDto($wishlistAndProduct), WP_Http::OK);
    }

    /**
     * @OA\Post(
     *     path="/admin/wishlists/{wishlistId}/items",
     *     tags={"Admin items of wishlist"},
     *     operationId="adminAddItemOfWishlistById",
     *     @OA\Parameter(
     *         name="wishlistId",
     *         in="path",
     *         description="ID of wishlist",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="A wishlist item object that needs to be added",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ItemOfWishlistRequest"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *              ref="#/components/schemas/ItemOfWishlist"
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
    public function createItem(WP_REST_Request $request): WP_REST_Response
    {
        $wishlistId = $request->get_param("wishlistId");
        if (!$wishlistId) {
            return new WP_REST_Response(null, WP_Http::BAD_REQUEST);
        }

        $itemOfWishlistRequest = ItemOfWishlistRequest::fromBody($request->get_json_params());
        $itemOfWishlistRequest->wishlistId = $wishlistId;
        if (!$itemOfWishlistRequest->isValidForCreation()) {
            return new WP_REST_Response(null, WP_Http::BAD_REQUEST);
        }

        try {
            $createdAt = new DateTime('now', new DateTimeZone('UTC'));
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_create_date", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        try {
            $itemOfWishlist = $this->itemsOfWishlistRepositoryWordpress->create(
                new ItemOfWishlistEntity(
                    0,
                    $itemOfWishlistRequest->wishlistId,
                    $itemOfWishlistRequest->productId,
                    $itemOfWishlistRequest->quantity,
                    $itemOfWishlistRequest->variation,
                    $itemOfWishlistRequest->cartItemData,
                    $createdAt,
                    $itemOfWishlistRequest->priority
                )
            );
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_create_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        if ($itemOfWishlist === null) {
            return new WP_REST_Response(null, WP_Http::NOT_FOUND);
        }

        return new WP_REST_Response($itemOfWishlist, WP_Http::OK);
    }

    /**
     * @OA\Delete(
     *     path="/admin/wishlists/{wishlistId}/items/{itemId}",
     *     tags={"Admin items of wishlist"},
     *     operationId="adminDeleteItemOfWishlistById",
     *     @OA\Parameter(
     *         name="wishlistId",
     *         in="path",
     *         description="ID of wishlist",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="itemId",
     *         in="path",
     *         description="ID of item from wishlist to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
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
    public function deleteItem(WP_REST_Request $request): WP_REST_Response
    {
        $id = $request->get_param("itemId");

        if (!$id) {
            return new WP_REST_Response(null, WP_Http::BAD_REQUEST);
        }

        try {
            if (!$this->itemsOfWishlistRepositoryWordpress->deleteById($id)) {
                return new WP_REST_Response(null, WP_Http::NOT_FOUND);
            }
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_delete_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        return new WP_REST_Response(null, WP_Http::OK);
    }

    /**
     * @OA\Put(
     *     path="/admin/wishlists/{wishlistId}/items",
     *     tags={"Admin items of wishlist"},
     *     operationId="adminUpdateItemOfWishlistById",
     *     @OA\Parameter(
     *         name="wishlistId",
     *         in="path",
     *         description="ID of wishlist",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="A wishlist item object that needs to be updated",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ItemOfWishlistRequest"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *              ref="#/components/schemas/ItemOfWishlist"
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
    public function updateItem(WP_REST_Request $request): WP_REST_Response
    {
        $itemOfWishlistRequest = ItemOfWishlistRequest::fromBody($request->get_json_params());
        if ($itemOfWishlistRequest->id === null) {
            return new WP_REST_Response(null, WP_Http::BAD_REQUEST);
        }

        try {
            $wishlistAndProduct = $this->itemsOfWishlistRepositoryWordpress->getById($itemOfWishlistRequest->id);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        if ($itemOfWishlistRequest->productId !== null) {
            $wishlistAndProduct->productId = $itemOfWishlistRequest->productId;
        }

        if ($itemOfWishlistRequest->wishlistId !== null) {
            $wishlistAndProduct->wishlistId = $itemOfWishlistRequest->wishlistId;
        }

        if ($itemOfWishlistRequest->quantity !== null) {
            $wishlistAndProduct->quantity = $itemOfWishlistRequest->quantity;
        }

        if ($itemOfWishlistRequest->variation !== null) {
            $wishlistAndProduct->variation = $itemOfWishlistRequest->variation;
        }

        if ($itemOfWishlistRequest->cartItemData !== null) {
            $wishlistAndProduct->cartItemData = $itemOfWishlistRequest->cartItemData;
        }

        if ($itemOfWishlistRequest->priority !== null) {
            $wishlistAndProduct->priority = $itemOfWishlistRequest->priority;
        }

        try {
            $wishlistAndProduct = $this->itemsOfWishlistRepositoryWordpress->update($wishlistAndProduct);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_create_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        if ($wishlistAndProduct === null) {
            return new WP_REST_Response(null, WP_Http::NOT_FOUND);
        }

        return new WP_REST_Response(self::convertItemOfWishlistEntityToDto($wishlistAndProduct), WP_Http::OK);
    }

    /**
     * @OA\Get(
     *     path="/admin/wishlists/{wishlistId}/items",
     *     tags={"Admin items of wishlist"},
     *     operationId="adminGetAllItemOfWishlist",
     *     @OA\Parameter(
     *         name="wishlistId",
     *         in="path",
     *         description="ID of wishlist",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ExtendedItemOfWishlist")),
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
        $wishlistId = $request->get_param("wishlistId");

        try {
            $itemsOfWishlist = $this->itemsOfWishlistRepositoryWordpress->getAllByWishlistId($wishlistId);
        } catch (Exception $e) {
            return new WP_REST_Response([
                "code" => "unable_to_get_resource",
                "message" => $e->getMessage(),
            ], WP_Http::INTERNAL_SERVER_ERROR);
        }

        return new WP_REST_Response(
            array_map([$this, 'convertItemOfWishlistEntityToDto'], $itemsOfWishlist),
            WP_Http::OK
        );
    }

    protected static function convertItemOfWishlistEntityToDto(ItemOfWishlistEntity $entity): ItemOfWishlist
    {
        global $wpdb;
        $wishlistsRepository = new WishlistsRepositoryWordpress($wpdb);
        $wishlistEntity = $wishlistsRepository->getById($entity->wishlistId);
        $wishlistUrl = (new UrlBuilder())->getUrlToWishlist($wishlistEntity);

        return new ItemOfWishlist(
            $entity->id,
            $entity->wishlistId,
            $entity->productId,
            $entity->quantity,
            $entity->variation,
            $entity->cartItemData,
            Date::fromDateTime($entity->createdAt),
            $entity->priority,
            $wishlistUrl
        );
    }
}
