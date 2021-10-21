<?php

namespace NSWDPC\Utilities\Trumbowyg;

use SilverStripe\Dev\Deprecation;

/**
 * This class is provided for backwards compatibility, as the original
 * field was created with the incorrect spelling
 * Update your code to use `TrumbowygEditorField`
 */
class TrumboywgEditorField extends TrumbowygEditorField {

    /**
     * Handle deprecation notice for incorrectly named field
     */
    public function Field($properties = []) {
        Deprecation::notice('1.0', 'TrumboywgEditorField will be removed in 1.0');
        return parent::Field($properties);
    }

}
