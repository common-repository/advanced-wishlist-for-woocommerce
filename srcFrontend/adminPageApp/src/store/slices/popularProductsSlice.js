import {createAsyncThunk, createSlice} from '@reduxjs/toolkit';
import {ApiClient} from "ApiClient";

const initialState = {
    list: [],
    total: 0,
    status: 'idle',
    timeRange: "last_week",
    searchByText: ""
};

export const loadPopularProductsAsync = createAsyncThunk(
    'popularProducts/fetchPopularProducts',
    async (args, {rejectWithValue}) => {
        let response = {};

        const {timeRange, searchByText, sorting, pagination} = args;

        try {
            const promise = new ApiClient().popularProductApi.adminGetPopularProducts({
                timeRange: timeRange,
                searchByText: searchByText,
                sorting: sorting,
                pagination: pagination,
            });
            promise.catch((reason) => console.log(reason));

            response = await promise;
        } catch (e) {
            return rejectWithValue(e.response.data)
        }

        return {
            list: response.data.items.map((item) => (
                {
                    name: item.title,
                    id: item.id,
                    variation: item.variation,
                    category: item.categories.map((category) => category.name).join(","),
                    counter: item.totalCount,
                    link: item.link,
                }
            )),
            total: response.data.total,
            timeRange: timeRange,
            searchByText: searchByText,
        }
    }
);

export const popularProductsSlice = createSlice({
    name: 'popularProducts',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(loadPopularProductsAsync.pending, (state) => {
                state.status = 'loading';
            })
            .addCase(loadPopularProductsAsync.fulfilled, (state, action) => {
                state.status = 'idle';
                state.list = action.payload.list;
                state.total = action.payload.total;
                state.timeRange = action.payload.timeRange;
                state.searchByText = action.payload.searchByText;
            });
    },
});

export const getPopularProducts = (state) => state.popularProducts.list;
export const getPopularProductsTotalRows = (state) => state.popularProducts.total;
export const getPopularProductsTimeRange = (state) => state.popularProducts.timeRange;
export const getPopularProductSearchByText = (state) => state.popularProducts.searchByText;


export default popularProductsSlice.reducer;
