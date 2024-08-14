<?php


/**
 * Class Giphy_Shortcodes
 *
 * Handles the creation of shortcodes for displaying GIF search forms, search results, and trending GIFs.
 */
class Giphy_Shortcodes
{
    /**
     * @var Giphy_API $giphy_api Instance of the Giphy_API class used to fetch GIF data.
     */
    private $giphy_api;

    /**
     * Giphy_Shortcodes constructor.
     *
     * Initializes the class with an instance of the Giphy_API class and sets up the shortcodes.
     *
     * @param Giphy_API $giphy_api Instance of the Giphy_API class.
     */
    public function __construct(Giphy_API $giphy_api)
    {
        $this->giphy_api = $giphy_api;
        add_shortcode('search_gifs', [$this, 'display_search_gifs']);
        add_shortcode('trending_gifs', [$this, 'display_trending_gifs']);
    }

    /**
     * Displays a search form for GIFs and shows search results.
     *
     * Generates an HTML form for searching GIFs and displays the search results if a term is provided.
     *
     * @return string HTML output of the search form and search results.
     */
    public function display_search_gifs()
    {
        ob_start();
?>
        <form method="get" id="giphy-search-form">
            <input type="text" name="giphy_search" placeholder="Search GIFs">
            <button type="submit">Search</button>
        </form>
<?php
        if (isset($_GET['giphy_search'])) {
            $search_term = sanitize_text_field($_GET['giphy_search']);
            $gifs = $this->giphy_api->search_gifs($search_term);

            echo '<div class="giphy-gifs-grid search-results">';
            foreach ($gifs as $gif) {
                echo '<div class="gif-item">';
                echo '<img src="' . esc_url($gif['images']['fixed_height']['url']) . '" alt="' . esc_attr($gif['title']) . '">';
                echo '</div>';
            }
            echo '</div>';
        }
        return ob_get_clean();
    }

    /**
     * Displays trending GIFs.
     *
     * Fetches trending GIFs and displays them in a grid format.
     *
     * @return string HTML output of the trending GIFs.
     */
    public function display_trending_gifs()
    {
        ob_start();
        $gifs = $this->giphy_api->fetch_trending_gifs();

        echo '<div class="giphy-gifs-grid trending-gifs">';
        foreach ($gifs as $gif) {
            echo '<div class="gif-item">';
            echo '<img src="' . esc_url($gif['images']['fixed_height']['url']) . '" alt="' . esc_attr($gif['title']) . '">';
            echo '</div>';
        }
        echo '</div>';

        return ob_get_clean();
    }
}
