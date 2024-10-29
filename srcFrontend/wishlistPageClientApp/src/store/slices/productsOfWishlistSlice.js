import {createAsyncThunk, createSlice} from '@reduxjs/toolkit';
import {ApiClient} from "ApiClient";

const initialState = {
    list: [],
    status: 'idle',
};

export const loadProductsOfWishlistAsync = createAsyncThunk(
    'productsOfWishlist/fetchProductsOfWishlist',
    async (args, {rejectWithValue}) => {
        let response = {};

        const {wishlistId} = args;

        try {
            const promise = new ApiClient().publicWishlistAndProductApi.getAllItemOfWishlist(
                wishlistId,
            );
            promise.catch((reason) => console.log(reason));

            response = await promise;
        } catch (e) {
            return rejectWithValue(e.response.data)
        }

        return response.data
    }
);

export const deleteProductOfWishlistAsync = createAsyncThunk(
    'productsOfWishlist/deleteProductOfWishlist',
    async (args, {rejectWithValue}) => {
        let response = {};

        const {wishlistId, relationshipId} = args;

        try {
            const promise = new ApiClient().publicWishlistAndProductApi.deleteItemOfWishlistById(
                wishlistId,
                relationshipId,
            );
            promise.catch((reason) => console.log(reason));

            response = await promise;
        } catch (e) {
            return rejectWithValue(e.response.data)
        }

        return relationshipId
    }
);

export const moveProductOfWishlistAsync = createAsyncThunk(
    'productsOfWishlist/moveProductOfWishlist',
    async (args, {rejectWithValue}) => {
        let response = {};

        const {wishlistId, relationshipId, newWishlistId} = args;

        try {
            const promise = new ApiClient().publicWishlistAndProductApi.updateItemOfWishlistById(
                {
                    id: relationshipId,
                    wishlistId: newWishlistId,
                },
                wishlistId,
            );
            promise.catch((reason) => console.log(reason));

            response = await promise;
        } catch (e) {
            return rejectWithValue(e.response.data)
        }

        return relationshipId
    }
);

export const addToCartProductOfWishlistAsync = createAsyncThunk(
    'productsOfWishlist/addToCartProductOfWishlist',
    async (args, {rejectWithValue}) => {
        let response = {};

        const {wishlistId, relationshipId, isDelete, isRedirect} = args;

        try {
            const promise = new ApiClient().publicWishlistAndProductApi.addToCartItemOfWishlist(
                {
                    deleteAddedToCart: isDelete,
                    withWcNotices: isRedirect,
                },
                wishlistId,
                relationshipId
            );
            promise.catch((reason) => console.log(reason));

            response = await promise;
        } catch (e) {
            return rejectWithValue(e.response.data)
        }

        return response.data
    }
);

export const commitNewOrdinationProductOfWishlistAsync = createAsyncThunk(
    'productsOfWishlist/reorderProductOfWishlist',
    async (args, {rejectWithValue, getState}) => {
        let response = {};

        const {wishlistId, list} = args;

        const newList = [];
        for ( let i = 0; i < list.length; i++ ) {
            newList[list[i].priority] = list[i].id;
        }

        try {
            const promise = new ApiClient().publicWishlistAndProductApi.reorderItemsOfWishlist(
                {
                    itemIds: newList,
                },
                wishlistId
            );
            promise.catch((reason) => console.log(reason));

            response = await promise;
        } catch (e) {
            return rejectWithValue(e.response.data)
        }

        return true;
    }
);

export const commitNewQuantityProductOfWishlistAsync = createAsyncThunk(
    'productsOfWishlist/setQuantityProductOfWishlist',
    async (args, {rejectWithValue, getState}) => {
        let response = {};

        const {wishlistId, relationshipId, quantity} = args;

        try {
            const promise = new ApiClient().publicWishlistAndProductApi.updateItemOfWishlistById(
                {
                    id: relationshipId,
                    quantity: quantity,
                },
                wishlistId
            );
            promise.catch((reason) => console.log(reason));

            response = await promise;
        } catch (e) {
            return rejectWithValue(e.response.data)
        }

        return true;
    }
);

export const batchDeleteItemsOfWishlistAsync = createAsyncThunk(
    'productsOfWishlist/batchDeleteItemsOfWishlistAsync',
    async (args, {rejectWithValue, getState}) => {
        let response = {};

        const {wishlistId, relationshipIds} = args;

        try {
            const promise = new ApiClient().publicWishlistAndProductApi.batchDeleteItemsOfWishlist(
                {
                    itemIds: relationshipIds,
                },
                wishlistId
            )
            promise.catch((reason) => console.log(reason))

            response = await promise
        } catch (e) {
            return rejectWithValue(e.response.data)
        }

        return response.data
    }
);

export const batchAddToCartItemsOfWishlistAsync = createAsyncThunk(
    'productsOfWishlist/batchAddToCartItemsOfWishlistAsync',
    async (args, {rejectWithValue, getState}) => {
        let response = {};

        const {wishlistId, relationshipIds, isDelete, isRedirect} = args;

        try {
            const promise = new ApiClient().publicWishlistAndProductApi.batchAddToCartItemsOfWishlist(
                {
                    itemIds: relationshipIds,
                    deleteAddedToCart: isDelete,
                    withWcNotices: isRedirect,
                },
                wishlistId
            )
            promise.catch((reason) => console.log(reason))

            response = await promise
        } catch (e) {
            return rejectWithValue(e.response.data)
        }

        return response.data
    }
);

export const addToCartAllItemsOfWishlistAsync = createAsyncThunk(
    'productsOfWishlist/addToCartAllItemsOfWishlistAsync',
    async (args, {rejectWithValue, getState}) => {
        let response = {};

        const {wishlistId, isDelete, isRedirect} = args;

        try {
            const promise = new ApiClient().publicWishlistAndProductApi.addToCartAllItemsOfWishlist(
                {
                    deleteAddedToCart: isDelete,
                    withWcNotices: isRedirect,
                },
                wishlistId
            )
            promise.catch((reason) => console.log(reason))

            response = await promise
        } catch (e) {
            return rejectWithValue(e.response.data)
        }

        return response.data
    }
);

export const productsOfWishlistSlice = createSlice({
    name: 'productsOfWishlist',
    initialState,
    reducers: {
        setList: (state,action) => {
            state.list = action.payload;
        },
        removeRowFromList: (state,action) => {
            const row = action.payload;

            state.list = state.list.filter((loopRow) => loopRow.id !== row.id);
        },
        setQuantityByRowId: (state,action) => {
            const {rowId, quantity} = action.payload;

            state.list.map((loopRow) => {loopRow.id === rowId && (loopRow.quantity = quantity)});
        },
    },
    extraReducers: (builder) => {
        builder
            .addCase(loadProductsOfWishlistAsync.pending, (state) => {
                state.status = 'loading';
            })
            .addCase(loadProductsOfWishlistAsync.fulfilled, (state, action) => {
                state.status = 'idle';
                state.list = action.payload;
            })
            .addCase(deleteProductOfWishlistAsync.pending, (state) => {
                state.status = 'loading';
            })
            .addCase(deleteProductOfWishlistAsync.fulfilled, (state, action) => {
                state.status = 'idle';
            })
            .addCase(moveProductOfWishlistAsync.pending, (state) => {
                state.status = 'loading';
            })
            .addCase(moveProductOfWishlistAsync.fulfilled, (state, action) => {
                state.status = 'idle';
            })
            .addCase(addToCartProductOfWishlistAsync.pending, (state) => {
                state.status = 'loading';
            })
            .addCase(addToCartProductOfWishlistAsync.fulfilled, (state, action) => {
                state.status = 'idle';
                state.list = action.payload.newItems;
            })
            .addCase(commitNewOrdinationProductOfWishlistAsync.pending, (state, action) => {
                state.status = 'loading';
            })
            .addCase(commitNewOrdinationProductOfWishlistAsync.fulfilled, (state, action) => {
                state.status = 'idle';
            })
            .addCase(batchDeleteItemsOfWishlistAsync.pending, (state, action) => {
                state.status = 'loading';
            })
            .addCase(batchDeleteItemsOfWishlistAsync.fulfilled, (state, action) => {
                state.status = 'idle';
                state.list = action.payload.newItems;
            })
            .addCase(batchAddToCartItemsOfWishlistAsync.pending, (state, action) => {
                state.status = 'loading';
            })
            .addCase(batchAddToCartItemsOfWishlistAsync.fulfilled, (state, action) => {
                state.status = 'idle';
                state.list = action.payload.newItems;
            })
    },
});

export const getProductsOfWishlist = (state) => state.productsOfWishlist.list;
export const { setList, removeRowFromList, setQuantityByRowId } = productsOfWishlistSlice.actions


export default productsOfWishlistSlice.reducer;
