import {AdminWishlistsApi} from "ApiClientLib/apis/admin-wishlists-api";
import {SettingsApi} from "ApiClientLib/apis/settings-api";
import {AdminPromotionalEmailApi} from "ApiClientLib/apis/admin-promotional-email-api";
import {AdminWCCouponsApi} from "ApiClientLib/apis/admin-wccoupons-api";
import {AdminPopularProductsApi} from "ApiClientLib/apis/admin-popular-products-api";
import {AppConfig} from "AppConfig";
import {BASE_PATH} from "ApiClientLib/base";

const config = new AppConfig();

let defaultConfig = {
    host: config.getConfig().host,
    nonce: config.getConfig().nonce,
};

export class ApiClient {
    constructor(config = defaultConfig) {
        const configuration = {
            basePath: BASE_PATH.replace("http://localhost/", config.host),
            baseOptions: {
                headers: {
                    "X-WP-Nonce": config.nonce,
                }
            }
        }

        this.popularProductApi = new AdminPopularProductsApi(configuration);
        this.wishlistApi = new AdminWishlistsApi(configuration);
        this.settingsApi = new SettingsApi(configuration);
        this.promotionalEmailApi = new AdminPromotionalEmailApi(configuration);
        this.wcCouponsApi = new AdminWCCouponsApi(configuration);
    }
}

