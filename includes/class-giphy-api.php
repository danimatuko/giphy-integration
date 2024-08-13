<?php

/**
 * Class Giphy_API
 *
 * Handles interactions with the Giphy API for fetching trending GIFs and searching for GIFs.
 */
class Giphy_API
{
    /**
     * @var string $api_key The API key for accessing the Giphy API.
     */
    private $api_key;

    /**
     * Giphy_API constructor.
     *
     * Initializes the class with the API key.
     */
    public function __construct()
    {
        $this->api_key = GIPHY_API_KEY;
    }

    /**
     * Fetches trending GIFs from the Giphy API.
     *
     * Checks if trending GIFs are cached. If not, it fetches them from the Giphy API, caches the response, and triggers an action.
     *
     * @return array List of trending GIFs.
     */
    public function fetch_trending_gifs()
    {
        // Attempt to get cached GIFs
        $cached_gifs = get_transient('trending_gifs');
        if ($cached_gifs !== false) {
            return $cached_gifs;
        }

        // Fetch trending GIFs from Giphy API
        $response = wp_remote_get('https://api.giphy.com/v1/gifs/trending?api_key=' . $this->api_key . '&limit=10');
        if (is_wp_error($response)) {
            return [];
        }

        // Decode response and cache it
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        set_transient('trending_gifs', $data['data'], HOUR_IN_SECONDS);

        // Notify that new data is available
        do_action('giphy_trending_gifs_fetched', $data['data']);

        return $data['data'];
    }

    /**
     * Searches for GIFs based on a search term.
     *
     * Checks if search results are cached. If not, it fetches them from the Giphy API, caches the response, and triggers an action.
     *
     * @param string $term The search term to find GIFs.
     * @return array List of GIFs matching the search term.
     */
    public function search_gifs($term)
    {
        // Attempt to get cached search results
        $cached_search = get_transient('search_gifs_' . $term);
        if ($cached_search !== false) {
            return $cached_search;
        }

        // Search GIFs on Giphy API
        $response = wp_remote_get('https://api.giphy.com/v1/gifs/search?api_key=' . $this->api_key . '&q=' . urlencode($term) . '&limit=10');
        if (is_wp_error($response)) {
            return [];
        }

        // Decode response and cache it
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        set_transient('search_gifs_' . $term, $data['data'], HOUR_IN_SECONDS);

        // Notify that new data is available
        do_action('giphy_search_gifs_fetched', $data['data']);

        return $data['data'];
    }
}
