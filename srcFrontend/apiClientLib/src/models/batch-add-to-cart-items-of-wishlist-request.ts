/* tslint:disable */
/* eslint-disable */
/**
 * Algol WC Wishlist
 * Algol WC Wishlist
 *
 * OpenAPI spec version: 1.0
 * 
 *
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen.git
 * Do not edit the class manually.
 */
/**
 * 
 * @export
 * @interface BatchAddToCartItemsOfWishlistRequest
 */
export interface BatchAddToCartItemsOfWishlistRequest {
    /**
     * 
     * @type {Array<number>}
     * @memberof BatchAddToCartItemsOfWishlistRequest
     */
    itemIds?: Array<number>;
    /**
     * 
     * @type {boolean}
     * @memberof BatchAddToCartItemsOfWishlistRequest
     */
    deleteAddedToCart?: boolean;
    /**
     * 
     * @type {boolean}
     * @memberof BatchAddToCartItemsOfWishlistRequest
     */
    withWcNotices?: boolean;
}
