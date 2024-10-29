import React from 'react';
import {useTranslation} from 'react-i18next';
import ProductBackInStockEmailSetting from "Components/tabs/settings/components/ProductBackInStockEmailSetting";
import ProductPriceDecreaseEmailSetting from "Components/tabs/settings/components/ProductPriceDecreaseEmailSetting";
import {
    getSettingCustomizeUrl,
    getSettingLabel,
    getSettingValue,
    commitSettingValue
} from "Components/tabs/settings/helpers";

const ProductNotificationsTab = (props) => {
    const {t} = useTranslation();

    const emailOnProductPriceDecreaseId = 'email_on_product_price_decrease';
    const emailOnProductPriceDecreaseValue = getSettingValue(props.settingsData, emailOnProductPriceDecreaseId);
    const emailOnProductPriceDecreaseLabel = getSettingLabel(props.settingsData, emailOnProductPriceDecreaseId);
    const emailOnProductPriceDecreaseCustomizeUrl = getSettingCustomizeUrl(props.settingsData, emailOnProductPriceDecreaseId);

    const emailProductBackInStockId = 'email_product_back_in_stock';
    const emailProductBackInStockValue = getSettingValue(props.settingsData, emailProductBackInStockId);
    const emailProductBackInStockLabel = getSettingLabel(props.settingsData, emailProductBackInStockId);
    const emailProductBackInStockCustomizeUrl = getSettingCustomizeUrl(props.settingsData, emailProductBackInStockId);

    return (
        <div>
            <h1>{t('Product notifications')}</h1>
            <ProductPriceDecreaseEmailSetting label={t(emailOnProductPriceDecreaseLabel)}
                                              value={emailOnProductPriceDecreaseValue}
                                              customizeUrl={emailOnProductPriceDecreaseCustomizeUrl}
                                              onChangeValue={commitSettingValue}
            />
            <ProductBackInStockEmailSetting label={t(emailProductBackInStockLabel)}
                                            value={emailProductBackInStockValue}
                                            customizeUrl={emailProductBackInStockCustomizeUrl}
                                            onChangeValue={commitSettingValue}
            />
        </div>
    );
};

export default ProductNotificationsTab;
