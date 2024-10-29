import {Button} from '@mui/material'
import React from 'react'
import {
    addToCartProductOfWishlistAsync,
} from 'Store/slices/productsOfWishlistSlice'
import {useDispatch} from "react-redux";
import {AppConfig} from "AppConfig";
import {getRefreshedFragments} from "Utils/cartFragments";

const AddToCartButton = (props) => {
    const {wishlistId, itemId, text, url, requiresSelection, lockInterfaceCallback} = props

    const dispatch = useDispatch()
    const config = new AppConfig()

    const addToCart = () => {
        let promise = lockInterfaceCallback(
            dispatch(
                addToCartProductOfWishlistAsync({
                    wishlistId: wishlistId,
                    relationshipId: itemId,
                    isDelete: config.getConfig().settings.remove_if_added_to_cart,
                })
            )
        ).then(getRefreshedFragments)

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
