import React from 'react';
import {useTranslation} from 'react-i18next';
import CheckboxSetting from "Components/tabs/settings/components/CheckboxSetting";
import {
    getSettingLabel,
    getSettingValue,
    commitSettingValue
} from "Components/tabs/settings/helpers";

const GeneralTab = (props) => {
    const {t} = useTranslation();

    const enableWishlistForLoggedOnlyId = 'enable_wishlists_for_logged_in_only';
    const enableWishlistForLoggedOnlyValue = getSettingValue(props.settingsData, enableWishlistForLoggedOnlyId);
    const enableWishlistForLoggedOnlyLabel = getSettingLabel(props.settingsData, enableWishlistForLoggedOnlyId);

    const enableMultiWishlistSupportId = 'enable_multi_wishlist_support';
    const enableMultiWishlistSupportValue = getSettingValue(props.settingsData, enableMultiWishlistSupportId);
    const enableMultiWishlistSupportLabel = getSettingLabel(props.settingsData, enableMultiWishlistSupportId);

    return (
        <div>
            <h1>{t('General')}</h1>
            <CheckboxSetting id={enableWishlistForLoggedOnlyId}
                             label={t(enableWishlistForLoggedOnlyLabel)}
                             value={enableWishlistForLoggedOnlyValue}
                             onChangeValue={commitSettingValue}
            />
            <CheckboxSetting id={enableMultiWishlistSupportId}
                             label={t(enableMultiWishlistSupportLabel)}
                             value={enableMultiWishlistSupportValue}
                             onChangeValue={commitSettingValue}
            />
        </div>
    );
};

export default GeneralTab;
