import {createSlice} from '@reduxjs/toolkit';
import { current } from '@reduxjs/toolkit'

const initialState = {
    list: [],
};

const nextId = (list) => {
    const maxId = list.reduce((maxId, todo) => Math.max(todo.id, maxId), -1)
    return maxId + 1
}

export const notificationsSlice = createSlice({
    name: 'notifications',
    initialState,
    reducers: {
        appendNotification(state, action) {
            const notification = action.payload;
            notification.id = nextId(state.list)
            state.list.push(notification);
        },
        removeNotification(state, action) {
            const notification = action.payload;
            state.list = state.list.filter((loopItem) => loopItem.id !== notification.id);
        },
    }
});

export const {appendNotification, removeNotification} = notificationsSlice.actions;

export const getNotificationsList = (state) => state.notifications.list;

export default notificationsSlice.reducer;
