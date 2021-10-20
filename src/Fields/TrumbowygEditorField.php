<?php

namespace NSWDPC\Utilities\Trumbowyg;

use SilverStripe\Core\Convert;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;
use Exception;
use DOMDocument;


class TrumbowygEditorField extends TextareaField {

    private static $casting = [
        'Value' => 'HTMLText',
    ];

    private static $include_own_jquery = true;

    /**
     * Get field options
     * @return array
     */
    protected function getFieldOptions() {
        $options = $this->config()->get('editor_options');
        if( empty($options) || !is_array($options) ) {
            // Fallback options in case of none configured
            $options = [
                "semantic" => true,
                "removeformatPasted" => true,
                "resetCss" => true,
                "autogrow" => true,
                "buttons" => [
                    [ "undo", "redo" ],
                    [ "p", "h2","h3", "h4", "h5", "strong", "em" ],
                    [ "link", "" ],
                    [ "unorderedList", "orderedList" ],
                    [ "removeformat" ],
                    [ "fullscreen" ]
                ],
                "tagsToKeep" => [
                    "p"
                ]
            ];
        }
        $options['tagsToRemove'] = self::getDeniedTags();
        return $options;
    }

    /**
     * These tags are denied by default
     * @return array
     */
    public static function getDeniedTags() {
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
    public function Field($properties = []) {
        $this->setAttribute('data-tw','1');

        if($this->config()->get('include_own_jquery')) {
            Requirements::javascript(
                "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js",
                [
                    "integrity" => "sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==",
                    "crossorigin" => "anonymous"
                ]
            );
        }
        Requirements::javascript(
            "https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.25.1/trumbowyg.min.js",
            [
                "integrity" => "sha512-t4CFex/T+ioTF5y0QZnCY9r5fkE8bMf9uoNH2HNSwsiTaMQMO0C9KbKPMvwWNdVaEO51nDL3pAzg4ydjWXaqbg==",
                "crossorigin" => "anonymous"
            ]
        );
        // import template with options
        $custom_script = ArrayData::create([
            'Options' => json_encode( $this->getFieldOptions(), JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT )
        ])->renderWith('NSWDPC/Utilities/Trumbowyg/Script');
        Requirements::customScript(
            $custom_script,
            "trumbowyg_editor"
        );
        Requirements::css(
            "https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.25.1/ui/trumbowyg.min.css",
            "screen",
            [
                "integrity" => "sha512-nwpMzLYxfwDnu68Rt9PqLqgVtHkIJxEPrlu3PfTfLQKVgBAlTKDmim1JvCGNyNRtyvCx1nNIVBfYm8UZotWd4Q==",
                "crossorigin" => "anonymous"
            ]
        );
        return parent::Field($properties);
    }

    /**
     * Return the value, sanitised
     */
    public function Value() {
        return $this->dataValue();
    }

    /**
     * Return cleaned data value
     */
    public function dataValue() {
        $sanitiser = new ContentSanitiser();
        $this->value = $sanitiser->clean($this->value);
        return $this->value;
    }

}
