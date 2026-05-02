<?php
/**
 * Content Plugin for Joomla! - ADALinker
 *
 * @author     Ioannis Fytros <info@rinenweb.eu>
 * @license    GNU GPL v3 or later
 * @link       https://github.com/rinenweb/plg_content_adalinker
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;

class plgContentADALinker extends CMSPlugin
{
    public function onContentPrepare($context, &$article, &$params, $page = 0)
    {
        if (empty($article->text) || !is_string($article->text)) {
            return true;
        }

        $linkClass = $this->params->get('link_class', '');
        $urlType   = $this->params->get('url_type', 'page');

        $content = $article->text;

        $pattern = '~<a\b[^>]*>.*?</a>(*SKIP)(*FAIL)|(?<![\p{L}\p{N}_])(ΑΔΑ:\s*)([\p{L}\p{N}]+(?:-[\p{L}\p{N}]+)?)(?![\p{L}\p{N}_-])~isu';

        $article->text = preg_replace_callback(
            $pattern,
            function ($matches) use ($linkClass, $urlType) {
                $prefix = $matches[1];
                $ada    = $matches[2];

                $url = $this->createUrl($ada, $urlType);

                $classAttr = $linkClass
                    ? ' class="' . htmlspecialchars($linkClass, ENT_QUOTES, 'UTF-8') . '"'
                    : '';

                return htmlspecialchars($prefix, ENT_QUOTES, 'UTF-8')
                    . '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '"'
                    . ' title="' . htmlspecialchars($ada, ENT_QUOTES, 'UTF-8') . '"'
                    . $classAttr
                    . ' target="_blank"'
                    . ' rel="noopener noreferrer">'
                    . htmlspecialchars($ada, ENT_QUOTES, 'UTF-8')
                    . '</a>';
            },
            $content
        );

        return true;
    }

    private function createUrl($ada, $urlType)
    {
        $baseUrl = 'https://diavgeia.gov.gr/';
        $encodedAda = rawurlencode($ada);

        if ($urlType === 'file') {
            return $baseUrl . 'doc/' . $encodedAda . '?inline=true';
        }

        return $baseUrl . 'decision/view/' . $encodedAda;
    }
}
