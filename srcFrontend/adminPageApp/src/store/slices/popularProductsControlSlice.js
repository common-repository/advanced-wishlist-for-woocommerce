import {createSlice} from '@reduxjs/toolkit';

const initialState = {
    selectedPopularProductIdForUsers: 0,
    selectedPopularProductNameForUsers: "",
};

export const popularProductsControlSlice = createSlice({
    name: 'popularProductsControl',
    initialState,
    reducers: {
        selectedPopularProductForUsersUpdated(state, action) {
            state.selectedPopularProductIdForUsers = action.payload.id;
            state.selectedPopularProductNameForUsers = action.payload.name;
        },
        selectedPopularProductForUsersDrop(state, action) {
            state.selectedPopularProductIdForUsers = 0;
            state.selectedPopularProductNameForUsers = "";
        },
    },
});

export const getSelectedPopularProductForUsers = (state) => {
    return {
        id: state.popularProductsControl.selectedPopularProductIdForUsers,
        name: state.popularProductsControl.selectedPopularProductNameForUsers
    }
};


export const {
    selectedPopularProductForUsersUpdated,
    selectedPopularProductForUsersDrop
} = popularProductsControlSlice.actions


export default popularProductsControlSlice.reducer;
