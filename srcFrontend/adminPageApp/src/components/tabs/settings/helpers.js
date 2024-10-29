import React from 'react';
import {store} from 'Store/store';
import {settingSet} from "Store/slices/settingsSlice";

export const getSettingValue = (data, id) => {
    const setting = data instanceof Array ? data.find(x => x.id === id) : false;
    return setting && setting.hasOwnProperty('value') ?
        setting.value : false;
}

export const getSettingLabel = (data, id) => {
    const setting = data instanceof Array ? data.find(x => x.id === id) : false;
    return setting && setting.hasOwnProperty('title') ?
        setting.title : '';
}

export const getSettingCustomizeUrl = (data, id) => {
    const setting = data instanceof Array ? data.find(x => x.id === id) : false;
    return setting && setting.hasOwnProperty('customizeUrl') ? setting.customizeUrl.customizeUrl : '';
}

export const getSettingSelections = (data, id) => {
    const setting = data instanceof Array ? data.find(x => x.id === id) : false;
    return setting && setting.hasOwnProperty('selections') ? setting.selections : [];
}

export const commitSettingValue = (id, value) => {
    const dispatch = store.dispatch;
    dispatch(settingSet({id: id, value: value}));
}
