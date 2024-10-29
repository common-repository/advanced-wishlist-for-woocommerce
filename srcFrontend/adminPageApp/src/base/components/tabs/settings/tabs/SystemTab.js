import React from 'react';
import {useTranslation} from 'react-i18next';
import CheckboxSetting from "Components/tabs/settings/components/CheckboxSetting";
import {
    getSettingLabel,
    getSettingValue,
    commitSettingValue,
} from "Components/tabs/settings/helpers";

const WishlistsTab = (props) => {
    const {t} = useTranslation();

    const uninstallRemoveDataId = 'uninstall_remove_data';
    const uninstallRemoveDataValue = getSettingValue(props.settingsData, uninstallRemoveDataId);
    const uninstallRemoveDataLabel = getSettingLabel(props.settingsData, uninstallRemoveDataId);

    return (
        <div>
            <h1>{t('System')}</h1>
            <CheckboxSetting id={uninstallRemoveDataId}
                             label={t(uninstallRemoveDataLabel)}
                             value={uninstallRemoveDataValue}
                             onChangeValue={commitSettingValue}
            />
        </div>
    );
};

export default WishlistsTab;
