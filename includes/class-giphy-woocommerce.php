<?php

/**
 * Class Giphy_WooCommerce
 *
 * Integrates Giphy GIFs with WooCommerce by creating products and setting GIFs as product images.
 */
class Giphy_WooCommerce
{
    /**
     * @var Giphy_API $giphy_api Instance of the Giphy_API class used to fetch GIF data.
     */
    private $giphy_api;

    /**
     * Giphy_WooCommerce constructor.
     *
     * Initializes the class with an instance of the Giphy_API class and sets up necessary actions and filters.
     *
     * @param Giphy_API $giphy_api Instance of the Giphy_API class.
     */
    public function __construct(Giphy_API $giphy_api)
    {
        $this->giphy_api = $giphy_api;
        add_action('giphy_trending_gifs_fetched', [$this, 'create_gif_products_from_giphy']);
        add_action('giphy_search_gifs_fetched', [$this, 'create_gif_products_from_giphy']);

        add_filter('woocommerce_single_product_image_html', [$this, 'set_external_gif_as_product_image'], 20, 2);
        add_filter('woocommerce_get_product_thumbnail', [$this, 'set_external_gif_as_product_image'], 20, 2);
        add_filter('woocommerce_single_product_image_thumbnail_html', [$this, 'set_external_gif_as_gallery_image'], 20, 2);
        add_filter('woocommerce_product_get_image', [$this, 'set_external_gif_as_product_image_in_archives'], 20, 4);
    }

    /**
     * Creates WooCommerce products from Giphy GIF data.
     *
     * Checks if a product already exists for each GIF and creates a new product if not.
     *
     * @param array $gifs Array of GIF data from Giphy.
     * @return void
     */
    public function create_gif_products_from_giphy($gifs)
    {
        foreach ($gifs as $gif) {
            $existing_product = get_page_by_title($gif['title'], OBJECT, 'product');

            if ($existing_product === null) {
                $product = new WC_Product_Simple();
                $product->set_name($gif['title']);
                $product->set_slug(sanitize_title($gif['title']));
                $product->set_regular_price('10.00');
                $product->set_description('Buy this GIF from Giphy.');
                $product->set_short_description($gif['title']);
                $product->set_catalog_visibility('visible');
                $product->set_sold_individually(true);
                $product->set_status('publish');
                $gif_url = $gif['images']['fixed_height']['url'];
                $product->add_meta_data('_external_image_url', $gif_url);

                $product->save();
            }
        }
    }

    /**
     * Sets the external GIF URL as the product image for single product pages.
     *
     * Filters the HTML for the single product image to use the external GIF URL if available.
     *
     * @param string $html The existing HTML for the product image.
     * @param int $post_id The ID of the product post.
     * @return string The modified HTML with the external GIF URL.
     */
    public function set_external_gif_as_product_image($html, $post_id)
    {
        $product = wc_get_product($post_id);
        $external_image_url = $product->get_meta('_external_image_url');

        if ($external_image_url) {
            return '<img src="' . esc_url($external_image_url) . '" alt="' . esc_attr($product->get_name()) . '" class="wp-post-image">';
        }

        return $html;
    }

    /**
     * Sets the external GIF URL as the gallery image for single product pages.
     *
     * Filters the HTML for the gallery images to use the external GIF URL if available.
     *
     * @param string $html The existing HTML for the gallery image.
     * @param int $attachment_id The ID of the attachment (image).
     * @return string The modified HTML with the external GIF URL.
     */
    public function set_external_gif_as_gallery_image($html, $attachment_id)
    {
        $product_id = get_post_meta($attachment_id, '_product_id', true);
        $product = wc_get_product($product_id);
        $external_image_url = $product->get_meta('_external_image_url');

        if ($external_image_url) {
            return '<img src="' . esc_url($external_image_url) . '" alt="' . esc_attr($product->get_name()) . '" class="wp-post-image">';
        }

        return $html;
    }

    /**
     * Sets the external GIF URL as the product image in WooCommerce product archives.
     *
     * Filters the HTML for the product image in archives and loops to use the external GIF URL if available.
     *
     * @param string $image The existing HTML for the product image.
     * @param WC_Product $product The product object.
     * @param string $size The image size.
     * @param array $attr Additional image attributes.
     * @return string The modified HTML with the external GIF URL.
     */
    public function set_external_gif_as_product_image_in_archives($image, $product, $size, $attr)
    {
        $external_image_url = $product->get_meta('_external_image_url');

        if ($external_image_url) {
            return '<img src="' . esc_url($external_image_url) . '" alt="' . esc_attr($product->get_name()) . '" class="wp-post-image" />';
        }

        return $image;
    }
}
