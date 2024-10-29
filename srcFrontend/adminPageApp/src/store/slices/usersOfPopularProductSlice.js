import {createAsyncThunk, createSlice} from '@reduxjs/toolkit';
import {ApiClient} from "ApiClient";

const initialState = {
    list: [],
    total: 0,
    status: 'idle',
    productId: 0,
};

export const loadUsersOfPopularProductAsync = createAsyncThunk(
    'popularProducts/fetchUsersOfPopularProducts',
    async (args, {rejectWithValue}) => {
        let response = {};

        const {productId, sorting, pagination} = args;

        try {
            const promise = new ApiClient().popularProductApi.adminGetUsersOfPopularItem({
                productId: productId,
                variation: {},
                sorting: sorting,
                pagination: pagination,
            });
            promise.catch((reason) => console.log(reason));

            response = await promise;
        } catch (e) {
            return rejectWithValue(e.response.data)
        }

        return {
            list: response.data.items,
            total: response.data.total,
            productId: productId,
        }
    }
);

export const usersOfPopularProductSlice = createSlice({
    name: 'usersOfPopularProduct',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(loadUsersOfPopularProductAsync.pending, (state) => {
                state.status = 'loading';
            })
            .addCase(loadUsersOfPopularProductAsync.fulfilled, (state, action) => {
                state.status = 'idle';
                state.list = action.payload.list;
                state.total = action.payload.total;
                state.productId = action.payload.productId;
            });
    },
});

export const getUsersOfPopularProduct = (state) => state.usersOfPopularProduct.list;
export const getUsersOfPopularProductTotalRows = (state) => state.usersOfPopularProduct.total;


export default usersOfPopularProductSlice.reducer;
