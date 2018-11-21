<?php

namespace VanushWasHere\LaravelHtmlCssInliner\CssInliner;

use Symfony\Component\CssSelector\CssSelector;
use Symfony\Component\CssSelector\CssSelectorConverter;
use TijsVerkoyen\CssToInlineStyles\Css\Processor;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles as BaseInliner;

class CssToInlineStyles extends BaseInliner
{

    public function __construct()
    {
       parent::__construct();
    }

    /**
     * Will inline the $css into the given $html
     *
     * Remark: if the html contains <style>-tags those will be used, the rules
     * in $css will be appended.
     *
     * @param string $html
     * @param bool   $convert_inline
     * @param string $css
     *
     * @return string
     */
    public function convert($html, $convert_inline = false, $css = null)
    {
        $document = $this->createDomDocumentFromHtml($html);
        $processor = new Processor();

        if ($convert_inline) {
            // get all styles from the style-tags
            $rules = $processor->getRules(
                $processor->getCssFromStyleTags($html)
            );
        } else {
            $rules = array();
        }


        if ($css !== null) {
            $rules = $processor->getRules($css, $rules);
        }

        $document = $this->inline($document, $rules);

        return $this->getHtmlFromDocument($document);
    }


}
