<?php

namespace AlgolWishlist\Repositories;

use Exception;

abstract class WordpressRepository
{
    /**
     * @var \wpdb
     */
    protected $wpDb;

    public function __construct(\wpdb $wpDb)
    {
        $this->wpDb = $wpDb;
    }

    /**
     * @throws Exception
     */
    protected function dbRequestErrorsWrapper($callback)
    {
        $showErrors = $this->wpDb->show_errors;
        $this->wpDb->show_errors = false;
        $result = $callback();
        $this->wpDb->show_errors = $showErrors;

        if ($this->wpDb->last_error) {
            throw new Exception($this->wpDb->last_error);
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    public function deleteById(int $id): bool
    {
        $result = $this->dbRequestErrorsWrapper(function () use ($id) {
            return $this->wpDb->delete(
                $this->getTableName(),
                ["id" => $id]
            );
        });

        if ($result === false) {
            return false;
        }

        return (bool)$result;
    }
}
