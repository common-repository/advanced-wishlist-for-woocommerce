import React from 'react';
import {useTranslation} from 'react-i18next';
import CheckboxSetting from "Components/tabs/settings/components/CheckboxSetting";
import ProductInWishlistSetting from "Components/tabs/settings/components/ProductInWishlistSetting";
import {
    getSettingCustomizeUrl,
    getSettingLabel,
    getSettingValue,
    commitSettingValue
} from "Components/tabs/settings/helpers";

const AddToWishlistTab = (props) => {
    const {t} = useTranslation();

    const showAtProductPageId = 'show_at_product_page';
    const showAtProductPageValue = getSettingValue(props.settingsData, showAtProductPageId);
    const showAtProductPageLabel = getSettingLabel(props.settingsData, showAtProductPageId);
    const showAtProductPageCustomizeUrl = getSettingCustomizeUrl(props.settingsData, showAtProductPageId);

    const showAtShopPagesId = 'show_at_shop_pages';
    const showAtShopPagesValue = getSettingValue(props.settingsData, showAtShopPagesId);
    const showAtShopPagesLabel = getSettingLabel(props.settingsData, showAtShopPagesId);
    const showAtShopPagesCustomizeUrl = getSettingCustomizeUrl(props.settingsData, showAtShopPagesId);

    const productInWishlistId = 'product_in_wishlist';
    const productInWishlistValue = getSettingValue(props.settingsData, productInWishlistId);
    const productInWishlistLabel = getSettingLabel(props.settingsData, productInWishlistId);

    return (
        <div>
            <h1>{t('Add to wishlist')}</h1>
            <CheckboxSetting id={showAtProductPageId}
                             label={t(showAtProductPageLabel)}
                             customizeUrl={showAtProductPageCustomizeUrl}
                             value={showAtProductPageValue}
                             onChangeValue={commitSettingValue}
            />
            <CheckboxSetting id={showAtShopPagesId}
                             label={t(showAtShopPagesLabel)}
                             customizeUrl={showAtShopPagesCustomizeUrl}
                             value={showAtShopPagesValue}
                             onChangeValue={commitSettingValue}
            />
            <ProductInWishlistSetting label={t(productInWishlistLabel)}
                                      value={productInWishlistValue}
                                      onChangeValue={commitSettingValue}
            />
        </div>
    );
};

export default AddToWishlistTab;
