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
import { User } from './user';
/**
 * 
 * @export
 * @interface AdminGetWishlistsElement
 */
export interface AdminGetWishlistsElement {
    /**
     * 
     * @type {number}
     * @memberof AdminGetWishlistsElement
     */
    id?: number;
    /**
     * 
     * @type {string}
     * @memberof AdminGetWishlistsElement
     */
    title?: string;
    /**
     * 
     * @type {string}
     * @memberof AdminGetWishlistsElement
     */
    token?: string;
    /**
     * 
     * @type {User}
     * @memberof AdminGetWishlistsElement
     */
    owner?: User;
    /**
     * 
     * @type {number}
     * @memberof AdminGetWishlistsElement
     */
    shareTypeId?: number;
    /**
     * 
     * @type {string}
     * @memberof AdminGetWishlistsElement
     */
    dateCreated?: string;
    /**
     * 
     * @type {number}
     * @memberof AdminGetWishlistsElement
     */
    itemsCount?: number;
}
