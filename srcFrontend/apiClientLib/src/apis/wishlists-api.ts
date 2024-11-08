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
import { Wishlist } from '../models';
import { WishlistRequest } from '../models';
/**
 * WishlistsApi - axios parameter creator
 * @export
 */
export const WishlistsApiAxiosParamCreator = function (configuration?: Configuration) {
    return {
        /**
         * Add a new wishlist
         * @param {WishlistRequest} body Wishlist object that needs to be added
         * @param {*} [options] Override http request option.
         * @throws {RequiredError}
         */
        addWishlist: async (body: WishlistRequest, options: AxiosRequestConfig = {}): Promise<RequestArgs> => {
            // verify required parameter 'body' is not null or undefined
            if (body === null || body === undefined) {
                throw new RequiredError('body','Required parameter body was null or undefined when calling addWishlist.');
            }
            const localVarPath = `/wishlists`;
            // use dummy base URL string because the URL constructor only accepts absolute URLs.
            const localVarUrlObj = new URL(localVarPath, 'https://example.com');
            let baseOptions;
            if (configuration) {
                baseOptions = configuration.baseOptions;
            }
            const localVarRequestOptions :AxiosRequestConfig = { method: 'POST', ...baseOptions, ...options};
            const localVarHeaderParameter = {} as any;
            const localVarQueryParameter = {} as any;

            // authentication apiKey required
            if (configuration && configuration.apiKey) {
                const localVarApiKeyValue = typeof configuration.apiKey === 'function'
                    ? await configuration.apiKey("X-WP-Nonce")
                    : await configuration.apiKey;
                localVarHeaderParameter["X-WP-Nonce"] = localVarApiKeyValue;
            }

            localVarHeaderParameter['Content-Type'] = 'application/json';

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
            const needsSerialization = (typeof body !== "string") || localVarRequestOptions.headers['Content-Type'] === 'application/json';
            localVarRequestOptions.data =  needsSerialization ? JSON.stringify(body !== undefined ? body : {}) : (body || "");

            return {
                url: localVarUrlObj.pathname + localVarUrlObj.search + localVarUrlObj.hash,
                options: localVarRequestOptions,
            };
        },
        /**
         * Delete the wishlist
         * @param {number} wishlistId ID of wishlist to return
         * @param {*} [options] Override http request option.
         * @throws {RequiredError}
         */
        deleteWishlist: async (wishlistId: number, options: AxiosRequestConfig = {}): Promise<RequestArgs> => {
            // verify required parameter 'wishlistId' is not null or undefined
            if (wishlistId === null || wishlistId === undefined) {
                throw new RequiredError('wishlistId','Required parameter wishlistId was null or undefined when calling deleteWishlist.');
            }
            const localVarPath = `/wishlists/{wishlistId}`
                .replace(`{${"wishlistId"}}`, encodeURIComponent(String(wishlistId)));
            // use dummy base URL string because the URL constructor only accepts absolute URLs.
            const localVarUrlObj = new URL(localVarPath, 'https://example.com');
            let baseOptions;
            if (configuration) {
                baseOptions = configuration.baseOptions;
            }
            const localVarRequestOptions :AxiosRequestConfig = { method: 'DELETE', ...baseOptions, ...options};
            const localVarHeaderParameter = {} as any;
            const localVarQueryParameter = {} as any;

            // authentication apiKey required
            if (configuration && configuration.apiKey) {
                const localVarApiKeyValue = typeof configuration.apiKey === 'function'
                    ? await configuration.apiKey("X-WP-Nonce")
                    : await configuration.apiKey;
                localVarHeaderParameter["X-WP-Nonce"] = localVarApiKeyValue;
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
        /**
         * Get all wishlists
         * @param {*} [options] Override http request option.
         * @throws {RequiredError}
         */
        getAllWishlists: async (options: AxiosRequestConfig = {}): Promise<RequestArgs> => {
            const localVarPath = `/wishlists`;
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
        /**
         * Returns a single wishlist
         * @param {number} wishlistId ID of wishlist to return
         * @param {*} [options] Override http request option.
         * @throws {RequiredError}
         */
        getWishlistById: async (wishlistId: number, options: AxiosRequestConfig = {}): Promise<RequestArgs> => {
            // verify required parameter 'wishlistId' is not null or undefined
            if (wishlistId === null || wishlistId === undefined) {
                throw new RequiredError('wishlistId','Required parameter wishlistId was null or undefined when calling getWishlistById.');
            }
            const localVarPath = `/wishlists/{wishlistId}`
                .replace(`{${"wishlistId"}}`, encodeURIComponent(String(wishlistId)));
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
        /**
         * Update the wishlist
         * @param {WishlistRequest} body Wishlist object
         * @param {*} [options] Override http request option.
         * @throws {RequiredError}
         */
        updateWishlist: async (body: WishlistRequest, options: AxiosRequestConfig = {}): Promise<RequestArgs> => {
            // verify required parameter 'body' is not null or undefined
            if (body === null || body === undefined) {
                throw new RequiredError('body','Required parameter body was null or undefined when calling updateWishlist.');
            }
            const localVarPath = `/wishlists`;
            // use dummy base URL string because the URL constructor only accepts absolute URLs.
            const localVarUrlObj = new URL(localVarPath, 'https://example.com');
            let baseOptions;
            if (configuration) {
                baseOptions = configuration.baseOptions;
            }
            const localVarRequestOptions :AxiosRequestConfig = { method: 'PUT', ...baseOptions, ...options};
            const localVarHeaderParameter = {} as any;
            const localVarQueryParameter = {} as any;

            // authentication apiKey required
            if (configuration && configuration.apiKey) {
                const localVarApiKeyValue = typeof configuration.apiKey === 'function'
                    ? await configuration.apiKey("X-WP-Nonce")
                    : await configuration.apiKey;
                localVarHeaderParameter["X-WP-Nonce"] = localVarApiKeyValue;
            }

            localVarHeaderParameter['Content-Type'] = 'application/json';

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
            const needsSerialization = (typeof body !== "string") || localVarRequestOptions.headers['Content-Type'] === 'application/json';
            localVarRequestOptions.data =  needsSerialization ? JSON.stringify(body !== undefined ? body : {}) : (body || "");

            return {
                url: localVarUrlObj.pathname + localVarUrlObj.search + localVarUrlObj.hash,
                options: localVarRequestOptions,
            };
        },
    }
};

/**
 * WishlistsApi - functional programming interface
 * @export
 */
export const WishlistsApiFp = function(configuration?: Configuration) {
    return {
        /**
         * Add a new wishlist
         * @param {WishlistRequest} body Wishlist object that needs to be added
         * @param {*} [options] Override http request option.
         * @throws {RequiredError}
         */
        async addWishlist(body: WishlistRequest, options?: AxiosRequestConfig): Promise<(axios?: AxiosInstance, basePath?: string) => Promise<AxiosResponse<Wishlist>>> {
            const localVarAxiosArgs = await WishlistsApiAxiosParamCreator(configuration).addWishlist(body, options);
            return (axios: AxiosInstance = globalAxios, basePath: string = BASE_PATH) => {
                const axiosRequestArgs :AxiosRequestConfig = {...localVarAxiosArgs.options, url: basePath + localVarAxiosArgs.url};
                return axios.request(axiosRequestArgs);
            };
        },
        /**
         * Delete the wishlist
         * @param {number} wishlistId ID of wishlist to return
         * @param {*} [options] Override http request option.
         * @throws {RequiredError}
         */
        async deleteWishlist(wishlistId: number, options?: AxiosRequestConfig): Promise<(axios?: AxiosInstance, basePath?: string) => Promise<AxiosResponse<void>>> {
            const localVarAxiosArgs = await WishlistsApiAxiosParamCreator(configuration).deleteWishlist(wishlistId, options);
            return (axios: AxiosInstance = globalAxios, basePath: string = BASE_PATH) => {
                const axiosRequestArgs :AxiosRequestConfig = {...localVarAxiosArgs.options, url: basePath + localVarAxiosArgs.url};
                return axios.request(axiosRequestArgs);
            };
        },
        /**
         * Get all wishlists
         * @param {*} [options] Override http request option.
         * @throws {RequiredError}
         */
        async getAllWishlists(options?: AxiosRequestConfig): Promise<(axios?: AxiosInstance, basePath?: string) => Promise<AxiosResponse<Array<Wishlist>>>> {
            const localVarAxiosArgs = await WishlistsApiAxiosParamCreator(configuration).getAllWishlists(options);
            return (axios: AxiosInstance = globalAxios, basePath: string = BASE_PATH) => {
                const axiosRequestArgs :AxiosRequestConfig = {...localVarAxiosArgs.options, url: basePath + localVarAxiosArgs.url};
                return axios.request(axiosRequestArgs);
            };
        },
        /**
         * Returns a single wishlist
         * @param {number} wishlistId ID of wishlist to return
         * @param {*} [options] Override http request option.
         * @throws {RequiredError}
         */
        async getWishlistById(wishlistId: number, options?: AxiosRequestConfig): Promise<(axios?: AxiosInstance, basePath?: string) => Promise<AxiosResponse<Wishlist>>> {
            const localVarAxiosArgs = await WishlistsApiAxiosParamCreator(configuration).getWishlistById(wishlistId, options);
            return (axios: AxiosInstance = globalAxios, basePath: string = BASE_PATH) => {
                const axiosRequestArgs :AxiosRequestConfig = {...localVarAxiosArgs.options, url: basePath + localVarAxiosArgs.url};
                return axios.request(axiosRequestArgs);
            };
        },
        /**
         * Update the wishlist
         * @param {WishlistRequest} body Wishlist object
         * @param {*} [options] Override http request option.
         * @throws {RequiredError}
         */
        async updateWishlist(body: WishlistRequest, options?: AxiosRequestConfig): Promise<(axios?: AxiosInstance, basePath?: string) => Promise<AxiosResponse<Wishlist>>> {
            const localVarAxiosArgs = await WishlistsApiAxiosParamCreator(configuration).updateWishlist(body, options);
            return (axios: AxiosInstance = globalAxios, basePath: string = BASE_PATH) => {
                const axiosRequestArgs :AxiosRequestConfig = {...localVarAxiosArgs.options, url: basePath + localVarAxiosArgs.url};
                return axios.request(axiosRequestArgs);
            };
        },
    }
};

/**
 * WishlistsApi - factory interface
 * @export
 */
export const WishlistsApiFactory = function (configuration?: Configuration, basePath?: string, axios?: AxiosInstance) {
    return {
        /**
         * Add a new wishlist
         * @param {WishlistRequest} body Wishlist object that needs to be added
         * @param {*} [options] Override http request option.
         * @throws {RequiredError}
         */
        async addWishlist(body: WishlistRequest, options?: AxiosRequestConfig): Promise<AxiosResponse<Wishlist>> {
            return WishlistsApiFp(configuration).addWishlist(body, options).then((request) => request(axios, basePath));
        },
        /**
         * Delete the wishlist
         * @param {number} wishlistId ID of wishlist to return
         * @param {*} [options] Override http request option.
         * @throws {RequiredError}
         */
        async deleteWishlist(wishlistId: number, options?: AxiosRequestConfig): Promise<AxiosResponse<void>> {
            return WishlistsApiFp(configuration).deleteWishlist(wishlistId, options).then((request) => request(axios, basePath));
        },
        /**
         * Get all wishlists
         * @param {*} [options] Override http request option.
         * @throws {RequiredError}
         */
        async getAllWishlists(options?: AxiosRequestConfig): Promise<AxiosResponse<Array<Wishlist>>> {
            return WishlistsApiFp(configuration).getAllWishlists(options).then((request) => request(axios, basePath));
        },
        /**
         * Returns a single wishlist
         * @param {number} wishlistId ID of wishlist to return
         * @param {*} [options] Override http request option.
         * @throws {RequiredError}
         */
        async getWishlistById(wishlistId: number, options?: AxiosRequestConfig): Promise<AxiosResponse<Wishlist>> {
            return WishlistsApiFp(configuration).getWishlistById(wishlistId, options).then((request) => request(axios, basePath));
        },
        /**
         * Update the wishlist
         * @param {WishlistRequest} body Wishlist object
         * @param {*} [options] Override http request option.
         * @throws {RequiredError}
         */
        async updateWishlist(body: WishlistRequest, options?: AxiosRequestConfig): Promise<AxiosResponse<Wishlist>> {
            return WishlistsApiFp(configuration).updateWishlist(body, options).then((request) => request(axios, basePath));
        },
    };
};

/**
 * WishlistsApi - object-oriented interface
 * @export
 * @class WishlistsApi
 * @extends {BaseAPI}
 */
export class WishlistsApi extends BaseAPI {
    /**
     * Add a new wishlist
     * @param {WishlistRequest} body Wishlist object that needs to be added
     * @param {*} [options] Override http request option.
     * @throws {RequiredError}
     * @memberof WishlistsApi
     */
    public async addWishlist(body: WishlistRequest, options?: AxiosRequestConfig) : Promise<AxiosResponse<Wishlist>> {
        return WishlistsApiFp(this.configuration).addWishlist(body, options).then((request) => request(this.axios, this.basePath));
    }
    /**
     * Delete the wishlist
     * @param {number} wishlistId ID of wishlist to return
     * @param {*} [options] Override http request option.
     * @throws {RequiredError}
     * @memberof WishlistsApi
     */
    public async deleteWishlist(wishlistId: number, options?: AxiosRequestConfig) : Promise<AxiosResponse<void>> {
        return WishlistsApiFp(this.configuration).deleteWishlist(wishlistId, options).then((request) => request(this.axios, this.basePath));
    }
    /**
     * Get all wishlists
     * @param {*} [options] Override http request option.
     * @throws {RequiredError}
     * @memberof WishlistsApi
     */
    public async getAllWishlists(options?: AxiosRequestConfig) : Promise<AxiosResponse<Array<Wishlist>>> {
        return WishlistsApiFp(this.configuration).getAllWishlists(options).then((request) => request(this.axios, this.basePath));
    }
    /**
     * Returns a single wishlist
     * @param {number} wishlistId ID of wishlist to return
     * @param {*} [options] Override http request option.
     * @throws {RequiredError}
     * @memberof WishlistsApi
     */
    public async getWishlistById(wishlistId: number, options?: AxiosRequestConfig) : Promise<AxiosResponse<Wishlist>> {
        return WishlistsApiFp(this.configuration).getWishlistById(wishlistId, options).then((request) => request(this.axios, this.basePath));
    }
    /**
     * Update the wishlist
     * @param {WishlistRequest} body Wishlist object
     * @param {*} [options] Override http request option.
     * @throws {RequiredError}
     * @memberof WishlistsApi
     */
    public async updateWishlist(body: WishlistRequest, options?: AxiosRequestConfig) : Promise<AxiosResponse<Wishlist>> {
        return WishlistsApiFp(this.configuration).updateWishlist(body, options).then((request) => request(this.axios, this.basePath));
    }
}
