import {createAsyncThunk, createSlice} from '@reduxjs/toolkit';
import {ApiClient} from "ApiClient";

const initialState = {
    list: [],
    status: 'idle',
};

export const loadSettingsAsync = createAsyncThunk(
    'settings/fetchSettings',
    async (_, {rejectWithValue}) => {
        let response = {};

        try {
            const promise = new ApiClient().settingsApi.getAllSettings();
            promise.catch((reason) => console.log(reason));

            response = await promise;
        } catch (e) {
            return rejectWithValue(e.response.data)
        }

        return {
            list: response.data.map((setting) => (
                {
                    id: setting.id,
                    type: setting.type,
                    title: setting.title,
                    defaultValue: setting.defaultValue,
                    value: setting.value,
                    customizeUrl: setting.customizeUrls,
                    selections: setting.selections,
                }
            )),
        };
    }
);

export const saveSettingsAsync = createAsyncThunk(
    'settings/saveSettings',
    async (_, thunkAPI) => {
        const state = thunkAPI.getState();

        let settings = state.settings.list;
        let requestSettings = {};
        settings.forEach((setting) => {
            requestSettings[setting.id] = setting.value;
        });

        let response = {};

        try {
            const promise = new ApiClient().settingsApi.updateAllSettings(requestSettings);
            promise.catch((reason) => console.log(reason));

            response = await promise;
        } catch (e) {
            return {
                list: []
            };
        }

        return {
            list: response.data.map((setting) => (
                {
                    id: setting.id,
                    type: setting.type,
                    title: setting.title,
                    defaultValue: setting.defaultValue,
                    value: setting.value,
                    customizeUrl: setting.customizeUrls,
                    selections: setting.selections,
                }
            )),
        };
    }
);

export const settingsSlice = createSlice({
    name: 'settings',
    initialState,
    reducers: {
        settingSet(state, action) {
            const {id, value} = action.payload;
            const setting = state.list.find((setting) => setting.id === id);
            if (setting) {
                setting.value = value;
            }
        }
    },
    extraReducers: (builder => {
        builder
            .addCase(loadSettingsAsync.pending, (state) => {
                state.status = 'loading';
            })
            .addCase(loadSettingsAsync.fulfilled, (state, action) => {
                state.status = 'idle';
                state.list = action.payload.list;
            })
            .addCase(saveSettingsAsync.pending, (state) => {
                state.status = 'loading';
            })
            .addCase(saveSettingsAsync.fulfilled, (state, action) => {
                state.status = 'idle';
                state.list = action.payload.list;
            })
    })
});

export const getSettings = (state) => state.settings.list;

export const {settingSet} = settingsSlice.actions;

export default settingsSlice.reducer;
