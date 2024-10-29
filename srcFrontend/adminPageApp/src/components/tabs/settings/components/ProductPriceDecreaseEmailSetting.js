import React from 'react';
import {useTranslation} from 'react-i18next';
import CheckboxSetting from './CheckboxSetting';

const ProductPriceDecreaseEmailSetting = (props) => {
    const {t} = useTranslation();

    let config = {
        value: null,
        label: '',
        customizeUrl: '',
        onChangeValue: () => void 0,
        ...props,
        id: 'email_on_product_price_decrease',
    }

    let customizeLink = config.customizeUrl ? <a href={config.customizeUrl}>{t('Customize Email')}</a> : "";

    return (
        <div>
            <CheckboxSetting id={config.id}
                             label={config.label}
                             value={config.value}
                             onChangeValue={config.onChangeValue}
            />
            {customizeLink}
        </div>
    );
};

export default ProductPriceDecreaseEmailSetting;
