let defaultConfig = {
    host: "http://localhost/",
    wpNonce: "",
    wcNonce: "",
    productsOfWishlist: {

    },

    price: {
        decimals: 2,
        format: "%1$s%2$s",
    },
    currency: {
        code: "USD",
        symbol: "$",
    },
    settings: {
        remove_if_added_to_cart: false,
        redirect_to_cart: false,
        share_wishlist: true,
    },
    urls: {
        cart: "",
    },
    columns: {
        icon: true,
        price: true,
        qty: true,
        stock: true,
        actions: true,
    },
    share: {
        url: '',
        facebookUrl: '',
        twitterUrl: '',
        pinterestUrl: '',
        emailUrl: '',
        whatsappUrl: '',
        shareBlockTitle: '',
        shareBlockPosition: 'after_bulk_actions',
        facebookShareIcon: '',
        facebookShareIconCustom: '',
        twitterShareIcon: '',
        twitterShareIconCustom: '',
        pinterestShareIcon: '',
        pinterestShareIconCustom: '',
        emailShareIcon: '',
        emailShareIconCustom: '',
        whatsappShareIcon: '',
        whatsappShareIconCustom: '',
    }
}

if (typeof algolWishlistClientAppData !== "undefined") {
    defaultConfig = {...defaultConfig, ...algolWishlistClientAppData}
}

export class AppConfig {
    constructor(config = defaultConfig) {
        this.config = config;
    }

    getConfig() {
        return this.config;
    }
}
