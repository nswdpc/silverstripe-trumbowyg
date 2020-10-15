<?php

namespace NSWDPC\Utilities\Trumbowyg;

use Silverstripe\Core\Config\Configurable;
use Silverstripe\Core\Config\Config;
use Exception;
use DOMDocument;

/**
 * Sanitise content provided by a trumbowyg field
 * @author James <james@dcs>
 */
class ContentSanitiser {

    use Configurable;

    // default allowed tags, if none are specified in configuration
    private static $default_allowed_html_tags = "<p><i><blockquote><b><strong><em><br>"
                                . "<h1><h2><h3><h4><h5><h6>"
                                . "<ol><ul><li><a><strike>";

    /**
     * Retains allowed tags from the provided html
     * The module requires "href" attributes, these need to have e.g "javascript:" removed
     * @param string $html
     * @returns string
     */
    private static function strip_html($html) {
        $options = Config::inst()->get(TrumboywgEditorField::class, 'editor_options');
        $allowed_html_tags = "";
        if(!empty($options['tagsToKeep']) && is_array($options['tagsToKeep'])) {
            // mogrify into something for strip_tags
            $allowed_html_tags = "<" . implode("><", $options['tagsToKeep']) . ">";
        }
        if($allowed_html_tags == "") {
            $allowed_html_tags = Config::inst()->get(self::class, 'default_allowed_html_tags');
        }
        if($allowed_html_tags == "") {
            $allowed_html_tags = "<p>";// disallow all
        }
        return strip_tags($html, $allowed_html_tags);
    }

    /**
     * Restrict tags that can be used and remove attributes
     * The module requires "href" attributes, these need to have e.g "javascript:" removed
     * @param string $html
     * @returns string
     */
    public static function clean($html) {
        libxml_use_internal_errors(true);
        try {
            // wrap in html, strip unwanted tags
            $cleaned = "<html>" . self::strip_html($html) . "</html>";
            $xml_prefix = '<?xml encoding="utf-8" ?>';
            $dom = new DOMDocument('1.0','UTF-8');
            $dom->loadHTML( $xml_prefix . $cleaned, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $tags = $dom->getElementsByTagName("*");
            foreach($tags as $tag) {
                foreach ($tag->attributes as $attr) {
                    if($attr->nodeName == "href") {
                        // the value must be a valid URL
                        $value = $attr->nodeValue;
                        $scheme = parse_url($value, PHP_URL_SCHEME);
                        switch($scheme) {
                            case "javascript":
                                $attr->nodeValue = "";
                                break;
                            default:
                                break;
                        }
                    } else {
                        // drop all attributes - we don't support them at all
                        $tag->removeAttribute($attr->nodeName);
                    }
                }
            }
            $cleaned = $dom->saveHTML();
        } catch (Exception $e) {
            // the least worst option on error is to return just the HTML with the allowed tags
            $cleaned = self::strip_html($html);
        }
        libxml_clear_errors();

        // remove tags that may have been added, this works around issues with DOMDocument adding html/body tags
        $cleaned = str_replace(["<html>","</html>","<?xml encoding=\"utf-8\" ?>"] , "", $cleaned);
        $cleaned = trim($cleaned);

        return $cleaned;
    }
}
