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
import globalAxios, { AxiosResponse, AxiosInstance, AxiosRequestConfig } from 'axios';
import { Configuration } from '../configuration';
// Some imports not used depending on template conditions
// @ts-ignore
import { BASE_PATH, COLLECTION_FORMATS, RequestArgs, BaseAPI, RequiredError } from '../base';
import { InlineResponse2003 } from '../models';
/**
 * AdminWCCouponsApi - axios parameter creator
 * @export
 */
export const AdminWCCouponsApiAxiosParamCreator = function (configuration?: Configuration) {
    return {
        /**
         * Returns coupons based on query
         * @param {string} term Limit results to those matching a string
         * @param {number} limit Maximum number of items to be returned in result set
         * @param {*} [options] Override http request option.
         * @throws {RequiredError}
         */
        adminGetWcCoupons: async (term: string, limit: number, options: AxiosRequestConfig = {}): Promise<RequestArgs> => {
            // verify required parameter 'term' is not null or undefined
            if (term === null || term === undefined) {
                throw new RequiredError('term','Required parameter term was null or undefined when calling adminGetWcCoupons.');
            }
            // verify required parameter 'limit' is not null or undefined
            if (limit === null || limit === undefined) {
                throw new RequiredError('limit','Required parameter limit was null or undefined when calling adminGetWcCoupons.');
            }
            const localVarPath = `/admin/wcCoupons`;
            // use dummy base URL string because the URL constructor only accepts absolute URLs.
            const localVarUrlObj = new URL(localVarPath, 'https://example.com');
            let baseOptions;
            if (configuration) {
                baseOptions = configuration.baseOptions;
            }
            const localVarRequestOptions :AxiosRequestConfig = { method: 'GET', ...baseOptions, ...options};
            const localVarHeaderParameter = {} as any;
            const localVarQueryParameter = {} as any;

            // authentication apiKey required
            if (configuration && configuration.apiKey) {
                const localVarApiKeyValue = typeof configuration.apiKey === 'function'
                    ? await configuration.apiKey("X-WP-Nonce")
                    : await configuration.apiKey;
                localVarHeaderParameter["X-WP-Nonce"] = localVarApiKeyValue;
            }

            if (term !== undefined) {
                localVarQueryParameter['term'] = term;
            }

            if (limit !== undefined) {
                localVarQueryParameter['limit'] = limit;
            }

            const query = new URLSearchParams(localVarUrlObj.search);
            for (const key in localVarQueryParameter) {
                query.set(key, localVarQueryParameter[key]);
            }
            for (const key in options.params) {
                query.set(key, options.params[key]);
            }
            localVarUrlObj.search = (new URLSearchParams(query)).toString();
            let headersFromBaseOptions = baseOptions && baseOptions.headers ? baseOptions.headers : {};
            localVarRequestOptions.headers = {...localVarHeaderParameter, ...headersFromBaseOptions, ...options.headers};

            return {
                url: localVarUrlObj.pathname + localVarUrlObj.search + localVarUrlObj.hash,
                options: localVarRequestOptions,
            };
        },
    }
};

/**
 * AdminWCCouponsApi - functional programming interface
 * @export
 */
export const AdminWCCouponsApiFp = function(configuration?: Configuration) {
    return {
        /**
         * Returns coupons based on query
         * @param {string} term Limit results to those matching a string
         * @param {number} limit Maximum number of items to be returned in result set
         * @param {*} [options] Override http request option.
         * @throws {RequiredError}
         */
        async adminGetWcCoupons(term: string, limit: number, options?: AxiosRequestConfig): Promise<(axios?: AxiosInstance, basePath?: string) => Promise<AxiosResponse<InlineResponse2003>>> {
            const localVarAxiosArgs = await AdminWCCouponsApiAxiosParamCreator(configuration).adminGetWcCoupons(term, limit, options);
            return (axios: AxiosInstance = globalAxios, basePath: string = BASE_PATH) => {
                const axiosRequestArgs :AxiosRequestConfig = {...localVarAxiosArgs.options, url: basePath + localVarAxiosArgs.url};
                return axios.request(axiosRequestArgs);
            };
        },
    }
};

/**
 * AdminWCCouponsApi - factory interface
 * @export
 */
export const AdminWCCouponsApiFactory = function (configuration?: Configuration, basePath?: string, axios?: AxiosInstance) {
    return {
        /**
         * Returns coupons based on query
         * @param {string} term Limit results to those matching a string
         * @param {number} limit Maximum number of items to be returned in result set
         * @param {*} [options] Override http request option.
         * @throws {RequiredError}
         */
        async adminGetWcCoupons(term: string, limit: number, options?: AxiosRequestConfig): Promise<AxiosResponse<InlineResponse2003>> {
            return AdminWCCouponsApiFp(configuration).adminGetWcCoupons(term, limit, options).then((request) => request(axios, basePath));
        },
    };
};

/**
 * AdminWCCouponsApi - object-oriented interface
 * @export
 * @class AdminWCCouponsApi
 * @extends {BaseAPI}
 */
export class AdminWCCouponsApi extends BaseAPI {
    /**
     * Returns coupons based on query
     * @param {string} term Limit results to those matching a string
     * @param {number} limit Maximum number of items to be returned in result set
     * @param {*} [options] Override http request option.
     * @throws {RequiredError}
     * @memberof AdminWCCouponsApi
     */
    public async adminGetWcCoupons(term: string, limit: number, options?: AxiosRequestConfig) : Promise<AxiosResponse<InlineResponse2003>> {
        return AdminWCCouponsApiFp(this.configuration).adminGetWcCoupons(term, limit, options).then((request) => request(this.axios, this.basePath));
    }
}
