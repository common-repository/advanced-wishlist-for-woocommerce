/* tslint:disable */
/* eslint-disable */
import {BASE_PATH} from "./src/base";
import {WishlistsApi, ItemsOfWishlistApi} from "./src";

export * from "./src/api";
export * from "./src/configuration";
export * from "./src/models";

let configuration = {};
// @ts-ignore
if (typeof awlApiLibConfig !== "undefined") {
  configuration = {
    // @ts-ignore
    basePath: BASE_PATH.replace("http://localhost/", awlApiLibConfig.host),
    baseOptions: {
      headers: {
        // @ts-ignore
        "X-WP-Nonce": awlApiLibConfig.nonce,
      }
    }
  }
}


// @ts-ignore
window.awlApi = {
  wishlistApi: new WishlistsApi(configuration),
  wishlistAndProductsApi: new ItemsOfWishlistApi(configuration),
}

