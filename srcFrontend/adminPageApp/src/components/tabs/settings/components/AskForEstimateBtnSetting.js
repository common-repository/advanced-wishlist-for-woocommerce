import React from 'react';
import {useTranslation} from 'react-i18next';
import CheckboxSetting from "./CheckboxSetting";

const AskForEstimateBtnSetting = (props) => {
    const {t} = useTranslation();

    let config = {
        value: null,
        label: '',
        customizeUrl: {
            customizeBtnUrl: '',
            customizeEmailUrl: '',
        },
        onChangeValue: () => void 0,
        ...props,
        id: 'enable_ask_for_estimate_btn',
    }

    const customizeButtonLink = config.customizeUrl.customizeBtnUrl ?
        <a href={config.customizeUrl.customizeBtnUrl}>{t('Customize Button')}</a> : "";
    const customizeEmailLink = config.customizeUrl.customizeEmailUrl ?
        <a href={config.customizeUrl.customizeEmailUrl}>{t('Customize Email')}</a> : "";

    return (
        <div>
            <CheckboxSetting id={config.id}
                             label={config.label}
                             value={config.value}
                             onChangeValue={config.onChangeValue}
            />
            <br/>
            {customizeButtonLink}
            <br/>
            {customizeEmailLink}
        </div>
    );
};

export default AskForEstimateBtnSetting;
