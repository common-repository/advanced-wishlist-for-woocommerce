<?php

namespace AlgolWishlist\API\Controllers;

use AlgolWishlist\API\DTO\AdminGetWishlistsElement;
use AlgolWishlist\API\DTO\AdminGetWishlistsResponse;
use AlgolWishlist\API\DTO\Date;
use AlgolWishlist\API\DTO\Error;
use AlgolWishlist\API\DTO\User;
use AlgolWishlist\API\DTO\Wishlist;
use AlgolWishlist\API\DTO\WishlistAdminRequest;
use AlgolWishlist\Repositories\ItemsOfWishlist\ItemsOfWishlistRepositoryWordpress;
use AlgolWishlist\Repositories\OrderByModifier;
use AlgolWishlist\Repositories\PreparedPagination;
use AlgolWishlist\Repositories\Wishlists\ShareTypeEnum;
use AlgolWishlist\Repositories\Wishlists\WishlistEntity;
use AlgolWishlist\Repositories\Wishlists\WishlistsOrderBy;
use AlgolWishlist\Repositories\Wishlists\WishlistsRepositoryWordpress;
use AlgolWishlist\Repositories\Wishlists\WishlistTokenGenerator;
use DateTime;
use DateTimeZone;
use Exception;
use OpenApi\Annotations as OA;
use WP_Http;
use WP_REST_Request;
use WP_REST_Response;

class AdminWishlists
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
        $this->namespace = 'algol-wishlist/v1/admin';
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
                'permission_callback' => [$this, 'canManageWoocommerce'],
            ],
            [
                'methods' => "DELETE",
                'callback' => array($this, 'deleteItem'),
                'permission_callback' => [$this, 'canManageWoocommerce'],
            ],
        ));

        register_rest_route($this->namespace, '/' . $this->resourceName, [
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
        ]);
    }

    public function canManageWoocommerce(): bool
    {
        return current_user_can('manage_woocommerce');
    }

    /**
     * @OA\Post(
     *     path="/admin/wishlists",
     *     tags={"Admin Wishlists"},
     *     description="Add a new wishlist as admin",
     *     operationId="adminAddWishlist",
     *     @OA\RequestBody(
     *         description="Wishlist object that needs to be added",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/WishlistAdminRequest"),
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
        $wishlistRequest = WishlistAdminRequest::fromBody($request->get_json_params());
        if (!$wishlistRequest->isValidForCreation()) {
            return new WP_REST_Response(null, WP_Http::BAD_REQUEST);
        }

        try {
            $wishlist = $this->wishlistRepository->create(
                new WishlistEntity(
                    0,
                    $wishlistRequest->title,
                    (new WishlistTokenGenerator())->generate(),
                    $wishlistRequest->ownerId,
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
     *     path="/admin/wishlists/{wishlistId}",
     *     tags={"Admin Wishlists"},
     *     description="Returns a single wishlist for admin",
     *     operationId="adminGetWishlistById",
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

        try {
            $wishlist = $this->wishlistRepository->getById($id);
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
     *     path="/admin/wishlists/{wishlistId}",
     *     tags={"Admin Wishlists"},
     *     description="Delete the wishlist as admin",
     *     operationId="adminDeleteWishlist",
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
            if (!$this->wishlistRepository->deleteById($id)) {
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
     *     path="/admin/wishlists",
     *     tags={"Admin Wishlists"},
     *     description="Update the wishlist as admin",
     *     operationId="adminUpdateWishlist",
     *     @OA\RequestBody(
     *         description="Wishlist object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/WishlistAdminRequest"),
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
        $wishlistRequest = WishlistAdminRequest::fromBody($request->get_json_params());
        if ($wishlistRequest->id === null) {
            return new WP_REST_Response(null, WP_Http::BAD_REQUEST);
        }

        try {
            $wishlist = $this->wishlistRepository->getById($wishlistRequest->id);
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        if ($wishlist === null) {
            return new WP_REST_Response(null, WP_Http::NOT_FOUND);
        }

        if ($wishlistRequest->title !== null) {
            $wishlist->title = $wishlistRequest->title;
        }

        if ($wishlistRequest->ownerId !== null) {
            $wishlist->ownerId = $wishlistRequest->ownerId;
        }

        if ($wishlistRequest->shareTypeId !== null) {
            try {
                $wishlist->shareType = new ShareTypeEnum($wishlistRequest->shareTypeId);
            } catch (Exception $e) {
            }
        }

        try {
            $result = $this->wishlistRepository->update($wishlist);
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
     *     path="/admin/wishlists",
     *     tags={"Admin Wishlists"},
     *     description="Get all wishlists for admin",
     *     operationId="adminGetAllWishlists",
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         description="Sort field",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort modifier: DESC or ASC",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         description="Maximum number of items to be returned in result set",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/AdminGetWishlistsResponse"),
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
        $sortField = $request->get_param("sortField");
        $sort = $request->get_param("sort");
        $page = $request->get_param("page");
        $itemsPerPage = $request->get_param("itemsPerPage");

        $ordination = new WishlistsOrderBy();
        if ($sortField && $sort) {
            try {
                $ordination->add($sortField, new OrderByModifier(strtoupper($sort)));
            } catch (Exception $e) {
                return new WP_REST_Response(
                    new Error("unable_to_get_resource", $e->getMessage()),
                    WP_Http::INTERNAL_SERVER_ERROR
                );
            }
        }

        if (!is_numeric($itemsPerPage)) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", "Incorrect 'itemsPerPage' parameter"),
                WP_Http::BAD_REQUEST
            );
        }
        if (!is_numeric($page) || $page < 1) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", "Incorrect 'page' parameter"),
                WP_Http::BAD_REQUEST
            );
        }
        $pagination = new PreparedPagination($itemsPerPage, $page);

        try {
            $results = $this->wishlistRepository->getList($ordination, $pagination);
            $total = $this->wishlistRepository->getLastFoundRowsCount();
        } catch (Exception $e) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", $e->getMessage()),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        /** @var array<int, AdminGetWishlistsElement> $list */
        $list = [];
        foreach ($results as $result) {
            $list[] = new AdminGetWishlistsElement(
                (int)$result->id,
                $result->name,
                $result->token,
                new User($result->ownerId, $result->username),
                (int)$result->shareType,
                Date::fromDateTime(
                    \DateTime::createFromFormat(
                        "Y-m-d H:i:s",
                        $result->dateCreated,
                        new \DateTimeZone('UTC')
                    )
                ),
                (int)$result->itemsCount
            );
        }

        return new WP_REST_Response(new AdminGetWishlistsResponse($list, $total), WP_Http::OK);
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
