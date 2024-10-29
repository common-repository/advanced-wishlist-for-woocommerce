<?php

namespace AlgolWishlist\SessionHandler;

class SessionHandlerRegister
{
    public function register() {
        add_filter( 'woocommerce_session_handler', function () {
            return 'AlgolWishlist\SessionHandler\WishlistSessionHandler';
        } );
    }
}
