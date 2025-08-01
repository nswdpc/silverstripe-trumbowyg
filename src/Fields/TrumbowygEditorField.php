<?php

namespace NSWDPC\Utilities\Trumbowyg;

use SilverStripe\Forms\TextareaField;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;

class TrumbowygEditorField extends TextareaField
{
    private static array $casting = [
        'Value' => 'HTMLText',
    ];

    private static bool $include_own_jquery = true;

    /**
     * Get field options
     * @return array
     */
    protected function getFieldOptions()
    {
        $options = $this->config()->get('editor_options');
        if (empty($options) || !is_array($options)) {
            // Fallback options in case of none configured
            $options = [
                "fixedBtnPane" => true,
                "semantic" => true,
                "removeformatPasted" => true,
                "resetCss" => true,
                "autogrow" => true,
                "btns" => [
                    [ "undo", "redo" ],
                    [ "p", "h2","h3", "h4", "h5", "strong", "em" ],
                    [ "link", "" ],
                    [ "unorderedList", "orderedList" ],
                    [ "removeformat" ],
                    [ "fullscreen" ]
                ],
                "tagsToKeep" => [
                    "p",
                    "i","b", "strong", "em", "br",
                    "h2","h3","h4","h5","h6",
                    "ol","ul","li","a"
                ]
            ];
        }

        $options['tagsToRemove'] = self::getDeniedTags();
        return $options;
    }

    /**
     * These tags are denied by default
     */
    public static function getDeniedTags(): array
    {
        return [
            'form',
            'script',
            'link',
            'style',
            'body',
            'html',
            'head',
            'meta',
            'applet',
            'object',
            'iframe',
            'img',
            'picture',
            'video',
        ];
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

        $this->value = ContentSanitiser::clean($value);
        return $this->value;
    }

}
