<?php

namespace AlgolWishlist;

use AlgolWishlist\Logger\ILogger;
use AlgolWishlist\Logger\LoggerImpl;
use AlgolWishlist\Settings\SettingsFramework\OptionsManager;
use AlgolWishlist\User\User;

class Context
{
    /**
     * @var OptionsManager
     */
    private $settings;

    /**
     * @var ILogger
     */
    private $logger;

    /**
     * @var QueryContext
     */
    private $queryContext;

    /**
     * @var User|null
     */
    private $currentUser;

    public function __construct(OptionsManager $settings)
    {
        $this->settings = $settings;
        $this->logger = new LoggerImpl();

        $this->queryContext = new QueryContext();
        $this->currentUser = User::initFromGlobals($this);
    }

    /**
     * @return OptionsManager
     */
    public function getSettings(): OptionsManager
    {
        return $this->settings;
    }

    public function getLogger(): ILogger
    {
        return $this->logger;
    }

    /**
     * @return QueryContext
     */
    public function getQueryContext(): QueryContext
    {
        return $this->queryContext;
    }

    public function getCurrentUser(): ?User
    {
        return $this->currentUser;
    }
}
