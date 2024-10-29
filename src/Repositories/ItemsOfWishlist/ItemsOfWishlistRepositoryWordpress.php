<?php

namespace AlgolWishlist\Repositories\ItemsOfWishlist;

use AlgolWishlist\Repositories\PreparedOrdination;
use AlgolWishlist\Repositories\PreparedPagination;
use AlgolWishlist\Repositories\Wishlists\ShareTypeEnum;
use AlgolWishlist\Repositories\Wishlists\WishlistsRepositoryWordpress;
use AlgolWishlist\Repositories\WordpressRepository;
use Exception;

class ItemsOfWishlistRepositoryWordpress extends WordpressRepository
{
    protected const TABLE_NAME = "wishlists_and_products";

    /**
     * @var \wpdb
     */
    protected $wpDb;

    /**
     * @var WishlistsRepositoryWordpress
     */
    protected $wishlistRepository;

    /**
     * @var int
     */
    protected $lastFoundRowsCount;

    public function __construct(\wpdb $wpDb)
    {
        parent::__construct($wpDb);

        $this->wishlistRepository = new WishlistsRepositoryWordpress($wpDb);
        $this->lastFoundRowsCount = 0;
    }

    public function getTableName(): string
    {
        return $this->wpDb->prefix . "algol_wishlist_" . self::TABLE_NAME;
    }

    private function mapQueryResultsToItemOfWishlistEntity($result): ?ItemOfWishlistEntity
    {
        return new ItemOfWishlistEntity(
            (int)$result->id,
            (int)$result->wishlistId,
            (int)$result->productId,
            (float)$result->quantity,
            json_decode($result->variation, true),
            json_decode($result->cartItemData, true),
            \DateTime::createFromFormat(
                "Y-m-d H:i:s",
                $result->createdAt,
                new \DateTimeZone('UTC')
            ),
            (int)$result->priority
        );
    }

    /**
     * @throws Exception
     */
    public function create(ItemOfWishlistEntity $itemOfWishlistEntity): ?ItemOfWishlistEntity
    {
        $result = $this->dbRequestErrorsWrapper(function () use ($itemOfWishlistEntity) {
            return $this->wpDb->insert($this->getTableName(), [
                "wishlistId" => $itemOfWishlistEntity->wishlistId,
                "productId" => $itemOfWishlistEntity->productId,
                "quantity" => $itemOfWishlistEntity->quantity,
                "variation" => json_encode($itemOfWishlistEntity->variation),
                "cartItemData" => json_encode($itemOfWishlistEntity->cartItemData),
                "createdAt" => $itemOfWishlistEntity->createdAt->format("Y-m-d H:i:s"),
                "priority" => $itemOfWishlistEntity->priority,
            ]);
        });

        if ($result === false) {
            return null;
        }

        return $this->getById($this->wpDb->insert_id);
    }

    /**
     * @throws Exception
     */
    public function update(ItemOfWishlistEntity $itemOfWishlistEntity): ?ItemOfWishlistEntity
    {
        $result = $this->dbRequestErrorsWrapper(function () use ($itemOfWishlistEntity) {
            return $this->wpDb->update(
                $this->getTableName(),
                [
                    "wishlistId" => $itemOfWishlistEntity->wishlistId,
                    "productId" => $itemOfWishlistEntity->productId,
                    "quantity" => $itemOfWishlistEntity->quantity,
                    "variation" => json_encode($itemOfWishlistEntity->variation),
                    "cartItemData" => json_encode($itemOfWishlistEntity->cartItemData),
                    "createdAt" => $itemOfWishlistEntity->createdAt->format("Y-m-d H:i:s"),
                    "priority" => $itemOfWishlistEntity->priority,
                ],
                ["id" => $itemOfWishlistEntity->id]
            );
        });

        if ($result === false) {
            return null;
        }

        return $this->getById($this->wpDb->insert_id);
    }

    /**
     * @throws Exception
     */
    public function getById(int $id): ?ItemOfWishlistEntity
    {
        $result = $this->dbRequestErrorsWrapper(function () use ($id) {
            return $this->wpDb->get_row(
                $this->wpDb->prepare("SELECT * FROM {$this->getTableName()} WHERE id = %d LIMIT 1", $id)
            );
        });

        if ($result === null) {
            return null;
        }

        return $this->mapQueryResultsToItemOfWishlistEntity($result);
    }

    /**
     * @throws Exception
     */
    public function getByIdAndWishlistIdAndOwnerId(int $id, int $wishlistId, int $ownerId): ?ItemOfWishlistEntity
    {
        $result = $this->dbRequestErrorsWrapper(function () use ($id, $wishlistId, $ownerId) {
            $wishlistsTableName = $this->wishlistRepository->getTableName();

            return $this->wpDb->get_row(
                $this->wpDb->prepare(
                    "SELECT {$this->getTableName()}.*
                            FROM {$this->getTableName()}
                            LEFT JOIN {$wishlistsTableName} ON {$this->getTableName()}.wishlistId = {$wishlistsTableName}.id
                            WHERE {$this->getTableName()}.id = %d AND {$wishlistsTableName}.id = %d AND {$wishlistsTableName}.ownerId = %d
                            LIMIT 1",
                    $id,
                    $wishlistId,
                    $ownerId
                )
            );
        });

        if ($result === null) {
            return null;
        }

        return $this->mapQueryResultsToItemOfWishlistEntity($result);
    }

    /**
     * @throws Exception
     */
    public function getByIdAndWishlistIdAndOwnerIdOrPublic(int $id, int $wishlistId, int $ownerId): ?ItemOfWishlistEntity
    {
        $result = $this->dbRequestErrorsWrapper(function () use ($id, $wishlistId, $ownerId) {
            $wishlistsTableName = $this->wishlistRepository->getTableName();
            $publicShareTypeId = ShareTypeEnum::PUBLIC()->getValue();

            return $this->wpDb->get_row(
                $this->wpDb->prepare(
                    "SELECT {$this->getTableName()}.*
                            FROM {$this->getTableName()}
                            LEFT JOIN {$wishlistsTableName} ON {$this->getTableName()}.wishlistId = {$wishlistsTableName}.id
                            WHERE {$this->getTableName()}.id = %d AND {$wishlistsTableName}.id = %d AND ( {$wishlistsTableName}.ownerId = %d OR {$wishlistsTableName}.shareType = %d )
                            LIMIT 1",
                    $id,
                    $wishlistId,
                    $ownerId,
                    $publicShareTypeId
                )
            );
        });

        if ($result === null) {
            return null;
        }

        return $this->mapQueryResultsToItemOfWishlistEntity($result);
    }

    /**
     * @throws Exception
     */
    public function getByIdAndWishlistId(int $id, int $wishlistId): ?ItemOfWishlistEntity
    {
        $result = $this->dbRequestErrorsWrapper(function () use ($id, $wishlistId) {
            $wishlistsTableName = $this->wishlistRepository->getTableName();

            return $this->wpDb->get_row(
                $this->wpDb->prepare(
                    "SELECT {$this->getTableName()}.*
                            FROM {$this->getTableName()}
                            LEFT JOIN {$wishlistsTableName} ON {$this->getTableName()}.wishlistId = {$wishlistsTableName}.id
                            WHERE {$this->getTableName()}.id = %d AND {$wishlistsTableName}.id = %d
                            LIMIT 1",
                    $id,
                    $wishlistId
                )
            );
        });

        if ($result === null) {
            return null;
        }

        return $this->mapQueryResultsToItemOfWishlistEntity($result);
    }

    /**
     * @throws Exception
     */
    public function deleteByIdAndWishlistId(int $id, int $wishlistId): bool
    {
        $result = $this->dbRequestErrorsWrapper(function () use ($id, $wishlistId) {
            $tableName = $this->getTableName();

            return $this->wpDb->query(
                $this->wpDb->prepare(
                    "DELETE {$tableName} FROM {$tableName}
                                WHERE {$tableName}.id = %d AND {$tableName}.wishlistId = %d",
                    $id,
                    $wishlistId
                )
            );
        });

        if ($result === false) {
            return false;
        }

        return (bool)$result;
    }

    /**
     * @param int $wishlistId
     * @param PreparedOrdination|null $ordination
     * @param PreparedPagination|null $pagination
     * @return array<int, ItemOfWishlistEntity>
     * @throws \AlgolWishlist\Enum\Exceptions\UnexpectedValueException
     * @throws \ReflectionException
     * @throws Exception
     */
    public function getAllByWishlistId(
        int $wishlistId,
        ?PreparedOrdination $ordination = null,
        ?PreparedPagination $pagination = null
    ): array {
        $orderBy = "";
        if ($ordination !== null) {
            $orderByColumns = [];
            $columns = ItemOfWishlistEntity::getListOfOrderByColumns();
            foreach ($ordination->getColumnNames() as $columnName) {
                if (in_array($columnName, $columns, true)) {
                    $orderByColumns[] = $columnName;
                }
            }

            if (count($orderByColumns) > 0) {
                $orderBy = sprintf(
                    "ORDER BY %s %s",
                    join(",", $orderByColumns),
                    $ordination->getOrder()->getValue()
                );
            }
        }

        $limit = "";
        if ($pagination !== null) {
            $limit = $this->wpDb->prepare(
                "LIMIT %d, %d",
                ($pagination->getCurrentPage() - 1) * $pagination->getItemsPerPage(),
                $pagination->getItemsPerPage()
            );
        }

        $results = $this->dbRequestErrorsWrapper(function () use ($wishlistId, $orderBy, $limit) {
            return $this->wpDb->get_results(
                $this->wpDb->prepare(
                    "SELECT * FROM {$this->getTableName()} WHERE wishlistId = %d {$orderBy} {$limit}",
                    $wishlistId
                )
            );
        });

        if (!is_array($results)) {
            return [];
        }

        $resultObjects = [];
        foreach ($results as $result) {
            $resultObjects[] = $this->mapQueryResultsToItemOfWishlistEntity($result);
        }

        return $resultObjects;
    }

    /**
     * @param int $productId
     * @param PreparedOrdination|null $ordination
     * @param PreparedPagination|null $pagination
     * @return array<int, ItemOfWishlistEntity>
     * @throws \AlgolWishlist\Enum\Exceptions\UnexpectedValueException
     * @throws \ReflectionException
     * @throws Exception
     */
    public function getAllByProductId(
        int $productId,
        ?PreparedOrdination $ordination = null,
        ?PreparedPagination $pagination = null
    ): array {
        $orderBy = "";
        if ($ordination !== null) {
            $orderByColumns = [];
            $columns = ItemOfWishlistEntity::getListOfOrderByColumns();
            foreach ($ordination->getColumnNames() as $columnName) {
                if (in_array($columnName, $columns, true)) {
                    $orderByColumns[] = $columnName;
                }
            }

            if (count($orderByColumns) > 0) {
                $orderBy = sprintf(
                    "ORDER BY %s %s",
                    join(",", $orderByColumns),
                    $ordination->getOrder()->getValue()
                );
            }
        }

        $limit = "";
        if ($pagination !== null) {
            $limit = $this->wpDb->prepare(
                "LIMIT %d, %d",
                ($pagination->getCurrentPage() - 1) * $pagination->getItemsPerPage(),
                $pagination->getItemsPerPage()
            );
        }

        $results = $this->dbRequestErrorsWrapper(function () use ($productId, $orderBy, $limit) {
            return $this->wpDb->get_results(
                $this->wpDb->prepare(
                    "SELECT * FROM {$this->getTableName()} WHERE productId = %d {$orderBy} {$limit}",
                    $productId
                )
            );
        });

        if (!is_array($results)) {
            return [];
        }

        $resultObjects = [];
        foreach ($results as $result) {
            $resultObjects[] = $this->mapQueryResultsToItemOfWishlistEntity($result);
        }

        return $resultObjects;
    }


    /**
     * @param PreparedOrdination|null $ordination
     * @param PreparedPagination|null $pagination
     * @return array<int, ItemOfWishlistEntity>
     * @throws \AlgolWishlist\Enum\Exceptions\UnexpectedValueException
     * @throws \ReflectionException
     * @throws Exception
     *
     * @example
     * $this->repository->getAll(
     *      new PreparedOrdination(['id'], PreparedOrdinationOrderEnum::ASC()),
     *      new PreparedPagination(10, 1)
     * );
     *
     */
    public function getAll(?PreparedOrdination $ordination = null, ?PreparedPagination $pagination = null): array
    {
        $orderBy = "";
        if ($ordination !== null) {
            $orderByColumns = [];
            $columns = ItemOfWishlistEntity::getListOfOrderByColumns();
            foreach ($ordination->getColumnNames() as $columnName) {
                if (in_array($columnName, $columns, true)) {
                    $orderByColumns[] = $columnName;
                }
            }

            if (count($orderByColumns) > 0) {
                $orderBy = sprintf(
                    "ORDER BY %s %s",
                    join(",", $orderByColumns),
                    $ordination->getOrder()->getValue()
                );
            }
        }

        $limit = "";
        if ($pagination !== null) {
            $limit = $this->wpDb->prepare(
                "LIMIT %d, %d",
                ($pagination->getCurrentPage() - 1) * $pagination->getItemsPerPage(),
                $pagination->getItemsPerPage()
            );
        }

        $results = $this->dbRequestErrorsWrapper(function () use ($orderBy, $limit) {
            return $this->wpDb->get_results(
                "SELECT * FROM {$this->getTableName()} {$orderBy} {$limit}"
            );
        });

        if (!is_array($results)) {
            return [];
        }

        $resultObjects = [];
        foreach ($results as $result) {
            $resultObjects[] = $this->mapQueryResultsToItemOfWishlistEntity($result);
        }

        return $resultObjects;
    }

    /**
     * @throws Exception
     */
    public function getAllByTextQueryAndSinceDate(
        string $query,
        \DateTime $since,
        PopularProductsOrderBy $ordination,
        ?PreparedPagination $pagination = null
    ): array {
        $orderBy = "";
        if ($ordination->getList()) {
            $orderBy = "ORDER BY " . join(",", array_map(function ($item) {
                    return $item['field'] . " " . $item['sort']->getValue();
                }, $ordination->getList()));
        }

        $limit = "";
        if ($pagination !== null) {
            $limit = $this->wpDb->prepare(
                "LIMIT %d, %d",
                ($pagination->getCurrentPage() - 1) * $pagination->getItemsPerPage(),
                $pagination->getItemsPerPage()
            );
        }

        $results = $this->dbRequestErrorsWrapper(function () use ($query, $since, $orderBy, $limit) {
            $productsTableName = $this->wpDb->posts;

            return $this->wpDb->get_results(
                $this->wpDb->prepare(
                    "SELECT SQL_CALC_FOUND_ROWS productId,{$productsTableName}.post_parent as parentId,{$productsTableName}.post_title as name,variation, cartItemData,COUNT(*) as counter
                        FROM {$this->getTableName()}
                            LEFT JOIN {$productsTableName} ON {$this->getTableName()}.productId = {$productsTableName}.ID
                        WHERE createdAt > %s AND ({$productsTableName}.post_title LIKE '%s')
                        GROUP BY {$this->getTableName()}.productId, {$this->getTableName()}.variation, {$this->getTableName()}.cartItemData
                        {$orderBy}
                        {$limit}
                        ",
                    $since->format("Y-m-d H:i:s"),
                    "%$query%"
                )
            );
        });

        $this->lastFoundRowsCount = $this->dbRequestErrorsWrapper(function () {
            return (int)$this->wpDb->get_var("SELECT FOUND_ROWS()");
        });

        if (!is_array($results)) {
            return [];
        }

        return $results;
    }

    /**
     * @throws Exception
     */
    public function getUsersByWishlistedProductId(
        int $productId,
        UsersOfPopularProductOrderBy $ordination,
        ?PreparedPagination $pagination = null
    ): array {
        $orderBy = "";
        if ($ordination->getList()) {
            $orderBy = "ORDER BY " . join(",", array_map(function ($item) {
                    return $item['field'] . " " . $item['sort']->getValue();
                }, $ordination->getList()));
        }

        $limit = "";
        if ($pagination !== null) {
            $limit = $this->wpDb->prepare(
                "LIMIT %d, %d",
                ($pagination->getCurrentPage() - 1) * $pagination->getItemsPerPage(),
                $pagination->getItemsPerPage()
            );
        }

        $results = $this->dbRequestErrorsWrapper(function () use ($productId, $orderBy, $limit) {
            $wishlistsTableName = $this->wishlistRepository->getTableName();
            $usersTableName = $this->wpDb->users;

            return $this->wpDb->get_results(
                $this->wpDb->prepare(
                    "SELECT SQL_CALC_FOUND_ROWS {$wishlistsTableName}.ownerId as userId,{$usersTableName}.display_name as name,{$this->getTableName()}.createdAt as addedOn
                        FROM {$this->getTableName()}
                            LEFT JOIN {$wishlistsTableName} ON {$this->getTableName()}.wishlistId = {$wishlistsTableName}.id
                            LEFT JOIN {$usersTableName} ON {$wishlistsTableName}.ownerId = {$usersTableName}.ID
                        WHERE {$this->getTableName()}.productId = %s
                        GROUP BY userId
                        {$orderBy}
                        {$limit}
                        ",
                    $productId
                )
            );
        });

        $this->lastFoundRowsCount = $this->dbRequestErrorsWrapper(function () {
            return (int)$this->wpDb->get_var("SELECT FOUND_ROWS()");
        });

        if (!is_array($results)) {
            return [];
        }

        return $results;
    }

    /**
     * @throws Exception
     */
    public function getUsersByWishlistedProductIdAndWishlistedVariation(
        int $productId,
        array $variation
    ): array {
        $results = $this->dbRequestErrorsWrapper(function () use ($productId, $variation) {
            $wishlistsTableName = $this->wishlistRepository->getTableName();
            $usersTableName = $this->wpDb->users;

            return $this->wpDb->get_results(
                $this->wpDb->prepare(
                    "SELECT {$wishlistsTableName}.ownerId as userId,
                               {$usersTableName}.display_name as name,
                               {$this->getTableName()}.createdAt as addedOn
                        FROM {$this->getTableName()}
                            LEFT JOIN {$wishlistsTableName} ON {$this->getTableName()}.wishlistId = {$wishlistsTableName}.id
                            LEFT JOIN {$usersTableName} ON {$wishlistsTableName}.ownerId = {$usersTableName}.ID
                        WHERE {$this->getTableName()}.productId = %s AND {$this->getTableName()}.variation = %s
                        GROUP BY userId
                        ",
                    $productId, json_encode($variation)
                )
            );
        });

        if (!is_array($results)) {
            return [];
        }

        return $results;
    }

    public function getExtendedItemsOfWishlistOrderByPriority(int $wishlistId): array
    {
        $productsTableName = $this->wpDb->posts;
        $productMetaLookupTableName = $this->wpDb->wc_product_meta_lookup;
        $wishlistsTableName = $this->wishlistRepository->getTableName();

        $showErrors              = $this->wpDb->show_errors;
        $this->wpDb->show_errors = false;
        $results = $this->wpDb->get_results(
            $this->wpDb->prepare(
                "SELECT {$this->getTableName()}.id as id,
                               {$this->getTableName()}.createdAt as createdAt,
                               {$this->getTableName()}.quantity as quantity,
                               {$this->getTableName()}.priority as priority,
                                productId,
                               {$productsTableName}.post_parent as parentId,
                               {$productsTableName}.post_title as title,
                               variation,
                               cartItemData,
                               {$productMetaLookupTableName}.min_price as unitPrice,
                               {$productMetaLookupTableName}.max_price as maxPrice,
                               {$productMetaLookupTableName}.stock_status as stockStatus,
                               {$productMetaLookupTableName}.stock_quantity as stockQuantity,
                               {$productMetaLookupTableName}.virtual as virtualProduct,
                               {$productMetaLookupTableName}.downloadable as downloadable,
                               {$productMetaLookupTableName}.sku as sku
                        FROM {$this->getTableName()}
                            LEFT JOIN {$wishlistsTableName} ON {$this->getTableName()}.wishlistId = {$wishlistsTableName}.id
                            LEFT JOIN {$productsTableName} ON {$this->getTableName()}.productId = {$productsTableName}.ID
                            LEFT JOIN {$productMetaLookupTableName} ON {$this->getTableName()}.productId = {$productMetaLookupTableName}.product_id
                        WHERE {$this->getTableName()}.wishlistId = %d
                        ORDER BY {$this->getTableName()}.priority ASC
                        ",
                $wishlistId
            )
        );

        if ($this->wpDb->last_error) {
            throw new Exception($this->wpDb->last_error);
        }

        $this->wpDb->show_errors = $showErrors;

        if (!is_array($results)) {
            return [];
        }

        return $results;
    }


    public function reorderProductsByWishlistId(array $newOrdination)
    {
        $showErrors              = $this->wpDb->show_errors;
        $this->wpDb->show_errors = false;
        $this->wpDb->query("START TRANSACTION");
        foreach ($newOrdination as $ordinationItem) {
            $id       = $ordinationItem['id'] ?? null;
            $priority = $ordinationItem['priority'] ?? null;

            if ($id === null || $priority === null) {
                continue;
            }

            $this->wpDb->query(
                $this->wpDb->prepare(
                    "UPDATE {$this->getTableName()} SET priority=%d WHERE id=%d",
                    (int)$priority,
                    (int)$id
                )
            );
        }
        $this->wpDb->query("COMMIT");

        if ($this->wpDb->last_error) {
            throw new Exception($this->wpDb->last_error);
        }

        if ($this->wpDb->last_error) {
            throw new Exception($this->wpDb->last_error);
        }

        $this->wpDb->show_errors = $showErrors;
    }

    /**
     * @return int
     */
    public function getLastFoundRowsCount(): int
    {
        return $this->lastFoundRowsCount;
    }


    public function createTable()
    {
        $charsetCollate = $this->wpDb->get_charset_collate();

        $tableName = $this->getTableName();
        $wishlistTableName = $this->wishlistRepository->getTableName();
        $productsTableName = $this->wpDb->posts;

        if ($this->wpDb->get_var("SHOW TABLES LIKE '$tableName'") === $tableName) {
            $this->wpDb->query(
                "ALTER TABLE {$tableName} DROP FOREIGN KEY FKwishlistId;"
            );
            $this->wpDb->query(
                "ALTER TABLE {$tableName} DROP FOREIGN KEY FKproductId;"
            );
            $this->wpDb->query(
                "ALTER TABLE {$tableName} DROP CONSTRAINT WP;"
            );
        }

        $sql = /** @lang MySQL */
            "CREATE TABLE {$tableName} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            quantity DOUBLE UNSIGNED NOT NULL,
            wishlistId BIGINT UNSIGNED NOT NULL,
            productId BIGINT UNSIGNED NOT NULL,
            variation VARCHAR(300) NOT NULL,
            cartItemData VARCHAR(300) NOT NULL,
            createdAt DATETIME NOT NULL,
            priority BIGINT UNSIGNED NOT NULL,
            PRIMARY KEY (id)
        ) $charsetCollate;";
        dbDelta($sql);

        $this->wpDb->query(
            "ALTER TABLE {$tableName}
                    ADD CONSTRAINT FKwishlistId FOREIGN KEY (wishlistId)
                    REFERENCES $wishlistTableName(id) ON DELETE CASCADE ON UPDATE CASCADE;"
        );
        $this->wpDb->query(
            "ALTER TABLE {$tableName}
                    ADD CONSTRAINT FKproductId FOREIGN KEY (productId)
                    REFERENCES $productsTableName(id) ON DELETE CASCADE ON UPDATE CASCADE;"
        );
        $this->wpDb->query(
            "ALTER TABLE {$tableName}
                    ADD CONSTRAINT WP UNIQUE (wishlistId, productId, variation, cartItemData)"
        );
    }

    public function deleteTable()
    {
        $tableName = $this->getTableName();
        $this->wpDb->query("DROP TABLE IF EXISTS $tableName");
    }
}
