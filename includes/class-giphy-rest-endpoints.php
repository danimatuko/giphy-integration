<?php

/**
 * Class Giphy_REST_Endpoints
 *
 * Registers custom REST API endpoints for interacting with Giphy API data.
 */
class Giphy_REST_Endpoints
{
    /**
     * @var Giphy_API $giphy_api Instance of the Giphy_API class used to fetch GIF data.
     */
    private $giphy_api;

    /**
     * Giphy_REST_Endpoints constructor.
     *
     * Initializes the class with an instance of the Giphy_API class and registers REST API routes.
     *
     * @param Giphy_API $giphy_api Instance of the Giphy_API class.
     */
    public function __construct(Giphy_API $giphy_api)
    {
        $this->giphy_api = $giphy_api;
        add_action('rest_api_init', [$this, 'register_rest_routes']);
    }

    /**
     * Registers REST API routes for Giphy endpoints.
     *
     * Adds routes for fetching trending GIFs and searching GIFs.
     *
     * @return void
     */
    public function register_rest_routes()
    {
        register_rest_route('giphy/v1', '/trending/', array(
            'methods' => 'GET',
            'callback' => [$this, 'get_trending_gifs'],
            'permission_callback' => '__return_true',
        ));

        register_rest_route('giphy/v1', '/search/', array(
            'methods' => 'GET',
            'callback' => [$this, 'search_gifs'],
            'args' => array(
                'term' => array(
                    'required' => true,
                    'validate_callback' => function ($param, $request, $key) {
                        return is_string($param);
                    }
                ),
            ),
            'permission_callback' => '__return_true',
        ));
    }

    /**
     * Callback function to return trending GIFs.
     *
     * Fetches trending GIFs from the Giphy_API class and returns them as a REST API response.
     *
     * @return WP_REST_Response The response containing the list of trending GIFs.
     */
    public function get_trending_gifs()
    {
        $gifs = $this->giphy_api->fetch_trending_gifs();
        return new WP_REST_Response($gifs, 200);
    }

    /**
     * Callback function to search for GIFs.
     *
     * Searches for GIFs based on the provided search term and returns them as a REST API response.
     *
     * @param array $data The request data containing the search term.
     * @return WP_REST_Response The response containing the list of search results.
     */
    public function search_gifs($data)
    {
        $term = sanitize_text_field($data['term']);
        $gifs = $this->giphy_api->search_gifs($term);
        return new WP_REST_Response($gifs, 200);
    }
}