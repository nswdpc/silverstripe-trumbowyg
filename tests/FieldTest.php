<?php

namespace NSWDPC\Utilities\Trumbowyg;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Dev\SapphireTest;

class FieldTest extends SapphireTest {

    use Configurable;

    protected $usesDatabase = false;

    public function testFieldContentSanitisation() {

        $html = "<h4>Header 4</h4>"
                . "<p>Paragraph 1</p>"
                . "<p>Paragraph 2</p>"
                . "<p>Paragraph 3</p>"
                . "<ul><li>list item 1</li><li>list item 2</li></ul>"
                . "<ol><li>list item 1</li><li>list item 2</li></ol>"
                . "<p><strong>strong 1</strong><em>Emphasis 1</em></p>";

        $field = TrumboywgEditorField::create("testFieldContentSanitisation", "test", $html);

        $data_value = $field->dataValue();

        $this->assertEquals($data_value, $html, "The data value {$data_value} should be the same as the HTML input {$html}");


    }
}
