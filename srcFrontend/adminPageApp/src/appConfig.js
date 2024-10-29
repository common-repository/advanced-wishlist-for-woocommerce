let defaultConfig = {
    host: "http://localhost/",
    nonce: "",
    popularProductsTab: {
        itemsPerPage: 5,
    },
    wishlistsTab: {
        itemsPerPage: 3,
    }
}

if (typeof algolWishlistAdminAppData !== "undefined") {
    defaultConfig = {...defaultConfig, ...algolWishlistAdminAppData}
}

export class AppConfig {
    constructor(config = defaultConfig) {
        this.config = config;
    }

    getConfig() {
        return this.config;
    }
}