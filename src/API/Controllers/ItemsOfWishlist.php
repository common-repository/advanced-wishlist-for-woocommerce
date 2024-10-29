<?php

namespace AlgolWishlist\API\Controllers;

use AlgolWishlist\API\DTO\AddIntoDefaultWishlistRequest;
use AlgolWishlist\API\DTO\AddToCartAllItemsOfWishlistRequest;
use AlgolWishlist\API\DTO\AddToCartItemOfWishlistRequest;
use AlgolWishlist\API\DTO\BatchAddToCartItemsOfWishlistRequest;
use AlgolWishlist\API\DTO\BatchDeleteItemsOfWishlistRequest;
use AlgolWishlist\API\DTO\BulkActionMessage;
use AlgolWishlist\API\DTO\Date;
use AlgolWishlist\API\DTO\Error;
use AlgolWishlist\API\DTO\ExecuteActionsOnItemsOfWishlistResponse;
use AlgolWishlist\API\DTO\ExtendedItemOfWishlist;
use AlgolWishlist\API\DTO\ItemOfWishlist;
use AlgolWishlist\API\DTO\ItemOfWishlistRequest;
use AlgolWishlist\API\DTO\ReorderItemsOfWishlistRequest;
use AlgolWishlist\Context;
use AlgolWishlist\Repositories\ItemsOfWishlist\ItemOfWishlistEntity;
use AlgolWishlist\Repositories\ItemsOfWishlist\ItemsOfWishlistRepositoryWordpress;
use AlgolWishlist\Repositories\Wishlists\ShareTypeEnum;
use AlgolWishlist\Repositories\Wishlists\WishlistsRepositoryWordpress;
use AlgolWishlist\UrlBuilder;
use Automattic\WooCommerce\StoreApi\Utilities\CartController;
use DateTime;
use DateTimeZone;
use Exception;
use OpenApi\Annotations as OA;
use WP_Http;
use WP_REST_Request;
use WP_REST_Response;

class ItemsOfWishlist
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

    /**
     * @var Context
     */
    private $context;

    public function __construct()
    {
        $this->namespace = 'algol-wishlist/v1';

        global $wpdb;
        $this->wishlistRepository = new WishlistsRepositoryWordpress($wpdb);
        $this->itemsOfWishlistRepositoryWordpress = new ItemsOfWishlistRepositoryWordpress($wpdb);

        $this->context = awlContext();
    }

    public function registerRoutes()
    {
        register_rest_route($this->namespace, '/wishlists/(?P<wishlistId>\d+)/items/(?P<itemId>\d+)', array(
            [
                'methods' => "GET",
                'callback' => array($this, 'getItem'),
                'permission_callback' => [$this, 'canGetWishlist'],
            ],
            [
                'methods' => "DELETE",
                'callback' => array($this, 'deleteItem'),
                'permission_callback' => [$this, 'canModifyWishlist'],
            ],
        ));

        register_rest_route($this->namespace, '/wishlists/(?P<wishlistId>\d+)/items', array(
            [
                'methods' => "GET",
                'callback' => array($this, 'getItems'),
                'permission_callback' => [$this, 'canGetWishlist'],
            ],
            [
                'methods' => "POST",
                'callback' => array($this, 'createItem'),
                'permission_callback' => [$this, 'canModifyWishlist'],
            ],
            [
                'methods' => "PUT",
                'callback' => array($this, 'updateItem'),
                'permission_callback' => [$this, 'canModifyWishlist'],
            ],
        ));

        register_rest_route($this->namespace, '/wishlists/default/items', array(
            [
                'methods' => "POST",
                'callback' => array($this, 'createItemInDefaultWishlist'),
            ]
        ));

        register_rest_route($this->namespace, '/wishlists/default/items/(?P<itemId>\d+)', array(
            [
                'methods' => "DELETE",
                'callback' => array($this, 'deleteItemFromDefault'),
            ]
        ));

        register_rest_route($this->namespace, '/wishlists/(?P<wishlistId>\d+)/items/(?P<itemId>\d+)/action/add-to-cart', array(
            [
                'methods' => "POST",
                'callback' => array($this, 'addToCartItemOfWishlist'),
                'permission_callback' => [$this, 'canGetWishlist'],
            ]
        ));

        register_rest_route($this->namespace, '/wishlists/(?P<wishlistId>\d+)/items/action/reorder', array(
            [
                'methods' => "POST",
                'callback' => array($this, 'reorderItemsOfWishlist'),
                'permission_callback' => [$this, 'canModifyWishlist'],
            ],
        ));

        register_rest_route($this->namespace, '/wishlists/(?P<wishlistId>\d+)/items/action/batch-delete', array(
            [
                'methods' => "POST",
                'callback' => array($this, 'batchDeleteItemsOfWishlist'),
                'permission_callback' => [$this, 'canModifyWishlist'],
            ],
        ));

        register_rest_route($this->namespace, '/wishlists/(?P<wishlistId>\d+)/items/action/batch-add-to-cart', array(
            [
                'methods' => "POST",
                'callback' => array($this, 'batchAddToCartItemsOfWishlist'),
                'permission_callback' => [$this, 'canGetWishlist'],
            ],
        ));

        register_rest_route($this->namespace, '/wishlists/(?P<wishlistId>\d+)/items/action/add-to-cart-all', array(
            [
                'methods' => "POST",
                'callback' => array($this, 'addToCartAllItemsOfWishlist'),
                'permission_callback' => [$this, 'canGetWishlist'],
            ],
        ));
    }

    public function canGetWishlist(WP_REST_Request $request): bool
    {
        $wishlistId = $request->get_param("wishlistId");
        $user = $this->context->getCurrentUser();

        try {
            $wishlist = $this->wishlistRepository->getById($wishlistId);
        } catch (Exception $e) {
            return false;
        }

        if ( $wishlist->shareType->equals(ShareTypeEnum::PUBLIC()) ) {
            return true;
        }

        if ( $user->isGuest() ) {
            return $wishlist->sessionKey === $user->getSessionKey();
        } else {
            return $wishlist->ownerId === $user->getUserId();
        }
    }

    public function canModifyWishlist(WP_REST_Request $request): bool
    {
        $wishlistId = $request->get_param("wishlistId");
        $user = $this->context->getCurrentUser();

        try {
            $wishlist = $this->wishlistRepository->getById($wishlistId);
        } catch (Exception $e) {
            return false;
        }

        if ( $user->isGuest() ) {
            return $wishlist->sessionKey === $user->getSessionKey();
        } else {
            return $wishlist->ownerId === $user->getUserId();
        }
    }

    /**
     * @OA\Get(
     *     path="/wishlists/{wishlistId}/items/{itemId}",
     *     tags={"Items of wishlist"},
     *     operationId="getItemOfWishlistById",
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
            $wishlistAndProduct = $this->itemsOfWishlistRepositoryWordpress->getByIdAndWishlistId(
                $id,
                $wishlistId
            );
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        if ($wishlistAndProduct === null) {
            return new WP_REST_Response(null, WP_Http::NOT_FOUND);
        }

        try {
            $itemWishlist = self::convertItemOfWishlistEntityToDto($wishlistAndProduct);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_convert_to_dto", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        return new WP_REST_Response($itemWishlist, WP_Http::OK);
    }

    /**
     * @OA\Post(
     *     path="/wishlists/{wishlistId}/items",
     *     tags={"Items of wishlist"},
     *     operationId="addItemOfWishlistById",
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
            $wishlistAndProduct = $this->itemsOfWishlistRepositoryWordpress->create(
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

        if ($wishlistAndProduct === null) {
            return new WP_REST_Response(null, WP_Http::NOT_FOUND);
        }

        return new WP_REST_Response($wishlistAndProduct, WP_Http::OK);
    }

    /**
     * @OA\Delete(
     *     path="/wishlists/{wishlistId}/items/{itemId}",
     *     tags={"Items of wishlist"},
     *     operationId="deleteItemOfWishlistById",
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
        $wishlistId = $request->get_param("wishlistId");

        if (!$id) {
            return new WP_REST_Response(null, WP_Http::BAD_REQUEST);
        }

        try {
            if (!$this->itemsOfWishlistRepositoryWordpress->deleteByIdAndWishlistId($id, $wishlistId)) {
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
     *     path="/wishlists/{wishlistId}/items",
     *     tags={"Items of wishlist"},
     *     operationId="updateItemOfWishlistById",
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
        $wishlistId = $request->get_param("wishlistId");

        $itemOfWishlistRequest = ItemOfWishlistRequest::fromBody($request->get_json_params());
        if ($itemOfWishlistRequest->id === null) {
            return new WP_REST_Response(null, WP_Http::BAD_REQUEST);
        }

        $user = $this->context->getCurrentUser();

        try {
            $itemOfWishlistEntity = $this->itemsOfWishlistRepositoryWordpress->getByIdAndWishlistId(
                $itemOfWishlistRequest->id,
                $wishlistId
            );
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        if ($itemOfWishlistRequest->productId !== null) {
            $itemOfWishlistEntity->productId = $itemOfWishlistRequest->productId;
        }

        if ($itemOfWishlistRequest->wishlistId !== null) {
            try {
                $newWishlist = $this->wishlistRepository->getById($itemOfWishlistRequest->wishlistId);
            } catch (Exception $e) {
                return new WP_REST_Response(
                    new Error("unable_to_get_resource", $e->getMessage()),
                    WP_Http::INTERNAL_SERVER_ERROR
                );
            }

            if ( ! $user->isGuest() ) {
                if ( $newWishlist->ownerId === $user->getUserId() ) {
                    $itemOfWishlistEntity->wishlistId = $newWishlist->id;
                }
            }
        }

        if ($itemOfWishlistRequest->quantity !== null) {
            $itemOfWishlistEntity->quantity = $itemOfWishlistRequest->quantity;
        }

        if ($itemOfWishlistRequest->variation !== null) {
            $itemOfWishlistEntity->variation = $itemOfWishlistRequest->variation;
        }

        if ($itemOfWishlistRequest->cartItemData !== null) {
            $itemOfWishlistEntity->cartItemData = $itemOfWishlistRequest->cartItemData;
        }

        if ($itemOfWishlistRequest->priority !== null) {
            $itemOfWishlistEntity->priority = $itemOfWishlistRequest->priority;
        }

        try {
            $itemOfWishlistEntity = $this->itemsOfWishlistRepositoryWordpress->update($itemOfWishlistEntity);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_create_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        if ($itemOfWishlistEntity === null) {
            return new WP_REST_Response(null, WP_Http::NOT_FOUND);
        }

        try {
            $itemWishlist = self::convertItemOfWishlistEntityToDto($itemOfWishlistEntity);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_convert_to_dto", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        return new WP_REST_Response($itemWishlist, WP_Http::OK);
    }

    /**
     * @OA\Get(
     *     path="/wishlists/{wishlistId}/items",
     *     tags={"Items of wishlist"},
     *     operationId="getAllItemOfWishlist",
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
            $list = $this->getExtendedItemsOfWishlistByWishlistId($wishlistId);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        return new WP_REST_Response($list, WP_Http::OK);
    }

    /**
     * @OA\Post(
     *     path="/wishlists/default/items",
     *     tags={"Items of wishlist"},
     *     operationId="addItemIntoDefaultWishlist",
     *     @OA\RequestBody(
     *         description="A wishlist item object that needs to be added",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AddIntoDefaultWishlistRequest"),
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
    public function createItemInDefaultWishlist(WP_REST_Request $request): WP_REST_Response
    {
        $requestBody = AddIntoDefaultWishlistRequest::fromBody($request->get_json_params());
        $wishlist = $this->context->getCurrentUser()->getDefaultWishlist();

        try {
            $createdAt = new DateTime('now', new DateTimeZone('UTC'));
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_create_date", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        try {
            $wishlistAndProduct = $this->itemsOfWishlistRepositoryWordpress->create(
                new ItemOfWishlistEntity(
                    0,
                    $wishlist->id,
                    $requestBody->productId,
                    $requestBody->quantity,
                    $requestBody->variation,
                    $requestBody->cartItemData,
                    $createdAt,
                    $requestBody->priority
                )
            );
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_create_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        if ($wishlistAndProduct === null) {
            return new WP_REST_Response(null, WP_Http::NOT_FOUND);
        }

        try {
            $itemWishlist = self::convertItemOfWishlistEntityToDto($wishlistAndProduct);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_convert_to_dto", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        return new WP_REST_Response($itemWishlist, WP_Http::OK);
    }

    /**
     * @OA\Delete(
     *     path="/wishlists/default/items/{itemId}",
     *     tags={"Items of wishlist"},
     *     operationId="deleteItemOfDefaultWishlistById",
     *     @OA\Parameter(
     *         name="itemId",
     *         in="path",
     *         description="ID of item from wishlist to delete",
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
    public function deleteItemFromDefault(WP_REST_Request $request): WP_REST_Response
    {
        $id = $request->get_param("itemId");

        if (!$id) {
            return new WP_REST_Response(null, WP_Http::BAD_REQUEST);
        }

        $defaultWishlist = $this->context->getCurrentUser()->getDefaultWishlist();

        if ($defaultWishlist === null) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", "Wishlist not found"),
                WP_Http::NOT_FOUND
            );
        }

        try {
            if (!$this->itemsOfWishlistRepositoryWordpress->deleteByIdAndWishlistId($id, $defaultWishlist->id)) {
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
     * @OA\Post(
     *     path="/wishlists/{wishlistId}/items/action/reorder",
     *     tags={"Items of wishlist"},
     *     operationId="ReorderItemsOfWishlist",
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
     *         description="An action object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ReorderItemsOfWishlistRequest"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ExecuteActionsOnItemsOfWishlistResponse"),
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
    public function reorderItemsOfWishlist(WP_REST_Request $request): WP_REST_Response
    {
        $wishlistId = $request->get_param("wishlistId");
        $action = ReorderItemsOfWishlistRequest::fromBody($request->get_json_params());

        try {
            $list = $this->getExtendedItemsOfWishlistByWishlistId($wishlistId);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        $list = array_combine(array_column($list, 'id'), $list);

        // remove items that are not in the target wishlist while saving an ordination
        $actionItemOfWishlistIds = array_intersect($action->itemIds, array_column($list, 'id'));

        $counter = 0;
        $newOrdination = array_map(function ($itemId) use (&$counter) {
            return [
                "id" => $itemId,
                "priority" => $counter++,
            ];
        }, $actionItemOfWishlistIds);

        $messages = [];

        try {
            $this->itemsOfWishlistRepositoryWordpress->reorderProductsByWishlistId($newOrdination);
        } catch (Exception $e) {
            $messages[] = new BulkActionMessage(
                BulkActionMessage::TYPE_ERROR,
                0,
                "",
                "unable_to_reorder_resources",
                $e->getMessage()
            );
        }

        try {
            $list = $this->getExtendedItemsOfWishlistByWishlistId($wishlistId);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        return new WP_REST_Response(
            new ExecuteActionsOnItemsOfWishlistResponse(
                $messages,
                $list
            ), WP_Http::OK
        );
    }

    /**
     * @OA\Post(
     *     path="/wishlists/{wishlistId}/items/action/batch-delete",
     *     tags={"Items of wishlist"},
     *     operationId="BatchDeleteItemsOfWishlist",
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
     *         description="An action object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BatchDeleteItemsOfWishlistRequest"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ExecuteActionsOnItemsOfWishlistResponse"),
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
    public function batchDeleteItemsOfWishlist(WP_REST_Request $request): WP_REST_Response
    {
        $wishlistId = $request->get_param("wishlistId");
        $action = BatchDeleteItemsOfWishlistRequest::fromBody($request->get_json_params());

        try {
            $list = $this->getExtendedItemsOfWishlistByWishlistId($wishlistId);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        $list = array_combine(array_column($list, 'id'), $list);

        // remove items that are not in the target wishlist while saving an ordination
        $actionItemOfWishlistIds = array_intersect($action->itemIds, array_column($list, 'id'));

        $messages = [];

        foreach ($actionItemOfWishlistIds as $itemId) {
            try {
                if (!$this->itemsOfWishlistRepositoryWordpress->deleteByIdAndWishlistId($itemId, $wishlistId)) {
                    $messages[] = new BulkActionMessage(
                        BulkActionMessage::TYPE_ERROR,
                        $itemId,
                        $list[$itemId]->title ?? "",
                        "unable_to_delete_resource",
                        __("Not Found", 'wc-wishlist')
                    );

                    continue;
                }
            } catch (Exception $e) {
                $messages[] = new BulkActionMessage(
                    BulkActionMessage::TYPE_ERROR,
                    $itemId,
                    $list[$itemId]->title ?? "",
                    "unable_to_delete_resource",
                    $e->getMessage()
                );

                continue;
            }

            $messages[] = new BulkActionMessage(
                BulkActionMessage::TYPE_SUCCESS,
                $itemId,
                $list[$itemId]->title ?? "",
                "deleted_from_cart",
                _x(
                    "\"{$list[$itemId]->title}\" has been deleted from your cart.",
                    "item added to cart",
                    'wc-wishlist'
                )
            );
        }

        try {
            $list = $this->getExtendedItemsOfWishlistByWishlistId($wishlistId);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        return new WP_REST_Response(
            new ExecuteActionsOnItemsOfWishlistResponse(
                $messages,
                $list
            ), WP_Http::OK
        );
    }

    /**
     * @OA\Post(
     *     path="/wishlists/{wishlistId}/items/action/batch-add-to-cart",
     *     tags={"Items of wishlist"},
     *     operationId="BatchAddToCartItemsOfWishlist",
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
     *         description="An action object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BatchAddToCartItemsOfWishlistRequest"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ExecuteActionsOnItemsOfWishlistResponse"),
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
    public function batchAddToCartItemsOfWishlist(WP_REST_Request $request): WP_REST_Response
    {
        $wishlistId = $request->get_param("wishlistId");
        $action = BatchAddToCartItemsOfWishlistRequest::fromBody($request->get_json_params());

        try {
            $list = $this->getExtendedItemsOfWishlistByWishlistId($wishlistId);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        $list = array_combine(array_column($list, 'id'), $list);

        // remove items that are not in the target wishlist while saving an ordination
        $actionItemOfWishlistIds = array_intersect($action->itemIds, array_column($list, 'id'));

        $messages = [];

        $cartController = new CartController();
        $cartController->load_cart();
        $cartController->get_cart_instance()->get_cart_from_session();

        foreach ($actionItemOfWishlistIds as $itemId) {
            $itemOfWishlist = $list[$itemId] ?? null;

            if (!$itemOfWishlist) {
                continue;
            }

            $variationData = $this->convertVariationData($itemOfWishlist);

            try {
                $cartId = $cartController->add_to_cart(
                    [
                        'id' => $itemOfWishlist->productId,
                        'quantity' => $itemOfWishlist->quantity,
                        'variation' => $variationData,
                        'cart_item_data' => $itemOfWishlist->cartItemData,
                    ]
                );
            } catch (Exception $e) {
                $messages[] = new BulkActionMessage(
                    BulkActionMessage::TYPE_ERROR,
                    $itemId,
                    $list[$itemId]->title ?? "",
                    "unable_to_add_to_cart",
                    $e->getMessage()
                );
                continue;
            }

            if ($cartId) {
                $messages[] = new BulkActionMessage(
                    BulkActionMessage::TYPE_SUCCESS,
                    $itemId,
                    $list[$itemId]->title ?? "",
                    "added_to_cart",
                    _x(
                        "\"{$list[$itemId]->title}\" has been added to your cart.",
                        "item added to cart",
                        'wc-wishlist'
                    )
                );
            }

            if ($action->deleteAddedToCart && $this->canModifyWishlist($request)) {
                try {
                    if (!$this->itemsOfWishlistRepositoryWordpress->deleteByIdAndWishlistId($itemId, $wishlistId)) {
                        $messages[] = new BulkActionMessage(
                            BulkActionMessage::TYPE_ERROR,
                            $itemId,
                            $list[$itemId]->title ?? "",
                            "unable_to_delete_resource",
                            __("Not Found", 'wc-wishlist')
                        );
                        continue;
                    }
                } catch (Exception $e) {
                    $messages[] = new BulkActionMessage(
                        BulkActionMessage::TYPE_ERROR,
                        $itemId,
                        $list[$itemId]->title ?? "",
                        "unable_to_delete_resource",
                        $e->getMessage()
                    );
                    continue;
                }
            }

            if ($action->withWcNotices) {
                foreach ( $messages as $message ) {
                    wc_add_notice($message->message, $message->type);
                }
            }
        }

        try {
            $list = $this->getExtendedItemsOfWishlistByWishlistId($wishlistId);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        return new WP_REST_Response(
            new ExecuteActionsOnItemsOfWishlistResponse(
                $messages,
                $list
            ), WP_Http::OK
        );
    }

    /**
     * @OA\Post(
     *     path="/wishlists/{wishlistId}/items/{itemId}/action/add-to-cart",
     *     tags={"Items of wishlist"},
     *     operationId="AddToCartItemOfWishlist",
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
     *     @OA\RequestBody(
     *         description="An action object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AddToCartItemOfWishlistRequest"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ExecuteActionsOnItemsOfWishlistResponse"),
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
    public function addToCartItemOfWishlist(WP_REST_Request $request): WP_REST_Response
    {
        $wishlistId = $request->get_param("wishlistId");
        $itemId = $request->get_param("itemId");
        $action = AddToCartItemOfWishlistRequest::fromBody($request->get_json_params());

        try {
            $list = $this->getExtendedItemsOfWishlistByWishlistId($wishlistId);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        $list = array_combine(array_column($list, 'id'), $list);

        $itemOfWishlist = $list[$itemId] ?? null;

        if (!$itemOfWishlist) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", __("Not Found", 'wc-wishlist')),
                WP_Http::NOT_FOUND
            );
        }

        $messages = [];

        $cartController = new CartController();
        $cartController->load_cart();
        $cartController->get_cart_instance()->get_cart_from_session();

        $variationData = $this->convertVariationData($itemOfWishlist);

        try {
            $cartId = $cartController->add_to_cart(
                [
                    'id' => $itemOfWishlist->productId,
                    'quantity' => $itemOfWishlist->quantity,
                    'variation' => $variationData,
                    'cart_item_data' => $itemOfWishlist->cartItemData,
                ]
            );
        } catch (Exception $e) {
            $messages[] = new BulkActionMessage(
                BulkActionMessage::TYPE_ERROR,
                $itemId,
                $list[$itemId]->title ?? "",
                "unable_to_add_to_cart",
                $e->getMessage()
            );

            $cartId = null;
        }

        if ($cartId) {
            $messages[] = new BulkActionMessage(
                BulkActionMessage::TYPE_SUCCESS,
                $itemId,
                $list[$itemId]->title ?? "",
                "added_to_cart",
                _x(
                    "\"{$list[$itemId]->title}\" has been added to your cart.",
                    "item added to cart",
                    'wc-wishlist'
                )
            );
        }

        if ($cartId && $action->deleteAddedToCart && $this->canModifyWishlist($request)) {
            try {
                if (!$this->itemsOfWishlistRepositoryWordpress->deleteByIdAndWishlistId($itemId, $wishlistId)) {
                    $messages[] = new BulkActionMessage(
                        BulkActionMessage::TYPE_ERROR,
                        $itemId,
                        $list[$itemId]->title ?? "",
                        "unable_to_delete_resource",
                        __("Not Found", 'wc-wishlist')
                    );
                }
            } catch (Exception $e) {
                $messages[] = new BulkActionMessage(
                    BulkActionMessage::TYPE_ERROR,
                    $itemId,
                    $list[$itemId]->title ?? "",
                    "unable_to_delete_resource",
                    $e->getMessage()
                );
            }
        }

        if ($action->withWcNotices) {
            foreach ($messages as $message) {
                wc_add_notice($message->message, $message->type);
            }
        }

        try {
            $list = $this->getExtendedItemsOfWishlistByWishlistId($wishlistId);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        return new WP_REST_Response(
            new ExecuteActionsOnItemsOfWishlistResponse(
                $messages,
                $list
            ), WP_Http::OK
        );
    }

    /**
     * @OA\Post(
     *     path="/wishlists/{wishlistId}/items/action/add-to-cart-all",
     *     tags={"Items of wishlist"},
     *     operationId="addToCartAllItemsOfWishlist",
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
     *         description="An action object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AddToCartAllItemsOfWishlistRequest"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ExecuteActionsOnItemsOfWishlistResponse"),
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
    public function addToCartAllItemsOfWishlist(WP_REST_Request $request): WP_REST_Response
    {
        $wishlistId = $request->get_param("wishlistId");
        $action = AddToCartAllItemsOfWishlistRequest::fromBody($request->get_json_params());

        try {
            $list = $this->getExtendedItemsOfWishlistByWishlistId($wishlistId);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        $actionItemOfWishlistIds = array_combine(array_column($list, 'id'), $list);

        $messages = [];

        $cartController = new CartController();
        $cartController->load_cart();
        $cartController->get_cart_instance()->get_cart_from_session();

        foreach ($actionItemOfWishlistIds as $itemId => $itemOfWishlist) {
            $variationData = $this->convertVariationData($itemOfWishlist);

            try {
                $cartId = $cartController->add_to_cart(
                    [
                        'id' => $itemOfWishlist->productId,
                        'quantity' => $itemOfWishlist->quantity,
                        'variation' => $variationData,
                        'cart_item_data' => $itemOfWishlist->cartItemData,
                    ]
                );
            } catch (Exception $e) {
                $messages[] = new BulkActionMessage(
                    BulkActionMessage::TYPE_ERROR,
                    $itemId,
                    $itemOfWishlist->title ?? "",
                    "unable_to_add_to_cart",
                    $e->getMessage()
                );
                continue;
            }

            if ($cartId) {
                $messages[] = new BulkActionMessage(
                    BulkActionMessage::TYPE_SUCCESS,
                    $itemId,
                    $itemOfWishlist->title ?? "",
                    "added_to_cart",
                    _x(
                        "\"{$itemOfWishlist->title}\" has been added to your cart.",
                        "item added to cart",
                        'wc-wishlist'
                    )
                );
            }

            if ($action->deleteAddedToCart && $this->canModifyWishlist($request)) {
                try {
                    if (!$this->itemsOfWishlistRepositoryWordpress->deleteByIdAndWishlistId($itemId, $wishlistId)) {
                        $messages[] = new BulkActionMessage(
                            BulkActionMessage::TYPE_ERROR,
                            $itemId,
                            $itemOfWishlist->title ?? "",
                            "unable_to_delete_resource",
                            __("Not Found", 'wc-wishlist')
                        );
                        continue;
                    }
                } catch (Exception $e) {
                    $messages[] = new BulkActionMessage(
                        BulkActionMessage::TYPE_ERROR,
                        $itemId,
                        $itemOfWishlist->title ?? "",
                        "unable_to_delete_resource",
                        $e->getMessage()
                    );
                    continue;
                }
            }

            if ($action->withWcNotices) {
                foreach ( $messages as $message ) {
                    wc_add_notice($message->message, $message->type);
                }
            }
        }

        try {
            $list = $this->getExtendedItemsOfWishlistByWishlistId($wishlistId);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        return new WP_REST_Response(
            new ExecuteActionsOnItemsOfWishlistResponse(
                $messages,
                $list
            ), WP_Http::OK
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

    /** @see WC_Product_Variation_Data_Store_CPT::generate_product_title */
    protected function generateProductTitle($product): string
    {
        $attributes = (array)$product->get_attributes();

        // Do not include attributes if the product has 3+ attributes.
        $shouldIncludeAttributes = count($attributes) < 3;

        // Do not include attributes if an attribute name has 2+ words and the
        // product has multiple attributes.
        if ($shouldIncludeAttributes && 1 < count($attributes)) {
            foreach ($attributes as $name => $value) {
                if (false !== strpos($name, '-')) {
                    $shouldIncludeAttributes = false;
                    break;
                }
            }
        }

        $shouldIncludeAttributes = apply_filters(
            'woocommerce_product_variation_title_include_attributes',
            $shouldIncludeAttributes,
            $product
        );
        $separator = apply_filters('woocommerce_product_variation_title_attributes_separator', ' - ', $product);
        $titleBase = get_post_field('post_title', $product->get_parent_id());
        $titleSuffix = $shouldIncludeAttributes ? wc_get_formatted_variation($product, true, false) : '';

        return apply_filters(
            'woocommerce_product_variation_title',
            $titleSuffix ? $titleBase . $separator . $titleSuffix : $titleBase,
            $product,
            $titleBase,
            $titleSuffix
        );
    }

    /**
     *
     * Converts variation data to format that is needed for item to get added by WC CartController.
     * Attribute keys get replaced with names from WC attribute objects, that way both global and custom product
     * attributes get validated successfully by CartController.
     *
     * @param ExtendedItemOfWishlist $itemOfWishlist
     * @return array
     */
    protected function convertVariationData(ExtendedItemOfWishlist $itemOfWishlist): array
    {
        $variationData = array();
        if (!empty($itemOfWishlist->variation)) {
            $product = wc_get_product($itemOfWishlist->productId);
            $parentProduct = wc_get_product($product->get_parent_id());
            $variableProductAttributes = $parentProduct->get_attributes();
            foreach ($itemOfWishlist->variation as $attr => $value) {
                $newAttr = $attr;
                $pos = strpos($attr, 'attribute_');
                if ($pos !== false) {
                    $newAttr = substr_replace($attr, '', $pos, strlen('attribute_'));
                }
                if (isset($variableProductAttributes[$newAttr])) {
                    $newAttr = $variableProductAttributes[$newAttr]['name'];
                }
                $variationData[] = array(
                    'attribute' => $newAttr,
                    'value' => $value,
                );
            }
        }

        return $variationData;
    }

    /** @see \WC_Product::is_purchasable() */
    protected static function productIsPurchasable(\WC_Product $product): bool
    {
        return $product->exists() && ('publish' === $product->get_status()) && '' !== $product->get_price('edit');
    }

    /**
     * @return ExtendedItemOfWishlist[]
     * @throws Exception
     */
    protected function getExtendedItemsOfWishlistByWishlistId(int $wishlistId): array
    {
        $results = $this->itemsOfWishlistRepositoryWordpress->getExtendedItemsOfWishlistOrderByPriority($wishlistId);

        $list = [];
        foreach ($results as $result) {
            $id = (int)$result->id;
            $productId = (int)$result->productId;
            $parentId = (int)$result->parentId;
            $product = wc_get_product($productId);
            if (!$product || !self::productIsPurchasable($product)) {
                try {
                    $this->itemsOfWishlistRepositoryWordpress->deleteById($id);
                } catch (Exception $e) {
                }

                continue;
            }

            $variation = json_decode($result->variation, true);
            $variation = is_array($variation) ? $variation : [];
            if ($product instanceof \WC_Product_Variation) {
                $attributes = $product->get_attributes('edit');
                $product->set_attributes(array_merge($attributes, $variation));
                $product->set_name($this->generateProductTitle($product));
            }

            $quantity = (int)$result->quantity;
            $priority = (int)$result->priority;

            $availability = $product->get_availability();

            /** It is not always an url ¯\_(ツ)_/¯  */
            $addToCartUrl = $product->add_to_cart_url();
            if (str_starts_with($addToCartUrl, "?")) {
                $addToCartUrl = $product->get_permalink() . $addToCartUrl;
            }

            $list[] = new ExtendedItemOfWishlist(
                $id,
                $productId,
                $parentId,
                $product->get_name("edit"),
                $product->get_type(),
                $product->is_virtual(),
                $product->is_downloadable(),
                $product->get_sku("edit"),
                $quantity,
                $priority,
                $variation,
                json_decode($result->cartItemData, true),
                Date::fromDateTime(
                    \DateTime::createFromFormat(
                        "Y-m-d H:i:s",
                        $result->createdAt,
                        new \DateTimeZone('UTC')
                    )
                ),
                $product->get_permalink(),
                new \AlgolWishlist\API\DTO\ItemOfWishlist\Thumbnail(
                    get_the_post_thumbnail_url($productId) ?: get_the_post_thumbnail_url($parentId)
                ),
                new \AlgolWishlist\API\DTO\ItemOfWishlist\Stock(
                    new ItemOfWishlist\StockAvailability(
                        $availability['availability'] ?? null,
                        $availability['class'] ?? null
                    )
                ),
                new \AlgolWishlist\API\DTO\ItemOfWishlist\Price(
                    (float)$result->unitPrice,
                    $result->unitPrice,
                    (float)$result->unitPrice,
                    $result->maxPrice,
                    (float)$result->maxPrice,
                    $product->get_price_html()
                ),
                new ItemOfWishlist\AddToCart(
                    $product->add_to_cart_text(),
                    $addToCartUrl,
                    !str_contains($addToCartUrl, "add-to-cart=")
                )
            );
        }

        return $list;
    }
}
