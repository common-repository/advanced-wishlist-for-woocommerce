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
 * @interface ItemOfWishlist
 */
export interface ItemOfWishlist {
    /**
     * 
     * @type {number}
     * @memberof ItemOfWishlist
     */
    id?: number;
    /**
     * 
     * @type {number}
     * @memberof ItemOfWishlist
     */
    wishlistId?: number;
    /**
     * 
     * @type {number}
     * @memberof ItemOfWishlist
     */
    productId?: number;
    /**
     * 
     * @type {number}
     * @memberof ItemOfWishlist
     */
    quantity?: number;
    /**
     * 
     * @type {any}
     * @memberof ItemOfWishlist
     */
    variation?: any;
    /**
     * 
     * @type {any}
     * @memberof ItemOfWishlist
     */
    cartItemData?: any;
    /**
     * 
     * @type {string}
     * @memberof ItemOfWishlist
     */
    createdAt?: string;
    /**
     * 
     * @type {number}
     * @memberof ItemOfWishlist
     */
    priority?: number;
    /**
     * 
     * @type {string}
     * @memberof ItemOfWishlist
     */
    wishlistUrl?: string;
}