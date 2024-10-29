import {createAsyncThunk, createSlice} from '@reduxjs/toolkit';
import {ApiClient} from "ApiClient";
import {PromotionalEmailPreviewRequest} from "ApiClientLib/models";

const initialState = {
    preview: '',
    status: 'idle',

    emailReceiversCount: '',
    emailReceiversLabel: '',
    emailReceiversStatus: 'idle',

    saveDraftSuccess: null,
    saveDraftStatus: 'idle',

    sendEmailSuccess: null,
    sendEmailStatus: 'idle',
};

export const loadPromotionalEmailPreviewAsync = createAsyncThunk(
    'promotionalEmail/fetchPreview',
    async (args, {rejectWithValue}) => {
        let response = {};

        const {type, htmlContent, plainContent, productID, userID, coupon} = args;

        try {
            const promise = new ApiClient().promotionalEmailApi.adminPreviewPromotionalEmail(
                {
                    type: type,
                    htmlContent: htmlContent,
                    plainContent: plainContent,
                    product: {
                        productId: productID,
                        variation: {},
                    },
                    userId: userID ? parseInt(userID): null,
                    coupon: coupon
                }
            );
            promise.catch((reason) => console.log(reason));

            response = await promise;
        } catch (e) {
            console.log(e)
            return rejectWithValue(e.response.data)
        }

        return {
            preview: response.data.preview,
        }
    }
);

export const loadPromotionalEmailSaveDraftAsync = createAsyncThunk(
    'promotionalEmail/fetchSaveDraft',
    async (args, {rejectWithValue}) => {
        let response = {};

        const {type, htmlContent, plainContent, productID, userID, coupon} = args;

        try {
            console.log(new ApiClient())

            const promise = new ApiClient().promotionalEmailApi.adminSaveDraftPromotionalEmail(
                {
                    type: type,
                    htmlContent: htmlContent,
                    plainContent: plainContent,
                    products: [
                        {
                            productId: productID,
                            variation: {},
                        }
                    ],
                    userIds: [userID],
                    coupon: coupon,
                }
            );
            promise.catch((reason) => console.log(reason));

            response = await promise;
        } catch (e) {
            console.log(e)
            return rejectWithValue(e.response.data)
        }

        return {}
    }
);

export const loadPromotionalEmailCalculateEmailReceiversAsync = createAsyncThunk(
    'promotionalEmail/fetchCalculateEmailReceivers',
    async (args, {rejectWithValue}) => {
        let response = {};

        const {productID, userID} = args;

        try {
            console.log(new ApiClient())
            const promise = new ApiClient().promotionalEmailApi.admincalculateEmailReceiversPromotionalEmail(
                {
                    products: [
                        {
                            productId: productID,
                            variation: {},
                        }
                    ],
                    userIds: [userID],
                }
            );
            promise.catch((reason) => console.log(reason));

            response = await promise;
        } catch (e) {
            console.log(e)
            return rejectWithValue(e.response.data)
        }

        return {
            count: response.data.count,
            label: response.data.label
        }
    }
);

export const loadPromotionalEmailSendAsync = createAsyncThunk(
    'promotionalEmail/fetchSendEmail',
    async (args, {rejectWithValue}) => {
        let response = {};

        const {type, htmlContent, plainContent, productID, userID, coupon} = args;

        try {
            const promise = new ApiClient().promotionalEmailApi.adminSendPromotionalEmail(
                {
                    type: type,
                    htmlContent: htmlContent,
                    plainContent: plainContent,
                    products: [
                        {
                            productId: productID,
                            variation: {},
                        }
                    ],
                    userIds: [userID],
                    coupon: coupon,
                }
            );
            promise.catch((reason) => console.log(reason));

            response = await promise;
        } catch (e) {
            console.log(e)
            return rejectWithValue(e.response.data)
        }

        return {
            count: response.data.count,
            label: response.data.label
        }
    }
);

export const promotionalEmailSlice = createSlice({
    name: 'promotionalEmail',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(loadPromotionalEmailPreviewAsync.pending, (state) => {
                state.status = 'loading';
            })
            .addCase(loadPromotionalEmailPreviewAsync.fulfilled, (state, action) => {
                state.status = 'idle';
                state.preview = action.payload.preview;
            })
            .addCase(loadPromotionalEmailCalculateEmailReceiversAsync.pending, (state) => {
                state.emailReceiversStatus = 'loading';
            })
            .addCase(loadPromotionalEmailCalculateEmailReceiversAsync.fulfilled, (state, action) => {
                state.emailReceiversStatus = 'idle';
                state.emailReceiversCount = action.payload.count;
                state.emailReceiversLabel = action.payload.label;
            })
            .addCase(loadPromotionalEmailSaveDraftAsync.pending, (state) => {
                state.saveDraftStatus = 'loading';
            })
            .addCase(loadPromotionalEmailSaveDraftAsync.fulfilled, (state, action) => {
                state.saveDraftStatus = 'idle';
                state.saveDraftSuccess = true;
            })
            .addCase(loadPromotionalEmailSendAsync.pending, (state) => {
                state.sendEmailStatus = 'loading';
            })
            .addCase(loadPromotionalEmailSendAsync.fulfilled, (state, action) => {
                state.sendEmailStatus = 'idle';
                state.sendEmailSuccess = true;
            });
    },
});

export const getPromotionalEmailPreview = (state) => state.promotionalEmail.preview;

export const getPromotionalEmailEmailReceiversLabel = (state) => state.promotionalEmail.emailReceiversLabel;

export default promotionalEmailSlice.reducer;
