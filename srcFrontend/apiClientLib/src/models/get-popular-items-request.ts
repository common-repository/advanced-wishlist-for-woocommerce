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
import { Pagination } from './pagination';
import { Sorting } from './sorting';
/**
 * 
 * @export
 * @interface GetPopularItemsRequest
 */
export interface GetPopularItemsRequest {
    /**
     * 
     * @type {string}
     * @memberof GetPopularItemsRequest
     */
    timeRange?: string;
    /**
     * 
     * @type {string}
     * @memberof GetPopularItemsRequest
     */
    searchByText?: string;
    /**
     * 
     * @type {Pagination}
     * @memberof GetPopularItemsRequest
     */
    pagination?: Pagination;
    /**
     * Sorting>
     * @type {Array<Sorting>}
     * @memberof GetPopularItemsRequest
     */
    sorting?: Array<Sorting>;
}
