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
import { PopularItem } from './popular-item';
/**
 * 
 * @export
 * @interface PopularItemsResponse
 */
export interface PopularItemsResponse {
    /**
     * PopularItem>
     * @type {Array<PopularItem>}
     * @memberof PopularItemsResponse
     */
    items?: Array<PopularItem>;
    /**
     * 
     * @type {number}
     * @memberof PopularItemsResponse
     */
    total?: number;
}