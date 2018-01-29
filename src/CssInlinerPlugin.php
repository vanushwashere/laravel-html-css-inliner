<?php

namespace VanushWasHere\LaravelHtmlCssInliner;

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class CssInlinerPlugin
{
    /**
     * @var CssToInlineStyles
     */
    private $converter;

    /**
     * @var string
     */
    protected $css;

    /**
     * @var string
     */
    protected $html = '';


    /**
     * @param array $options options defined in the configuration file.
     */
    public function __construct(array $options)
    {
        $this->converter = new CssToInlineStyles();
        $this->loadOptions($options);
    }

    public function inline()
    {
        $body = $this->loadCssFilesFromLinks($this->html);
        return $this->converter->convert($body, $this->css);
    }

    public function loadHtml($html)
    {
        $this->html = $html;
    }


    /**
     * Load the options
     * @param  array $options Options array
     */
    public function loadOptions($options)
    {
        if (isset($options['css-files']) && count($options['css-files']) > 0) {
            $this->css = '';
            foreach ($options['css-files'] as $file) {
                $this->css .= file_get_contents($file);
            }
        }
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

}
