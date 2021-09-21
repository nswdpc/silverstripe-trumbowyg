<?php

namespace NSWDPC\Utilities\Trumbowyg;

use SilverStripe\Core\Convert;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\Core\Manifest\ResourceURLGenerator;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;
use Exception;


class TrumboywgEditorField extends TextareaField {

    private static $casting = [
        'Value' => 'HTMLText',
    ];

    private static $include_own_jquery = true;

    /**
     * @var string
     * Chore: when updating the version, ensure the SRI hashes are updated
     */
    protected $version = "2.23.0";

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

        // ensure tagsToRemove is set by us
        $options['tagsToRemove'] = self::getDeniedTags();

        // if no svgPath defined in configuration, set one up
        if(empty($options['svgPath'])) {
            $svgPath = '';

            if($sprite = ModuleResourceLoader::singleton()
                ->resolvePath(
                    "nswdpc/silverstripe-trumbowyg:client/static/svg/icons.{$this->version}.svg"
                )
            ) {
                // get the path without the ?m= nonce
                $svgPath = Injector::inst()->get(ResourceURLGenerator::class)
                    ->setNonceStyle(null)
                    ->urlForResource( $sprite );
            }
            // load it up, if found
            if($svgPath) {
                $options['svgPath'] = $svgPath;
                $options['svgAbsoluteUsePath'] = false;
            }
        }

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
                "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js",
                [
                    "integrity" => "sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==",
                    "crossorigin" => "anonymous"
                ]
            );
        }
        Requirements::javascript(
            "https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/{$this->version}/trumbowyg.min.js",
            [
                "integrity" => "sha512-sffB9/tXFFTwradcJHhojkhmrCj0hWeaz8M05Aaap5/vlYBfLx5Y7woKi6y0NrqVNgben6OIANTGGlojPTQGEw==",
                "crossorigin" => "anonymous"
            ]
        );
        // import template with options
        $custom_script = ArrayData::create([
            'Options' => json_encode( $this->getFieldOptions(), JSON_UNESCAPED_SLASHES )
        ])->renderWith('NSWDPC/Utilities/Trumbowyg/Script');
        Requirements::customScript(
            $custom_script,
            "trumbowyg_editor"
        );
        Requirements::css(
            "https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/{$this->version}/ui/trumbowyg.min.css",
            "screen",
            [
                "integrity" => "sha512-iw/TO6rC/bRmSOiXlanoUCVdNrnJBCOufp2s3vhTPyP1Z0CtTSBNbEd5wIo8VJanpONGJSyPOZ5ZRjZ/ojmc7g==",
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
