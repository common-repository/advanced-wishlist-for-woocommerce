<?php

namespace AlgolWishlist\API\Controllers;

use AlgolWishlist\API\DTO\Error;
use AlgolWishlist\API\DTO\PromotionalEmailCalculateEmailReceiversRequest;
use AlgolWishlist\API\DTO\PromotionalEmailPreviewRequest;
use AlgolWishlist\API\DTO\PromotionalEmailRequest;
use AlgolWishlist\Repositories\ItemsOfWishlist\ItemsOfWishlistRepositoryWordpress;
use AlgolWishlist\VersionPro\Emails\Emails\PromotionEmail;
use Exception;
use OpenApi\Annotations as OA;
use WC_Coupon;
use WP_Http;
use WP_REST_Request;
use WP_REST_Response;

class AdminPromotionalEmailController
{
    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var ItemsOfWishlistRepositoryWordpress
     */
    protected $itemsOfWishlistRepositoryWordpress;

    public function __construct()
    {
        $this->namespace = 'algol-wishlist/v1/admin';

        global $wpdb;
        $this->itemsOfWishlistRepositoryWordpress = new ItemsOfWishlistRepositoryWordpress($wpdb);
    }

    public function registerRoutes()
    {
        register_rest_route($this->namespace, '/promotional-email/preview', array(
            [
                'methods' => "POST",
                'callback' => array($this, 'getPreview'),
                'permission_callback' => [$this, 'canManageWoocommerce'],
            ]
        ));

        register_rest_route($this->namespace, '/promotional-email/save-draft', array(
            [
                'methods' => "POST",
                'callback' => array($this, 'saveDraft'),
                'permission_callback' => [$this, 'canManageWoocommerce'],
            ],
        ));

        register_rest_route($this->namespace, '/promotional-email/calculate-email-receivers', array(
            [
                'methods' => "POST",
                'callback' => array($this, 'calculateEmailReceivers'),
                'permission_callback' => [$this, 'canManageWoocommerce'],
                'args' => [
                    'productID' => [
                        'description' => __('Product id', 'wc-wishlist'),
                        'required' => false,
                        'type' => 'string',
                    ],
                    'userID' => [
                        'description' => __('User id', 'wc-wishlist'),
                        'required' => false,
                        'type' => 'string',
                    ],
                ],
            ],
        ));

        register_rest_route($this->namespace, '/promotional-email/send', array(
            [
                'methods' => "POST",
                'callback' => array($this, 'send'),
                'permission_callback' => [$this, 'canManageWoocommerce'],
            ],
        ));
    }

    public function canManageWoocommerce(): bool
    {
        return current_user_can('manage_woocommerce');
    }

    /**
     * @OA\Post(
     *     path="/admin/promotional-email/preview",
     *     tags={"Admin promotional email"},
     *     operationId="adminPreviewPromotionalEmail",
     *     @OA\RequestBody(
     *         description="Email body",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PromotionalEmailPreviewRequest"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="preview", type="string")
     *         ),
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
    public function getPreview(WP_REST_Request $request): WP_REST_Response
    {
        $requestBody = PromotionalEmailPreviewRequest::fromBody($request->get_json_params());

        if (!in_array($requestBody->type, array('html', 'plain'), true)) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", "Missing 'type' parameter"),
                WP_Http::BAD_REQUEST
            );
        }

        if (!is_string($requestBody->htmlContent)) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", "Missing 'htmlContent' parameter"),
                WP_Http::BAD_REQUEST
            );
        }

        if (!is_string($requestBody->plainContent)) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", "Missing 'htmlContent' parameter"),
                WP_Http::BAD_REQUEST
            );
        }

        $template = $requestBody->type;
        $templatePath = 'plain' === $template ? 'plain/' : '';

        $contentHtml = sanitize_textarea_field(wp_unslash($requestBody->htmlContent));
        $contentText = sanitize_textarea_field(wp_unslash($requestBody->plainContent));

        $productID = $requestBody->product && $requestBody->product->productId ?: 0;
        $variation = $requestBody->product && $requestBody->product->variation ?: [];
        $coupon = sanitize_text_field(wp_unslash($requestBody->coupon));

        // load the mailer class.
        $mailer = WC()->mailer();
        if (!class_exists("\AlgolWishlist\VersionPro\Emails\Emails\PromotionEmail")) {
            return new WP_REST_Response(
                new Error("unable_to_find_email_class", "Email class was not found"),
                WP_Http::INTERNAL_SERVER_ERROR
            );
        }

        /** @var PromotionEmail $email */
        $email = $mailer->emails['\AlgolWishlist\VersionPro\Emails\Emails\PromotionEmail'];
        $email->user = $requestBody->userId ? get_user_by('id', $requestBody->userId) : get_user_by(
            'id',
            get_current_user_id()
        );
        $email->object = wc_get_product($productID);

        // set contents.
        if ($contentHtml) {
            $email->content_html = wpautop($contentHtml);
        }
        if ($contentText) {
            $email->content_text = $contentText;
        }

        // set coupon.
        if ($coupon) {
            $email->coupon = new WC_Coupon($coupon);
        }

        // get the preview email subject.
        $email_heading = $email->get_heading();
        $email_content = $email->{'get_custom_content_' . $template}();

        // get the preview email content.
        ob_start();
        include ALGOL_WISHLIST_PLUGIN_PATH . 'src/VersionPro/templates/emails/' . $templatePath . 'promotion.php';
        $message = ob_get_clean();

        if ('plain' === $template) {
            $message = nl2br($message);
        }

        $message = $email->style_inline($message);

        return new WP_REST_Response(['preview' => $message], WP_Http::OK);
    }

    /**
     * @OA\Post(
     *     path="/admin/promotional-email/save-draft",
     *     tags={"Admin promotional email"},
     *     operationId="adminSaveDraftPromotionalEmail",
     *     @OA\RequestBody(
     *         description="Email body",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PromotionalEmailRequest"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="result", type="boolean")
     *         ),
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
    public function saveDraft(WP_REST_Request $request): WP_REST_Response
    {
        $requestBody = PromotionalEmailRequest::fromBody($request->get_json_params());

        $productIds = array_filter(
            array_map(function ($product) {
                return $product->productId;
            }, $requestBody->products)
        );

        $userIds = is_array($requestBody->userIds) ? $requestBody->userIds : [];

        // if we're saving draft, update option and skip.
        update_option(
            'algol_wishlist_promotion_draft',
            array(
                'product_id' => $productIds,
                'user_id' => $userIds,
                'content_html' => wp_kses_post(wp_unslash($requestBody->htmlContent)),
                'content_text' => sanitize_textarea_field(wp_unslash($requestBody->plainContent)),
                'coupon' => sanitize_text_field(wp_unslash($requestBody->coupon)),
            )
        );

        return new WP_REST_Response(['result' => true], WP_Http::OK);
    }

    /**
     * @OA\Post(
     *     path="/admin/promotional-email/calculate-email-receivers",
     *     tags={"Admin promotional email"},
     *     operationId="admincalculateEmailReceiversPromotionalEmail",
     *     @OA\RequestBody(
     *         description="Body",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PromotionalEmailCalculateEmailReceiversRequest"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="count", type="integer"),
     *              @OA\Property(property="label", type="string")
     *         ),
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
    public function calculateEmailReceivers(WP_REST_Request $request): WP_REST_Response
    {
        $requestBody = PromotionalEmailCalculateEmailReceiversRequest::fromBody($request->get_json_params());
        $userIds = $requestBody->userIds;

        $count = 0;
        if (count($userIds) > 0) {
            $count = count($userIds);
        } else {
            $productIds = array_filter(
                array_map(function ($product) {
                    return $product->productId;
                }, $requestBody->products)
            );

            $receiversIDs = [];
            foreach ($productIds as $id) {
                try {
                    $items = $this->itemsOfWishlistRepositoryWordpress->getAllByProductId($id);
                } catch (Exception $e) {
                    continue;
                }

                foreach ($items as $item) {
                    $receiversIDs[] = $item->id;
                }
                $count += count(array_unique($receiversIDs));
            }
        }

        return new WP_REST_Response([
            'count' => $count,
            'label' => sprintf('%d %s', $count, _n('user', 'users', $count, 'wc-wishlist'))
        ], WP_Http::OK);
    }

    /**
     * @OA\Post(
     *     path="/admin/promotional-email/send",
     *     tags={"Admin promotional email"},
     *     operationId="adminSendPromotionalEmail",
     *     @OA\RequestBody(
     *         description="Email body",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PromotionalEmailRequest"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="result", type="boolean")
     *         ),
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
    public function send(WP_REST_Request $request): WP_REST_Response
    {
        $requestBody = PromotionalEmailRequest::fromBody($request->get_json_params());

        if (!in_array($requestBody->type, array('html', 'plain'), true)) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", "Missing 'type' parameter"),
                WP_Http::BAD_REQUEST
            );
        }

        if (!is_string($requestBody->htmlContent)) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", "Missing 'htmlContent' parameter"),
                WP_Http::BAD_REQUEST
            );
        }

        if (!is_string($requestBody->plainContent)) {
            return new WP_REST_Response(
                new Error("unable_to_get_resource", "Missing 'htmlContent' parameter"),
                WP_Http::BAD_REQUEST
            );
        }

        $coupon = sanitize_text_field(wp_unslash($requestBody->coupon));

        $productIds = array_filter(
            array_map(function ($product) {
                return $product->productId;
            }, $requestBody->products)
        );

        $userIds = is_array($requestBody->userIds) ? $requestBody->userIds : [];

        $receiversIDs = [];
        if (count($userIds) > 0) {
            $receiversIDs = $userIds;
        } else {
            foreach ($productIds as $id) {
                try {
                    $items = $this->itemsOfWishlistRepositoryWordpress->getAllByProductId($id);
                } catch (Exception $e) {
                    continue;
                }

                foreach ($items as $item) {
                    $receiversIDs[] = $item->id;
                }
            }

            $receiversIDs = array_unique($receiversIDs);
        }

        $campaignInfo = apply_filters(
            'algol_wishlist_promotional_email_additional_info',
            [
                'html_content' => $requestBody->htmlContent,
                'text_content' => $requestBody->plainContent,
                'coupon_code' => $coupon,
                'product_id' => $productIds,
                'user_id' => $userIds,
                'receivers' => $receiversIDs,
                'schedule_date' => time(),
                'counters' => [
                    'sent' => 0,
                    'to_send' => count($receiversIDs),
                ],
            ]
        );
        // retrieve campaign queue.
        $queue = get_option('algol_wishlist_promotion_campaign_queue', []);
        $queue[] = $campaignInfo;
        $result = update_option('algol_wishlist_promotion_campaign_queue', $queue);

        return new WP_REST_Response(['result' => $result], WP_Http::OK);
    }

}
