<?php
/**
 * Content Plugin for Joomla! - ADALinker
 *
 * @author     Ioannis Fytros <info@rinenweb.eu>
 * @license    GNU GPL v3 or later
 * @link       https://github.com/rinenweb/plg_content_adalinker
 */

defined('_JEXEC') or die;

class plgContentADALinker extends JPlugin
{
    public function onContentPrepare($context, &$article, &$params, $page = 0)
    {
        // Define the pattern for "ΑΔΑ: ..."
        $pattern = '/\bΑΔΑ: ([^\W_]+-?[^\W_]+)\b/iu';
		
		// Get the link class from the plugin parameters
        $linkClass = $this->params->get('link_class', '');
		$urlType = $this->params->get('url_type', 'page');

        // Get the content
        $content = $article->text;

        // Search for the pattern in the content
        if (preg_match_all($pattern, $content, $matches))
        {
            // Loop through matched ADAs
            foreach ($matches[1] as $ada)
            {
                // Define the URL with the ADA parameter
                $url = $this->createUrl($ada, $urlType);

                // Construct the replacement string to preserve surrounding text
                $replace = 'ΑΔΑ: <a href="' . $url . '" title="' . $ada . '" class="' . $linkClass . '" target="_blank">' . $ada . '</a>';

                // Replace the matched pattern with the hyperlink
                $content = preg_replace('/\bΑΔΑ: ' . preg_quote($ada, '/') . '\b/iu', $replace, $content, 1); // Added '1' as the fourth argument to limit the replacements to one occurrence
            }

            // Update the content
            $article->text = $content;
        }

        return true;
    }
	private function createUrl($ada, $urlType)
	{
		$baseUrl = 'https://diavgeia.gov.gr/';
		// Determine the URL based on the selected type
		if ($urlType === 'file') {
			// URL for viewing a file
			$url = $baseUrl . 'doc/' . $ada . '?inline=true';
		} else {
			// Default URL for viewing a page in Diavgeia
			$url = $baseUrl . 'decision/view/' . $ada;
		}
		return $url;
	}
}
