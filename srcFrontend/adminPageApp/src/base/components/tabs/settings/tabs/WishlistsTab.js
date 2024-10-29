import React from 'react';
import {Trans, useTranslation} from 'react-i18next';
import CheckboxSetting from "Components/tabs/settings/components/CheckboxSetting";
import SelectSetting from "Components/tabs/settings/components/SelectSetting";
import {
    getSettingCustomizeUrl,
    getSettingLabel,
    getSettingValue,
    commitSettingValue,
    getSettingSelections
} from "Components/tabs/settings/helpers";

const WishlistsTab = (props) => {
    const {t} = useTranslation();

    const wishlistPageId = 'wishlist_page';
    const wishlistPageValue = getSettingValue(props.settingsData, wishlistPageId);
    const wishlistPageLabel = getSettingLabel(props.settingsData, wishlistPageId);
    const wishlistPageOptions = {};
    getSettingSelections(props.settingsData, wishlistPageId).forEach((value) => wishlistPageOptions[value.value] = value.title)
    const wishlistPageCustomizeUrl = getSettingCustomizeUrl(props.settingsData, wishlistPageId);

    const redirectToCartId = 'redirect_to_cart';
    const redirectToCartValue = getSettingValue(props.settingsData, redirectToCartId);
    const redirectToCartLabel = getSettingLabel(props.settingsData, redirectToCartId);

    const removeIfAddedToCartId = 'remove_if_added_to_cart';
    const removeIfAddedToCartValue = getSettingValue(props.settingsData, removeIfAddedToCartId);
    const removeIfAddedToCartLabel = getSettingLabel(props.settingsData, removeIfAddedToCartId);

    const shareWishlistId = 'share_wishlist';
    const shareWishlistValue = getSettingValue(props.settingsData, shareWishlistId);
    const shareWishlistLabel = getSettingLabel(props.settingsData, shareWishlistId);
    const shareWishlistCustomizeUrl = getSettingCustomizeUrl(props.settingsData, shareWishlistId);

    return (
        <div>
            <h1>{t('Wishlists')}</h1>
            <SelectSetting id={wishlistPageId}
                           label={t(wishlistPageLabel)}
                           options={wishlistPageOptions}
                           value={wishlistPageValue}
                           customizeUrl={wishlistPageCustomizeUrl}
                           onChangeValue={commitSettingValue}
            />
            <p><Trans>Pick a page as the main Wishlist page; make sure you add the <span className="code"><code>[awl_products_of_wishlist]</code></span> shortcode into the page content</Trans></p>
            <CheckboxSetting id={redirectToCartId}
                             label={t(redirectToCartLabel)}
                             value={redirectToCartValue}
                             onChangeValue={commitSettingValue}
            />
            <CheckboxSetting id={removeIfAddedToCartId}
                             label={t(removeIfAddedToCartLabel)}
                             value={removeIfAddedToCartValue}
                             onChangeValue={commitSettingValue}
            />
            <CheckboxSetting id={shareWishlistId}
                             label={t(shareWishlistLabel)}
                             customizeUrl={shareWishlistCustomizeUrl}
                             value={shareWishlistValue}
                             onChangeValue={commitSettingValue}
            />
        </div>
    );
};

export default WishlistsTab;
