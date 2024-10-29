import {WishlistsApi} from "ApiClientLib/apis/wishlists-api";
import {ItemsOfWishlistApi} from "ApiClientLib/apis/items-of-wishlist-api";
import {BASE_PATH} from "ApiClientLib/base";
import {AppConfig} from "AppConfig";

const config = new AppConfig();

let defaultConfig = {
    host: config.getConfig().host,
    wpNonce: config.getConfig().wpNonce,
    wcNonce: config.getConfig().wcNonce,
};

export class ApiClient {
    constructor(config = defaultConfig) {
        const configuration = {
            basePath: BASE_PATH.replace("http://localhost/", config.host),
            baseOptions: {
                headers: {
                    "X-WP-Nonce": config.wpNonce,
                    "Nonce": config.wcNonce,
                }
            }
        }

        this.publicWishlistApi = new WishlistsApi(configuration);
        this.publicWishlistAndProductApi = new ItemsOfWishlistApi(configuration);
    }
}

