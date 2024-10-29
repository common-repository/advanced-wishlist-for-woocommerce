import React from "react";
import {AppConfig} from "AppConfig";

const config = new AppConfig();

function formatPrice(format, priceDecimals, priceAsNumber, currencySymbol) {
    return format.replace("%1$s", priceAsNumber.toFixed(priceDecimals)).replace("%2$s", currencySymbol)
}

export const Price = (props) => {
    const priceAsNumber = props.price;
    const format = config.getConfig().price.format;
    const priceDecimals = config.getConfig().price.decimals;
    const currencySymbol = config.getConfig().currency.symbol;

    return (
        <>{formatPrice(format, priceDecimals, priceAsNumber, currencySymbol)}</>
    )
}
