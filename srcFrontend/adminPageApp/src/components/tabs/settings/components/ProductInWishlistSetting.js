import React from 'react';
import {useTranslation} from 'react-i18next';
import {FormControl, FormControlLabel, FormLabel, Radio, RadioGroup} from "@mui/material";

const ProductInWishlistSetting = (props) => {
    const {t} = useTranslation();

    let config = {
        value: null,
        label: '',
        onChangeValue: () => void 0,
        ...props,
        id: 'product_in_wishlist',
    }

    return (
        <div>
            <FormControl>
                <FormLabel>{config.label}</FormLabel>
                <RadioGroup
                    row
                    id={config.id}
                    name={config.id}
                    value={config.value}
                    onChange={(e) => config.onChangeValue(config.id, e.target.value)}
                >
                    <FormControlLabel value='show_remove_from_wishlist'
                                      control={<Radio/>}
                                      label={t('Show Remove from Wishlist')}
                    />
                    <FormControlLabel value='show_view_wishlist'
                                      control={<Radio/>}
                                      label={t('Show View Wishlist')}
                    />
                </RadioGroup>
            </FormControl>
        </div>
    );
};

export default ProductInWishlistSetting;
