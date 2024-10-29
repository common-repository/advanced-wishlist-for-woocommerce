import {configureStore} from '@reduxjs/toolkit';
import productsOfWishlistSliceReducer from "./slices/productsOfWishlistSlice";
import wishlistsSliceReducer from "./slices/wishlistsSlice";
import notificationsSliceReducer from "./slices/notifications";

export const store = configureStore({
    reducer: {
        productsOfWishlist: productsOfWishlistSliceReducer,
        wishlists: wishlistsSliceReducer,
        notifications: notificationsSliceReducer,
    },
});
