<?php

namespace AlgolWishlist\Repositories\Wishlists;

use AlgolWishlist\Repositories\ItemsOfWishlist\ItemsOfWishlistRepositoryWordpress;
use AlgolWishlist\Repositories\PreparedOrdination;
use AlgolWishlist\Repositories\PreparedPagination;
use AlgolWishlist\Repositories\WordpressRepository;
use AlgolWishlist\User\GuestMetaData;
use AlgolWishlist\User\UserMetaData;
use Exception;

class WishlistsRepositoryWordpress extends WordpressRepository
{
    protected const TABLE_NAME = "wishlists";

    /**
     * @var \wpdb
     */
    protected $wpDb;

    /**
     * @var int
     */
    protected $lastFoundRowsCount;

    public function __construct(\wpdb $wpDb)
    {
        parent::__construct($wpDb);

        $this->lastFoundRowsCount = 0;
    }

    public function getTableName(): string
    {
        return $this->wpDb->prefix . "algol_wishlist_" . self::TABLE_NAME;
    }

    private function mapQueryResultsToWishlistEntity($result): WishlistEntity
    {
        return new WishlistEntity(
            (int)$result->id,
            $result->title,
            $result->token,
            $result->ownerId !== null ? (int)$result->ownerId : null,
            $result->sessionKey !== null ? (string)$result->sessionKey : null,
            new ShareTypeEnum((int)$result->shareType),
            \DateTime::createFromFormat(
                "Y-m-d H:i:s",
                $result->createdAt,
                new \DateTimeZone('UTC')
            )
        );
    }

    /**
     * @throws Exception
     */
    public function create(WishlistEntity $wishlist): ?WishlistEntity
    {
        $result = $this->dbRequestErrorsWrapper(function () use ($wishlist) {
            return $this->wpDb->insert($this->getTableName(), [
                "title" => $wishlist->title,
                "token" => $wishlist->token,
                "ownerId" => $wishlist->ownerId,
                "sessionKey" => $wishlist->sessionKey,
                "shareType" => $wishlist->shareType->getValue(),
                "createdAt" => $wishlist->createdAt->format("Y-m-d H:i:s"),
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
    public function update(WishlistEntity $wishlist): ?WishlistEntity
    {
        $result = $this->dbRequestErrorsWrapper(function () use ($wishlist) {
            return $this->wpDb->update(
                $this->getTableName(),
                [
                    "title" => $wishlist->title,
                    "token" => $wishlist->token,
                    "ownerId" => $wishlist->ownerId,
                    "sessionKey" => $wishlist->sessionKey,
                    "shareType" => $wishlist->shareType->getValue(),
                    "createdAt" => $wishlist->createdAt->format("Y-m-d H:i:s"),
                ],
                ["id" => $wishlist->id]
            );
        });

        if ($result === false) {
            return null;
        }

        return $this->getById($this->wpDb->insert_id);
    }

    public function updateByIdAndOwnerId(WishlistEntity $wishlist): ?WishlistEntity
    {
        $showErrors = $this->wpDb->show_errors;
        $this->wpDb->show_errors = false;
        $result = $this->wpDb->update(
            $this->getTableName(),
            [
                "title" => $wishlist->title,
                "token" => $wishlist->token,
                "shareType" => $wishlist->shareType->getValue(),
                "createdAt" => $wishlist->createdAt->format("Y-m-d H:i:s"),
            ],
            ["id" => $wishlist->id, "ownerId" => $wishlist->ownerId]
        );
        $this->wpDb->show_errors = $showErrors;

        if ($this->wpDb->last_error) {
            throw new Exception($this->wpDb->last_error);
        }

        if ($result === false) {
            return null;
        }

        return $wishlist;
    }

    /**
     * @throws Exception
     */
    public function getById(int $id): ?WishlistEntity
    {
        $result = $this->dbRequestErrorsWrapper(function () use ($id) {
            return $this->wpDb->get_row(
                $this->wpDb->prepare("SELECT * FROM {$this->getTableName()} WHERE id = %d LIMIT 1", $id)
            );
        });

        if ($result === null) {
            return null;
        }

        return $this->mapQueryResultsToWishlistEntity($result);
    }

    /**
     * @throws Exception
     */
    public function getFirstBySessionKey(string $sessionKey): ?WishlistEntity
    {
        $result = $this->dbRequestErrorsWrapper(function () use ($sessionKey) {
            return $this->wpDb->get_row(
                $this->wpDb->prepare("SELECT * FROM {$this->getTableName()} WHERE sessionKey = %s LIMIT 1", $sessionKey)
            );
        });

        if ($result === null) {
            return null;
        }

        return $this->mapQueryResultsToWishlistEntity($result);
    }

    /**
     * @throws Exception
     */
    public function getByIdAndOwnerId(int $id, int $ownerId): ?WishlistEntity
    {
        $result = $this->dbRequestErrorsWrapper(function () use ($id, $ownerId) {
            return $this->wpDb->get_row(
                $this->wpDb->prepare(
                    "SELECT * FROM {$this->getTableName()} WHERE id = %d AND ownerId = %d LIMIT 1",
                    $id,
                    $ownerId
                )
            );
        });

        if ($result === null) {
            return null;
        }

        return $this->mapQueryResultsToWishlistEntity($result);
    }

    /**
     * @throws Exception
     */
    public function getByToken(string $token): ?WishlistEntity
    {
        $result = $this->dbRequestErrorsWrapper(function () use ($token) {
            return $this->wpDb->get_row(
                $this->wpDb->prepare("SELECT * FROM {$this->getTableName()} WHERE token = %s LIMIT 1", $token)
            );
        });

        if ($result === null) {
            return null;
        }

        return $this->mapQueryResultsToWishlistEntity($result);
    }

    /**
     * @throws Exception
     */
    public function getFirstByOwnerId(int $ownerId): ?WishlistEntity
    {
        $result = $this->dbRequestErrorsWrapper(function () use ($ownerId) {
            return $this->wpDb->get_row(
                $this->wpDb->prepare("SELECT * FROM {$this->getTableName()} WHERE ownerId = %d LIMIT 1", $ownerId)
            );
        });

        if ($result === null) {
            return null;
        }

        return $this->mapQueryResultsToWishlistEntity($result);
    }

    /**
     * @throws Exception
     */
    public function deleteByIdAndOwnerId(int $id, int $ownerId): bool
    {
        $result = $this->dbRequestErrorsWrapper(function () use ($id, $ownerId) {
            return $this->wpDb->delete(
                $this->getTableName(),
                ["id" => $id, "ownerId" => $ownerId]
            );
        });

        if ($result === false) {
            return false;
        }

        return (bool)$result;
    }

    /**
     * @param int $ownerId
     * @param PreparedOrdination|null $ordination
     * @param PreparedPagination|null $pagination
     * @return array<int, WishlistEntity>
     * @throws \AlgolWishlist\Enum\Exceptions\UnexpectedValueException
     * @throws \ReflectionException
     * @throws Exception
     */
    public function getAllByOwnerId(
        int $ownerId,
        ?PreparedOrdination $ordination = null,
        ?PreparedPagination $pagination = null
    ): array {
        $orderBy = "";
        if ($ordination !== null) {
            $orderByColumns = [];
            $columns = WishlistEntity::getListOfOrderByColumns();
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

        $results = $this->dbRequestErrorsWrapper(function () use ($ownerId, $orderBy, $limit) {
            return $this->wpDb->get_results(
                $this->wpDb->prepare(
                    "SELECT * FROM {$this->getTableName()} WHERE ownerId = %d {$orderBy} {$limit}",
                    $ownerId
                )
            );
        });

        if (!is_array($results)) {
            return [];
        }

        $resultObjects = [];
        foreach ($results as $result) {
            $resultObjects[] = $this->mapQueryResultsToWishlistEntity($result);
        }

        return $resultObjects;
    }

    /**
     * @param PreparedOrdination|null $ordination
     * @param PreparedPagination|null $pagination
     * @return array<int, WishlistEntity>
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
            $columns = WishlistEntity::getListOfOrderByColumns();
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
            $resultObjects[] = $this->mapQueryResultsToWishlistEntity($result);
        }

        return $resultObjects;
    }

    /**
     * @param WishlistsOrderBy|null $ordination
     * @param PreparedPagination|null $pagination
     * @return array
     * @throws \AlgolWishlist\Enum\Exceptions\UnexpectedValueException
     * @throws \ReflectionException
     * @throws Exception
     *
     */
    public function getList(?WishlistsOrderBy $ordination = null, ?PreparedPagination $pagination = null): array
    {
        $orderBy = "";
        if ($ordination->getList()) {
            $orderBy = "ORDER BY " . join(
                    ",",
                    array_map(function ($item) {
                        return $item['field'] . " " . $item['sort']->getValue();
                    }, $ordination->getList())
                );
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
            $itemsOfWishlistTableName = (new ItemsOfWishlistRepositoryWordpress($this->wpDb))->getTableName();
            $usersTableName = $this->wpDb->users;

            return $this->wpDb->get_results(
                "SELECT SQL_CALC_FOUND_ROWS {$this->getTableName()}.id as id,
                                               {$this->getTableName()}.title as name,
                                               {$this->getTableName()}.token as token,
                                               {$usersTableName}.ID as ownerId,
                                               {$usersTableName}.display_name as username,
                                               {$this->getTableName()}.shareType as shareType,
                                               {$this->getTableName()}.createdAt as dateCreated,
                                               COUNT({$itemsOfWishlistTableName}.wishlistId) as itemsCount
                    FROM {$this->getTableName()}
                        LEFT JOIN {$itemsOfWishlistTableName} ON {$this->getTableName()}.id = {$itemsOfWishlistTableName}.wishlistId
                        LEFT JOIN {$usersTableName} ON {$this->getTableName()}.ownerId = {$usersTableName}.ID
                        GROUP BY {$this->getTableName()}.id
                        {$orderBy}
                         {$limit}
                         "
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
    public function moveAllItemsFromGuestDefaultWishlistToUserDefaultWishlist(
        string $guestSessionKey,
        int $targetUserId
    ): bool {
        $sourceWishlist = (new GuestMetaData($guestSessionKey))->getDefaultWishlist();
        if ($sourceWishlist === null) {
            return false;
        }

        $targetWishlist = (new UserMetaData($targetUserId))->getDefaultWishlist();
        if ($targetWishlist === null) {
            return false;
        }

        $result = $this->dbRequestErrorsWrapper(function () use ($sourceWishlist, $targetWishlist) {
            $itemsOfWishlistTableName = (new ItemsOfWishlistRepositoryWordpress($this->wpDb))->getTableName();

            return $this->wpDb->update(
                $itemsOfWishlistTableName,
                [
                    "wishlistId" => $targetWishlist->id,
                    "priority" => 0,
                ],
                ["wishlistId" => $sourceWishlist->id]
            );
        });

        if ($result === false) {
            return false;
        }

        return true;
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
        $usersTableName = $this->wpDb->users;
        $sessionsTableName = $this->wpDb->prefix . 'woocommerce_sessions';

        if ($this->wpDb->get_var("SHOW TABLES LIKE '$tableName'") === $tableName) {
            $this->wpDb->query(
                "ALTER TABLE {$tableName} DROP FOREIGN KEY FKownerId;"
            );
            $this->wpDb->query(
                "ALTER TABLE {$tableName} DROP FOREIGN KEY FKsessionId;"
            );
        }

        $sql = /** @lang MySQL */
            "CREATE TABLE {$tableName} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                title VARCHAR(50) NOT NULL,
                token VARCHAR(50) NOT NULL,
                ownerId BIGINT UNSIGNED NULL,
                sessionKey CHAR(32) NULL,
                shareType INT NOT NULL,
                createdAt DATETIME NOT NULL,
                PRIMARY KEY (id)
             ) $charsetCollate;";
        dbDelta($sql);

        $this->wpDb->query(
            "ALTER TABLE {$tableName}
                    ADD CONSTRAINT FKownerId FOREIGN KEY (ownerId)
                    REFERENCES $usersTableName(id) ON DELETE CASCADE ON UPDATE CASCADE;"
        );
        $this->wpDb->query(
            "ALTER TABLE {$tableName}
                    ADD CONSTRAINT FKsessionId FOREIGN KEY (sessionKey)
                    REFERENCES $sessionsTableName(session_key) ON DELETE CASCADE ON UPDATE CASCADE;"
        );
    }

    public function deleteTable()
    {
        $tableName = $this->getTableName();
        $this->wpDb->query("DROP TABLE IF EXISTS $tableName");
    }
}
