<?php

namespace AlgolWishlist\SessionHandler;

use AlgolWishlist\Repositories\Wishlists\WishlistsRepositoryWordpress;

class WishlistSessionHandler extends \WC_Session_Handler
{
    public function delete_session($customerId)
    {
        $currentUserId = get_current_user_id();

        if ($customerId !== "" && $currentUserId !== 0 && $customerId !== $currentUserId) {
            global $wpdb;

            try {
                (new WishlistsRepositoryWordpress($wpdb))->moveAllItemsFromGuestDefaultWishlistToUserDefaultWishlist(
                    (string)$customerId,
                    (int)$currentUserId
                );
            } catch (\Exception $e) {
                awlContext()->getLogger()->critical("Cannot move wishlist items of guest! Reason: {$e->getMessage()}");
            }
        }

        parent::delete_session($customerId);
    }
}
