import {createAsyncThunk, createSlice} from '@reduxjs/toolkit';
import {ApiClient} from "ApiClient";

const initialState = {
    list: [],
    status: 'idle',
};

export const loadWishlistsAsync = createAsyncThunk(
    'productsOfWishlist/fetchWishlists',
    async (args, {rejectWithValue}) => {
        let response = {};

        try {
            const promise = new ApiClient().publicWishlistApi.publicWishlistsGet();
            promise.catch((reason) => console.log(reason));

            response = await promise;
        } catch (e) {
            return rejectWithValue(e.response.data)
        }

        return {
            list: response.data,
        }
    }
);

export const wishlistsSlice = createSlice({
    name: 'wishlists',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(loadWishlistsAsync.pending, (state) => {
                state.status = 'loading';
            })
            .addCase(loadWishlistsAsync.fulfilled, (state, action) => {
                state.status = 'idle';
                state.list = action.payload.list;
            })
    },
});

export const getWishlists = (state) => state.wishlists.list;

export default wishlistsSlice.reducer;
