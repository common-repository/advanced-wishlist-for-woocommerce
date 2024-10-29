import {configureStore} from '@reduxjs/toolkit';
import popularProductsReducer from './slices/popularProductsSlice';
import usersOfPopularProductReducer from './slices/usersOfPopularProductSlice';
import popularProductsControlReducer from './slices/popularProductsControlSlice';
import wishlistsSliceReducer from './slices/wishlistsSlice';
import settingsReducer from './slices/settingsSlice';
import promotionalEmailReducer from './slices/promotionalEmailSlice';
import wcCouponsReducer from './slices/wcCouponsSlice';

export const store = configureStore({
    reducer: {
        popularProducts: popularProductsReducer,
        wishlists: wishlistsSliceReducer,
        usersOfPopularProduct: usersOfPopularProductReducer,
        popularProductsControl: popularProductsControlReducer,
        settings: settingsReducer,
        promotionalEmail: promotionalEmailReducer,
        wcCoupons: wcCouponsReducer,
    },
});
