<?php

namespace NSWDPC\Utilities\Trumbowyg;

use SilverStripe\Assets\Filesystem;
use Silverstripe\Core\Config\Configurable;
use Silverstripe\Core\Config\Config;
use Silverstripe\ORM\ValidationException;

/**
 * Sanitise content provided by a trumbowyg field
 * @author James <james@dcs>
 */
class ContentSanitiser {

    use Configurable;

    /**
     * @var string
     * default allowed tags, if none are specified in configuration
     */
    private static $default_allowed_html_tags = "<p><i><blockquote>"
        . "<b><strong><em><br>"
        . "<h2><h3><h4><h5><h6>"
        . "<ol><ul><li><a><strike>";

    /**
     * Return tags suitable for strip_tags
     * @return string
     */
    public static function getAllowedHTMLTags() : string {
        $allowedHTMLTags = Config::inst()->get(self::class, 'default_allowed_html_tags');
        if($allowedHTMLTags == "") {
            $allowedHTMLTags = "<p>";// disallow all
        }
        return $allowedHTMLTags;
    }

    /**
     * Return tags suitable for strip_tags
     * @return array
     */
    public static function getAllowedHTMLTagsAsArray() : array {
        $allowedHTMLTags = trim(self::getAllowedHTMLTags(), "<>");
        return explode("><", $allowedHTMLTags);
    }

    /**
     * Generate a strict configuration for handling incoming user content
     * @return array
     */
    public static function generateConfig() : array {
        $serializerPath = TEMP_PATH . "/HtmlPurifier/Serializer";
        if(!is_dir($serializerPath)) {
            Filesystem::makeFolder($serializerPath);
        }
        return [
            'Core.Encoding' => 'UTF-8',
            'HTML.AllowedElements' => self::getAllowedHTMLTagsAsArray(),
            'HTML.AllowedAttributes' => ['href'],
            'URI.AllowedSchemes' => ['http','https','mailto','callto'],
            'Attr.ID.HTML5' => true,
            'AutoFormat.RemoveEmpty.RemoveNbsp' => true,
            'AutoFormat.RemoveEmpty' => true,
            'Cache.SerializerPath' => $serializerPath,
            'Cache.SerializerPermissions' => null
        ];
    }

    /**
     * Clean dirty HTML using HTML purifier
     * If the purification fails in any way, an entitised version of the HTML is returned
     * @param string $html
     * @return string
     */
    public static function clean($dirtyHtml) : string {
        try {
            $htmlPurifierConfig = \HTMLPurifier_Config::createDefault();
            $configuration = self::generateConfig();
            foreach ($configuration as $key => $value) {
                $htmlPurifierConfig->set($key, $value);
            }
            $purifier = new \HTMLPurifier($htmlPurifierConfig);
            $cleaned = $purifier->purify($dirtyHtml);
            if(trim(strip_tags($cleaned ?? '')) === '') {
                return '';
            } else {
                return $cleaned;
            }
            return $cleaned;
        } catch (\Exception $e) {
            return htmlentities($dirtyHtml, ENT_QUOTES|ENT_HTML5, "UTF-8");
        }
    }
}
