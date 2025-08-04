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
        $options = $this->config()->get('editor_options');
        if (empty($options) || !is_array($options)) {
            throw new \InvalidArgumentException("Missing or invalid editor_options configuration");
        }
        $options['tagsToKeep'] = ContentSanitiser::getAllowedHTMLTagsAsArray();
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

        if ($this->config()->get('include_own_jquery')) {
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
        // import template with options
        $custom_script = ArrayData::create([
            'ID' => $this->ID(),
            'Options' => json_encode($this->getFieldOptions())
        ])->renderWith('NSWDPC/Utilities/Trumbowyg/Script');
        Requirements::customScript(
            $custom_script,
            "trumbowyg_editor_" . $this->ID()
        );
        Requirements::css(
            "https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/ui/trumbowyg.min.css",
            "screen",
            [
                "integrity" => "sha256-BmAbHF77DxO8YJPVlYChVGWOah1AU2NMtO9SQj8KI8E=",
                "crossorigin" => "anonymous"
            ]
        );
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
        }

        $options = $this->getFieldOptions();
        $tagsToKeep = [];
        if(isset($options['tagsToKeep']) && is_array($options['tagsToKeep'])) {
            $tagsToKeep = $options['tagsToKeep'];
        }
        $this->value = ContentSanitiser::clean($value, $tagsToKeep);
        return $this->value;
    }

}
