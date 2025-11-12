<?php

namespace NSWDPC\Utilities\Trumbowyg;

use SilverStripe\Assets\Filesystem;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Config\Config;

/**
 * Sanitise content provided by a trumbowyg field
 * @author James
 */
class ContentSanitiser
{
    use Configurable;

    /**
     * Default allowed tags, if none are specified in configuration
     */
    private static array $default_allowed_html_tags = [
        "p", "i", "blockquote",
        "b", "strong", "em", "br",
        "h2", "h3", "h4", "h5", "h6",
        "ol", "ul", "li",
        "a", "strike"
    ];

    /**
     * Allowed attributes
     */
    private static array $default_allowed_attributes = [];

    /**
     * Allowed CSS properties
     */
    private static array $default_allowed_css_properties = [];

    /**
     * Return tags as an array, or a default tag if empty
     */
    public static function getAllowedHTMLTags(): array
    {
        $allowedHTMLTags = static::config()->get('default_allowed_html_tags');
        if ($allowedHTMLTags == "") {
            $allowedHTMLTags = ["p"];// disallow all except p
        }
        return $allowedHTMLTags;
    }

    /**
     * Return allowed attributes from configuration
     */
    public static function getAllowedAttributes(): array
    {
        $allowedAttributes = static::config()->get('default_allowed_attributes');
        if(!is_array($allowedAttributes)) {
            return [];
        } else {
            return $allowedAttributes;
        }
    }

    /**
     * Return allowed CSS properties from configuration
     */
    public static function getAllowedCssProperties(): array
    {
        $allowedCssProperties = static::config()->get('default_allowed_css_properties');
        if(!is_array($allowedCssProperties)) {
            return [];
        } else {
            return $allowedCssProperties;
        }
    }

    /**
     * Generate a strict configuration for handling incoming user content
     * @param array $allowedTags an array of allowed HTML tags e.g. ['p','strong']
     */
    public static function generateConfig(array $allowedTags = []): array
    {
        $serializerPath = TEMP_PATH . "/HtmlPurifier/Serializer";
        if (!is_dir($serializerPath)) {
            Filesystem::makeFolder($serializerPath);
        }

        if($allowedTags === []) {
            $allowedTags = static::getAllowedHTMLTags();
        }

        $allowedAttributes = static::getAllowedAttributes();
        // if 'a' is an allowed tag, allow href
        if(in_array('a', $allowedTags)) {
            $allowedAttributes = ['a.href'];
        }

        $allowedCssProperties = static::getAllowedCssProperties();

        return [
            'Core.Encoding' => 'UTF-8',
            'HTML.AllowedElements' => $allowedTags,
            'HTML.AllowedAttributes' => $allowedAttributes,
            'CSS.AllowedProperties' => $allowedCssProperties,
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
     */
    public static function clean(string $dirtyHtml, array $allowedTags = []): string
    {
        try {
            $htmlPurifierConfig = \HTMLPurifier_Config::createDefault();
            $configuration = self::generateConfig($allowedTags);
            foreach ($configuration as $key => $value) {
                $htmlPurifierConfig->set($key, $value);
            }

            $purifier = new \HTMLPurifier($htmlPurifierConfig);
            $cleaned = $purifier->purify($dirtyHtml);
            if (trim(strip_tags($cleaned ?? '')) === '') {
                return '';
            } else {
                return trim($cleaned);
            }
        } catch (\Exception $exception) {
            return htmlentities($dirtyHtml, ENT_QUOTES | ENT_HTML5, "UTF-8");
        }
    }
}
