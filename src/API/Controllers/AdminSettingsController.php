<?php

namespace AlgolWishlist\API\Controllers;

use AlgolWishlist\API\DTO\Option;
use AlgolWishlist\CustomizerExtensions\ProductPageButtonProperties;
use AlgolWishlist\CustomizerExtensions\ShopLoopButtonProperties;
use AlgolWishlist\CustomizerExtensions\ExactWishlistProperties;
use AlgolWishlist\Settings\SettingsFramework\OptionsManager;
use AlgolWishlist\Settings\SettingsFramework\Varieties\Option\BooleanOption;
use AlgolWishlist\Settings\SettingsFramework\Varieties\Option\IntegerNumberOption;
use AlgolWishlist\Settings\SettingsFramework\Varieties\Option\ShortTextOption;
use AlgolWishlist\Settings\SettingsFramework\Varieties\SelectiveOption\SelectiveOption;
use AlgolWishlist\Settings\SettingsFramework\Varieties\SelectiveOption\SelectiveOptionWithCallback;
use AlgolWishlist\UrlBuilder;
use OpenApi\Annotations as OA;
use WP_Http;
use WP_REST_Request;
use WP_REST_Response;

class AdminSettingsController
{
    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $resourceName;

    /**
     * @var OptionsManager
     */
    protected $optionsManager;

    public function __construct()
    {
        $this->namespace = 'algol-wishlist/v1/admin';
        $this->optionsManager = awlContext()->getSettings();
    }

    public function registerRoutes()
    {
        register_rest_route($this->namespace, '/settings', [
            [
                'methods' => "GET",
                'callback' => array($this, 'getItems'),
                'permission_callback' => [$this, 'canManageWoocommerce'],
            ],
            [
                'methods' => "POST",
                'callback' => array($this, 'updateItems'),
                'permission_callback' => [$this, 'canManageWoocommerce'],
            ],
        ]);
    }

    public function canManageWoocommerce(): bool
    {
        return current_user_can('manage_woocommerce');
    }

    /**
     * @OA\Get(
     *     path="/admin/settings",
     *     tags={"Settings"},
     *     description="Get all settings",
     *     operationId="getAllSettings",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Option")),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Error",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     security={
     *        {"apiKey": {}}
     *     }
     * )
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function getItems(WP_REST_Request $request)
    {
        $optionValues = $this->optionsManager->getOptions();

        $data = $this->convertOptionsForResponse($optionValues);

        return new WP_REST_Response($data, WP_Http::OK);
    }

    /**
     * @OA\Post(
     *     path="/admin/settings",
     *     tags={"Settings"},
     *     description="Update settings",
     *     operationId="updateAllSettings",
     *     @OA\RequestBody(
     *         description="Options",
     *         required=true,
     *         @OA\JsonContent(type="object"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Option")),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Error",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     security={
     *        {"apiKey": {}}
     *     }
     * )
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function updateItems(WP_REST_Request $request)
    {
        $body = $request->get_json_params();

        foreach (array_keys($this->optionsManager->getOptions()) as $key) {
            $option = $this->optionsManager->tryGetOption($key);

            if ($option) {
                if (isset($body[$key])) {
                    $option->set($body[$key]);
                }
            }
        }

        $this->optionsManager->save();

        $optionValues = $this->optionsManager->getOptions();
        $data = $this->convertOptionsForResponse($optionValues);

        return new WP_REST_Response($data, WP_Http::OK);
    }

    /**
     * @param array<string, mixed> $optionValues
     * @return array<int, Option>
     */
    private function convertOptionsForResponse(array $optionValues): array
    {
        $dtoOptions = [];
        foreach ($optionValues as $optionKey => $optionValue) {
            $option = $this->optionsManager->tryGetOption($optionKey);

            if ($option instanceof BooleanOption) {
                $dtoOptions[] = new Option(
                    "boolean",
                    $optionKey,
                    $option->getTitle(),
                    $option->getDefault(),
                    $optionValue
                );
            } elseif ($option instanceof ShortTextOption) {
                $dtoOptions[] = new Option(
                    "shortText",
                    $optionKey,
                    $option->getTitle(),
                    $option->getDefault(),
                    $optionValue
                );
            } elseif ($option instanceof IntegerNumberOption) {
                $dtoOptions[] = new Option(
                    "integer",
                    $optionKey,
                    $option->getTitle(),
                    $option->getDefault(),
                    $optionValue
                );
            } elseif ($option instanceof SelectiveOption) {
                $dtoOption = new Option(
                    "selective",
                    $optionKey,
                    $option->getTitle(),
                    $option->getDefault(),
                    $optionValue
                );

                $dtoOption->selections = array_map(function ($selection) {
                    return [
                        "title" => $selection->getTitle(),
                        "value" => $selection->getValue(),
                    ];
                }, $option->getSelections());

                $dtoOptions[] = $dtoOption;
            } elseif ($option instanceof SelectiveOptionWithCallback) {
                $selections = array_map(function ($selection) {
                    return [
                        "title" => $selection->getTitle(),
                        "value" => $selection->getValue(),
                    ];
                }, $option->getSelections());

                $dtoOption = new Option(
                    "selective",
                    $optionKey,
                    $option->getTitle(),
                    $option->getDefault(),
                    $optionValue
                );

                $dtoOption->selections = $selections;

                $dtoOptions[] = $dtoOption;
            }
        }

        return $this->addCustomizeUrls($dtoOptions);
    }

    /**
     * @param array<int, Option> $data
     * @return array<int, Option>
     */
    private function addCustomizeUrls(array $data): array
    {
        $query = new \WC_Product_Query(array(
            'limit' => 1,
            'orderby' => 'date',
            'order' => 'DESC',
            'return' => 'objects',
        ));
        $lastProduct = ($products = $query->get_products()) ? $products[0] : null;

        $defaultWishlistPageUrl = (new UrlBuilder())->getUrlToDefaultWishlistForCurrentUser();

        $customizeUrls = [
            'show_at_product_page' => [
                'customizeUrl' => add_query_arg(
                    [
                        'return' => admin_url('themes.php'),
                        'autofocus[section]' => ProductPageButtonProperties::KEY,
                        'url' => $lastProduct ? $lastProduct->get_permalink() : "",
                    ],
                    admin_url('customize.php')
                )
            ],
            'show_at_shop_pages' => [
                'customizeUrl' => add_query_arg(
                    [
                        'return' => admin_url('themes.php'),
                        'autofocus[section]' => ShopLoopButtonProperties::KEY,
                        'url' => get_permalink( wc_get_page_id( 'shop' ) ),
                    ],
                    admin_url('customize.php')
                )
            ],
            'wishlist_page' => [
                'customizeUrl' => add_query_arg(
                    [
                        'return' => admin_url('themes.php'),
                        'autofocus[section]' => ExactWishlistProperties::KEY,
                        'url' => $defaultWishlistPageUrl,
                    ],
                    admin_url('customize.php')
                )
            ],
            'share_wishlist' => [
                'customizeUrl' => add_query_arg(
                    [
                        'return' => admin_url('themes.php'),
                        'autofocus[section]' => ExactWishlistProperties::KEY,
                        'url' => $defaultWishlistPageUrl,
                    ],
                    admin_url('customize.php')
                )
            ],
        ];

        foreach ($data as &$setting) {
            if (isset($customizeUrls[$setting->id])) {
                $setting->customizeUrls = $customizeUrls[$setting->id];
            }
        }

        return $data;
    }
}
