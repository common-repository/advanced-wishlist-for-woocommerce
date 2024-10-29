<?php

namespace AlgolWishlist\Logger;

class LoggerImpl implements ILogger
{
    /**
     * @var \WC_Logger_Interface
     */
    protected $wcLogger;

    /**
     * @var string
     */
    protected $context;

    public function __construct()
    {
        if (did_action("plugins_loaded")) {
            $this->wcLogger = wc_get_logger();
        } else {
            add_action("plugins_loaded", function () {
                $this->wcLogger = wc_get_logger();
            });
        }

        $this->context = ["source" => "awl-wishlist"];
    }

    protected function backTraceMixIn($message)
    {
        return sprintf("%s\n%s", $message, print_r((new \Exception())->getTraceAsString(), true)) . PHP_EOL;
    }

    public function emergency($message)
    {
        $this->wcLogger->emergency($this->backTraceMixIn($message), $this->context);
    }

    public function alert($message)
    {
        $this->wcLogger->alert($this->backTraceMixIn($message), $this->context);
    }

    public function critical($message)
    {
        $this->wcLogger->critical($this->backTraceMixIn($message), $this->context);
    }

    public function error($message)
    {
        $this->wcLogger->error($this->backTraceMixIn($message), $this->context);
    }

    public function warning($message)
    {
        $this->wcLogger->warning($this->backTraceMixIn($message), $this->context);
    }

    public function notice($message)
    {
        $this->wcLogger->warning($message, $this->context);
    }

    public function info($message)
    {
        $this->wcLogger->info($message, $this->context);
    }

    public function debug($message)
    {
        $this->wcLogger->debug($message, $this->context);
    }
}
