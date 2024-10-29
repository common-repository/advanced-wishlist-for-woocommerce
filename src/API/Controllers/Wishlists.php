<?php

namespace AlgolWishlist\API\Controllers;

use AlgolWishlist\API\DTO\Date;
use AlgolWishlist\API\DTO\Error;
use AlgolWishlist\API\DTO\User;
use AlgolWishlist\API\DTO\Wishlist;
use AlgolWishlist\API\DTO\WishlistRequest;
use AlgolWishlist\Repositories\ItemsOfWishlist\ItemsOfWishlistRepositoryWordpress;
use AlgolWishlist\Repositories\Wishlists\ShareTypeEnum;
use AlgolWishlist\Repositories\Wishlists\WishlistEntity;
use AlgolWishlist\Repositories\Wishlists\WishlistsRepositoryWordpress;
use AlgolWishlist\Repositories\Wishlists\WishlistTokenGenerator;
use DateTime;
use DateTimeZone;
use Exception;
use OpenApi\Annotations as OA;
use WP_Http;
use WP_REST_Request;
use WP_REST_Response;

class Wishlists
{
    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $resourceName;

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
        $this->resourceName = 'wishlists';

        global $wpdb;
        $this->wishlistRepository = new WishlistsRepositoryWordpress($wpdb);
        $this->itemsOfWishlistRepositoryWordpress = new ItemsOfWishlistRepositoryWordpress($wpdb);
    }

    public function registerRoutes()
    {
        register_rest_route($this->namespace, '/' . $this->resourceName . '/(?P<wishlistId>\d+)', array(
            [
                'methods' => "GET",
                'callback' => array($this, 'getItem'),
                'permission_callback' => [$this, 'permissionCallback'],
            ],
            [
                'methods' => "DELETE",
                'callback' => array($this, 'deleteItem'),
                'permission_callback' => [$this, 'permissionCallback'],
            ],
        ));

        register_rest_route($this->namespace, '/' . $this->resourceName, [
            [
                'methods' => "GET",
                'callback' => array($this, 'getItems'),
                'permission_callback' => [$this, 'permissionCallback'],
            ],
            [
                'methods' => "POST",
                'callback' => array($this, 'createItem'),
                'permission_callback' => [$this, 'permissionCallback'],
            ],
            [
                'methods' => "PUT",
                'callback' => array($this, 'updateItem'),
                'permission_callback' => [$this, 'permissionCallback'],
            ],
        ]);
    }

    public function permissionCallback(): bool
    {
        return is_user_logged_in();
    }

    /**
     * @OA\Post(
     *     path="/wishlists",
     *     tags={"Wishlists"},
     *     description="Add a new wishlist",
     *     operationId="addWishlist",
     *     @OA\RequestBody(
     *         description="Wishlist object that needs to be added",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/WishlistRequest"),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Wishlist"),
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
        $wishlistRequest = WishlistRequest::fromBody($request->get_json_params());
        if (!$wishlistRequest->isValidForCreation()) {
            return new WP_REST_Response(null, WP_Http::BAD_REQUEST);
        }

        $ownerId = get_current_user_id();

        try {
            $wishlist = $this->wishlistRepository->create(
                new WishlistEntity(
                    0,
                    $wishlistRequest->title,
                    (new WishlistTokenGenerator())->generate(),
                    $ownerId,
                    null,
                    new ShareTypeEnum($wishlistRequest->shareTypeId),
                    new DateTime('now', new DateTimeZone('UTC'))
                )
            );
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_create_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        if ($wishlist === null) {
            return new WP_REST_Response(null, WP_Http::NOT_FOUND);
        }

        return new WP_REST_Response(self::convertWishlistEntityToDto($wishlist), WP_Http::CREATED);
    }

    /**
     * @OA\Get(
     *     path="/wishlists/{wishlistId}",
     *     tags={"Wishlists"},
     *     description="Returns a single wishlist",
     *     operationId="getWishlistById",
     *     @OA\Parameter(
     *         name="wishlistId",
     *         in="path",
     *         description="ID of wishlist to return",
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
     *              ref="#/components/schemas/Wishlist"
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
        $id = $request->get_param("wishlistId");

        if (!$id) {
            return new WP_REST_Response(null, WP_Http::BAD_REQUEST);
        }

        $ownerId = get_current_user_id();

        try {
            $wishlist = $this->wishlistRepository->getByIdAndOwnerId($id, $ownerId);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        if ($wishlist === null) {
            return new WP_REST_Response(null, WP_Http::NOT_FOUND);
        }

        return new WP_REST_Response(self::convertWishlistEntityToDto($wishlist), WP_Http::OK);
    }

    /**
     * @OA\Delete(
     *     path="/wishlists/{wishlistId}",
     *     tags={"Wishlists"},
     *     description="Delete the wishlist",
     *     operationId="deleteWishlist",
     *     @OA\Parameter(
     *         name="wishlistId",
     *         in="path",
     *         description="ID of wishlist to return",
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
        $id = $request->get_param("wishlistId");
        if (!$id) {
            return new WP_REST_Response(null, WP_Http::BAD_REQUEST);
        }

        try {
            if (!$this->wishlistRepository->deleteByIdAndOwnerId($id, get_current_user_id())) {
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
     *     path="/wishlists",
     *     tags={"Wishlists"},
     *     description="Update the wishlist",
     *     operationId="updateWishlist",
     *     @OA\RequestBody(
     *         description="Wishlist object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/WishlistRequest"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Wishlist"),
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
        $wishlistRequest = WishlistRequest::fromBody($request->get_json_params());
        if ($wishlistRequest->id === null) {
            return new WP_REST_Response(null, WP_Http::BAD_REQUEST);
        }

        $ownerId = get_current_user_id();

        try {
            $wishlist = $this->wishlistRepository->getByIdAndOwnerId($wishlistRequest->id, $ownerId);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        if ($wishlist === null) {
            return new WP_REST_Response(null, WP_Http::NOT_FOUND);
        }

        if ( $wishlistRequest->title !== null ) {
            $wishlist->title = $wishlistRequest->title;
        }

        if ( $wishlistRequest->shareTypeId !== null ) {
            try {
                $wishlist->shareType = new ShareTypeEnum($wishlistRequest->shareTypeId);
            } catch ( Exception $e ) {

            }
        }

        try {
            $result = $this->wishlistRepository->updateByIdAndOwnerId($wishlist);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_update_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        if ($result === null) {
            return new WP_REST_Response(null, WP_Http::NOT_FOUND);
        }

        return new WP_REST_Response(self::convertWishlistEntityToDto($wishlist), WP_Http::OK);
    }

    /**
     * @OA\Get(
     *     path="/wishlists",
     *     tags={"Wishlists"},
     *     description="Get all wishlists",
     *     operationId="getAllWishlists",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Wishlist")),
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
        $ownerId = get_current_user_id();

        try {
            $wishlists = $this->wishlistRepository->getAllByOwnerId($ownerId);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        return new WP_REST_Response(array_map([$this, 'convertWishlistEntityToDto'], $wishlists), WP_Http::OK);
    }

    protected static function convertWishlistEntityToDto(WishlistEntity $wishlistEntity): Wishlist
    {
        $user = get_userdata($wishlistEntity->ownerId);

        return new Wishlist(
            $wishlistEntity->id,
            $wishlistEntity->title,
            $wishlistEntity->token,
            new User($wishlistEntity->ownerId, $user ? $user->nickname : $wishlistEntity->ownerId),
            $wishlistEntity->shareType->getValue(),
            Date::fromDateTime($wishlistEntity->createdAt)
        );
    }
}
