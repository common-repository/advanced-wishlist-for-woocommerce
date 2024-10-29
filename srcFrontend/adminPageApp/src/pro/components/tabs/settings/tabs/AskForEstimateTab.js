import React from 'react';
import {useTranslation} from 'react-i18next';
import CheckboxSetting from "Components/tabs/settings/components/CheckboxSetting";
import AskForEstimateBtnSetting from "Components/tabs/settings/components/AskForEstimateBtnSetting";
import {
    getSettingCustomizeUrl,
    getSettingLabel,
    getSettingValue,
    commitSettingValue
} from "Components/tabs/settings/helpers";

const AskForEstimateTab = (props) => {
    const {t} = useTranslation();

    const enableAskForEstimateBtnId = 'enable_ask_for_estimate_btn';
    const enableAskForEstimateBtnValue = getSettingValue(props.settingsData, enableAskForEstimateBtnId);
    const enableAskForEstimateBtnLabel = getSettingLabel(props.settingsData, enableAskForEstimateBtnId);
    const enableAskForEstimateBtnCustomizeUrl = getSettingCustomizeUrl(props.settingsData, enableAskForEstimateBtnId);

    const enableAdditionalNotesId = 'enable_additional_notes';
    const enableAdditionalNotesValue = getSettingValue(props.settingsData, enableAdditionalNotesId);
    const enableAdditionalNotesLabel = getSettingLabel(props.settingsData, enableAdditionalNotesId);

    return (
        <div>
            <h1>{t('Ask for Estimate')}</h1>
            <AskForEstimateBtnSetting label={t(enableAskForEstimateBtnLabel)}
                                      value={enableAskForEstimateBtnValue}
                                      customizeUrl={enableAskForEstimateBtnCustomizeUrl}
                                      onChangeValue={commitSettingValue}
            />
            <CheckboxSetting id={enableAdditionalNotesId}
                             label={t(enableAdditionalNotesLabel)}
                             value={enableAdditionalNotesValue}
                             onChangeValue={commitSettingValue}
            />
        </div>
    );
};

export default AskForEstimateTab;
