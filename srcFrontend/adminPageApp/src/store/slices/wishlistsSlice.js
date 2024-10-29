import {createAsyncThunk, createSlice} from '@reduxjs/toolkit';
import {ApiClient} from "ApiClient";

const initialState = {
    list: [],
    total: 0,
    status: 'idle',
    searchByText: ""
};

export const loadWishlistsAsync = createAsyncThunk(
    'wishlists/fetchWishlists',
    async (args, {rejectWithValue}) => {
        let response = {};

        const {searchByText, sorting, pagination} = args;

        const sortField = typeof sorting[0] !== "undefined" && typeof sorting[0].field !== "undefined" ? sorting[0].field : "";
        const sort = typeof sorting[0] !== "undefined" && typeof sorting[0].sort !== "undefined" ? sorting[0].sort : "";

        try {
            const promise = new ApiClient().wishlistApi.adminGetAllWishlists(
                pagination.currentPage,
                pagination.perPage,
                searchByText,
                sortField,
                sort,
            );
            promise.catch((reason) => console.log(reason));

            response = await promise;
        } catch (e) {
            return rejectWithValue(e.response.data)
        }

        return {
            list: response.data.items.map((item) => (
                {
                    id: item.id,
                    name: item.title,
                    username: item.owner.username,
                    shareType: item.shareTypeId,
                    dateCreated: item.dateCreated,
                    itemsCount: item.itemsCount,
                }
            )),
            total: response.data.total,
            searchByText: searchByText,
        }
    }
);

export const deleteWishlistsAsync = createAsyncThunk(
    'wishlists/deleteWishlists',
    async (args, {rejectWithValue}) => {
        let responses = {};

        const {wishlistIds} = args;
        const promises = [];

        try {
            wishlistIds.forEach((wishlistId) => {
                const promise = new ApiClient().wishlistApi.adminDeleteWishlist(wishlistId);
                promises.push(promise);
                promise.catch((reason) => console.log(reason));
            })

            const allPromise = Promise.all(promises);
            allPromise.catch((reason) => console.log(reason));
            responses = await allPromise;
        } catch (e) {
            return rejectWithValue(e.response.data)
        }

        return true;
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
                state.total = action.payload.total;
                state.timeRange = action.payload.timeRange;
                state.searchByText = action.payload.searchByText;
            })
            .addCase(deleteWishlistsAsync.pending, (state) => {
                state.status = 'loading';
            })
            .addCase(deleteWishlistsAsync.fulfilled, (state, action) => {
                state.status = 'idle';
            });
    },
});

export const getWishlists = (state) => state.wishlists.list;
export const getWishlistsTotalRows = (state) => state.wishlists.total;
export const getWishlistsSearchByText = (state) => state.wishlists.searchByText;


export default wishlistsSlice.reducer;
