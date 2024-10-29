import {Button} from '@mui/material'
import React from 'react'
import {
    addToCartProductOfWishlistAsync,
} from 'Store/slices/productsOfWishlistSlice'
import {useDispatch} from "react-redux";
import {AppConfig} from "AppConfig";
import {getRefreshedFragments} from "Utils/cartFragments";
import {formatString} from "Utils/formatString";
import {showErrorMessage, showSuccessMessage} from "BaseComponents/productsOfWishlistList/NotificationsBlock";
import {useTranslation} from "react-i18next";

const AddToCartButton = (props) => {
    const {wishlistId, row, itemId, text, url, requiresSelection, lockInterfaceCallback} = props

    const dispatch = useDispatch()
    const config = new AppConfig()
    const { t } = useTranslation()

    const addToCart = () => {
        let promise = dispatch(
            addToCartProductOfWishlistAsync({
                wishlistId: wishlistId,
                relationshipId: itemId,
                isDelete: config.getConfig().settings.remove_if_added_to_cart,
                isRedirect: config.getConfig().settings.redirect_to_cart,
            })
        );

        lockInterfaceCallback(promise);
        promise.then(getRefreshedFragments);

        if ( ! config.getConfig().settings.redirect_to_cart ) {
            promise.then((result) => {
                const message = result.payload.messages[0]
                if ( message.type === "success" ) {
                    showSuccessMessage(
                        formatString(t('"{title} has been added to your cart.'), {title: row.title})
                    );
                } else if ( message.type === "error" ) {
                    showErrorMessage(
                        formatString(t('Cannot add "{title}" to your cart. Reason: {reason}'), {title: row.title, reason: message.message})
                    );
                }

                return result
            });
        }


        if ( config.getConfig().settings.redirect_to_cart ) {
            promise.then(() => window.location.replace(config.getConfig().urls.cart));
        }
    }

    return (
        <>
            {!!requiresSelection
                ? <Button onClick={() => window.open(url, '_self')} style={{textTransform: 'none'}}>{text}</Button>
                : <Button onClick={addToCart} style={{textTransform: 'none'}}>{text}</Button>
            }
        </>
    );
}

export default AddToCartButton
