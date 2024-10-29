import React from 'react';
import {useTranslation} from 'react-i18next';
import {Checkbox, FormControlLabel} from "@mui/material";

const CheckboxSetting = (props) => {
    const {t} = useTranslation();

    let config = {
        id: '',
        value: null,
        label: '',
        customizeUrl: '',
        onChangeValue: () => void 0,
        ...props
    }

    let customizeLink = config.customizeUrl ? <a href={config.customizeUrl}>{t('Customize')}</a> : "";

    return (
        <div>
            <FormControlLabel control={<Checkbox id={config.id} name={config.id} checked={config.value}/>}
                              label={config.label}
                              onChange={(e) => config.onChangeValue(config.id, e.target.checked)}
            />
            {customizeLink}
        </div>
    );
};

export default CheckboxSetting;
