<?php

namespace NSWDPC\Utilities\Trumbowyg;

use SilverStripe\Forms\TextareaField;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;

class TrumbowygEditorField extends TextareaField
{
    private static array $casting = [
        'Value' => 'HTMLFragment',
    ];

    private static bool $include_own_jquery = true;

    /**
     * See _config.yml for default editor options
     */
    private static array $editor_options = [];

    /**
     * Get field options
     * @return array
     */
    protected function getFieldOptions(): array
    {
        // default options
        $options = static::config()->get('editor_options');
        if (empty($options) || !is_array($options)) {
            throw new \InvalidArgumentException("Missing or invalid editor_options configuration");
        }
        // keep these tags
        $options['tagsToKeep'] = ContentSanitiser::getAllowedHTMLTags();
        // remove these tags from the editor
        $options['tagsToRemove'] = self::getDeniedTags();
        return $options;
    }

    /**
     * These tags are denied by default
     *
     */
    public static function getDeniedTags(): array
    {
        $tags = static::config()->get('tags_to_remove');
        if(!is_array($tags)) {
            return [];
        } else {
            return $tags;
        }
    }

    /**
     * Returns the field
     */
    #[\Override]
    public function Field($properties = [])
    {
        $this->setAttribute('data-tw', '1');
        $this->setAttribute('data-tw-options', json_encode($this->getFieldOptions()));

        if (static::config()->get('include_own_jquery')) {
            Requirements::javascript(
                "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js",
                [
                    "integrity" => "sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==",
                    "crossorigin" => "anonymous"
                ]
            );
        }

        Requirements::javascript(
            "https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/trumbowyg.min.js",
            [
                "integrity" => "sha256-22WtbR/cVHSNiBetApYI0dgQOz/fuKbJ13h0dgAFXCs=",
                "crossorigin" => "anonymous"
            ]
        );

        Requirements::javascript("nswdpc/silverstripe-trumbowyg:client/static/js/loader.js");
        Requirements::css(
            "https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/ui/trumbowyg.min.css",
            "screen",
            [
                "integrity" => "sha256-BmAbHF77DxO8YJPVlYChVGWOah1AU2NMtO9SQj8KI8E=",
                "crossorigin" => "anonymous"
            ]
        );

        // the loader script
        $trumbowygLoader = <<<JAVASCRIPT
window.addEventListener(
    'DOMContentLoaded',
    function () {
        let trumbowygLoader = new TrumbowygLoader();
        trumbowygLoader.handle();
    }
);
JAVASCRIPT;
        Requirements::customScript($trumbowygLoader, "trumbowygLoader");
        return parent::Field($properties);
    }

    /**
     * Return the value, sanitised
     */
    #[\Override]
    public function Value()
    {
        return $this->dataValue();
    }

    /**
     * Return cleaned data value
     */
    #[\Override]
    public function dataValue()
    {
        $value = $this->value;
        if (!is_string($value)) {
            $value = "";
        } else {
            $value = trim($value);
        }

        // Handle empty
        if($value === '') {
            return '';
        }

        // Sanitise values, using the configured tagsToKeep setting
        $options = $this->getFieldOptions();
        $tagsToKeep = [];
        if(isset($options['tagsToKeep']) && is_array($options['tagsToKeep'])) {
            $tagsToKeep = $options['tagsToKeep'];
        }
        $this->value = ContentSanitiser::clean($value, $tagsToKeep);
        return $this->value;
    }

}
