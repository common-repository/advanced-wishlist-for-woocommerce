<?php

namespace AlgolWishlist\Logger;

interface ILogger
{
    public function emergency($message);

    public function alert($message);

    public function critical($message);

    public function error($message);

    public function warning($message);

    public function notice($message);

    public function info($message);

    public function debug($message);
}