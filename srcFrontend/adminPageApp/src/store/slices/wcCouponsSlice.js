import {createAsyncThunk, createSlice} from '@reduxjs/toolkit';
import {ApiClient} from "ApiClient";

const initialState = {
    list: [],
    status: 'idle',
};

export const loadWcCouponsAsync = createAsyncThunk(
    'wc-coupons/fetchCoupons',
    async (args, {rejectWithValue}) => {
        let response = {};

        const {term, limit} = args;

        try {
            const promise = new ApiClient().wcCouponsApi.adminGetWcCoupons(
                term,
                limit
            );
            promise.catch((reason) => console.log(reason));

            response = await promise;
        } catch (e) {
            console.log(e)
            return rejectWithValue(e.response.data)
        }

        return {
            list: response.data.list,
        }
    }
);

export const wcCouponsSlice = createSlice({
    name: 'wcCoupons',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(loadWcCouponsAsync.pending, (state) => {
                state.status = 'loading';
            })
            .addCase(loadWcCouponsAsync.fulfilled, (state, action) => {
                state.status = 'idle';
                state.list = action.payload.list;
            });
    },
});

export const getWcCoupons = (state) => state.wcCoupons.list;

export default wcCouponsSlice.reducer;
