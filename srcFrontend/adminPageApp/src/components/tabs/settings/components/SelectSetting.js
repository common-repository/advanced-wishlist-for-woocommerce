import React from 'react';
import {useTranslation} from "react-i18next";
import {FormControl, InputLabel, MenuItem, Select} from "@mui/material";
import {Label} from "@mui/icons-material";

const SelectSetting = (props) => {
    const {t} = useTranslation();

    let config = {
        id: '',
        value: null,
        label: '',
        customizeUrl: '',
        onChangeValue: () => void 0,
        options: {},
        ...props
    }

    const options = [];

    for (let key in config.options) {
        options.push(<MenuItem key={key} value={key}>{props.options[key]}</MenuItem>);
    }

    let customizeLink = config.customizeUrl ?
        <a style={{marginLeft: 15}} href={config.customizeUrl}>{t('Customize')}</a> : "";

    return (
        <div style={{display: 'flex', alignItems: 'center'}}>
            <InputLabel style={{marginRight: 10,}}>{config.label}</InputLabel>
            <FormControl>
                <Select id={config.id}
                        name={config.id}
                        value={config.value}
                        onChange={(e) => config.onChangeValue(config.id, e.target.value)}
                        style={{height: 40,}}
                >
                    {options}
                </Select>
            </FormControl>
            {customizeLink}
        </div>
    );
};

export default SelectSetting;
