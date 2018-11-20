<?php

namespace VanushWasHere\LaravelHtmlCssInliner;

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class HtmlCssInlinerPlugin
{
    /**
     * @var string
     */
    protected $css;
    /**
     * @var string
     */
    protected $html = '';
    /**
     * @var CssToInlineStyles
     */
    private $converter;

    /**
     * @param array $options options defined in the configuration file.
     */
    public function __construct(array $options)
    {
        $this->converter = new CssToInlineStyles();
        $this->loadOptions($options);
    }

    /**
     * Load the options
     *
     * @param  array $options Options array
     */
    public function loadOptions($options)
    {
        if (isset($options['css-files']) && count($options['css-files']) > 0) {
            $this->css = '';
            foreach ($options['css-files'] as $file) {
                $this->css .= file_get_contents($this->encodeURI($file));
            }
        }
    }

    public function inline()
    {
        $body = $this->loadCssFilesFromLinks($this->html);

        return $this->converter->convert($body, $this->css);
    }

    /**
     * Find CSS stylesheet links and load them
     *
     * Loads the body of the html and passes
     * any link stylesheets to $this->css
     * Removes any link elements
     *
     * @return string $html The HTML
     */
    public function loadCssFilesFromLinks($html)
    {
        $dom = new \DOMDocument();
        // set error level
        $internalErrors = libxml_use_internal_errors(true);

        $dom->loadHTML($html);

        // Restore error level
        libxml_use_internal_errors($internalErrors);
        $link_tags = $dom->getElementsByTagName('link');

        if ($link_tags->length > 0) {
            do {
                if ($link_tags->item(0)->getAttribute('rel') == "stylesheet") {
                    $options['css-files'][] = $link_tags->item(0)->getAttribute('href');

                    // remove the link node
                    $link_tags->item(0)->parentNode->removeChild($link_tags->item(0));
                }
            } while ($link_tags->length > 0);

            if (isset($options)) {
                // reload the options
                $this->loadOptions($options);
            }

            return $dom->saveHTML();
        }

        return $html;
    }

    public function loadHtml($html)
    {
        $this->html = $html;
    }

    protected function encodeURI($url)
    {
        // http://php.net/manual/en/function.rawurlencode.php
        // https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/encodeURI
        $unescaped = array(
            '%2D' => '-', '%5F' => '_', '%2E' => '.', '%21' => '!', '%7E' => '~',
            '%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')'
        );
        $reserved = array(
            '%3B' => ';', '%2C' => ',', '%2F' => '/', '%3F' => '?', '%3A' => ':',
            '%40' => '@', '%26' => '&', '%3D' => '=', '%2B' => '+', '%24' => '$'
        );
        $score = array(
            '%23' => '#'
        );

        return strtr(rawurlencode($url), array_merge($reserved, $unescaped, $score));

    }

}
