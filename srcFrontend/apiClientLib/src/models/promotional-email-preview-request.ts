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
import { Product } from './product';
/**
 * 
 * @export
 * @interface PromotionalEmailPreviewRequest
 */
export interface PromotionalEmailPreviewRequest {
    /**
     * 
     * @type {string}
     * @memberof PromotionalEmailPreviewRequest
     */
    type?: string;
    /**
     * 
     * @type {string}
     * @memberof PromotionalEmailPreviewRequest
     */
    htmlContent?: string;
    /**
     * 
     * @type {string}
     * @memberof PromotionalEmailPreviewRequest
     */
    plainContent?: string;
    /**
     * 
     * @type {Product}
     * @memberof PromotionalEmailPreviewRequest
     */
    product?: Product;
    /**
     * 
     * @type {number}
     * @memberof PromotionalEmailPreviewRequest
     */
    userId?: number;
    /**
     * 
     * @type {string}
     * @memberof PromotionalEmailPreviewRequest
     */
    coupon?: string;
}