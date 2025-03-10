<?php

namespace NSWDPC\Utilities\Trumbowyg\Tests;

use NSWDPC\Utilities\Trumbowyg\ContentSanitiser;
use NSWDPC\Utilities\Trumbowyg\TrumbowygEditorField;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Dev\SapphireTest;

class FieldTest extends SapphireTest {

    /**
     * @var bool
     */
    protected $usesDatabase = false;

    /**
     * Test that the field content is sanitised
     */
    public function testFieldContentSanitisation() {

        $options = [
            "fixedBtnPane" => true,
            "semantic" => true,
            "removeformatPasted" => true,
            "resetCss" => true,
            "autogrow" => true,
            "buttons" => [
                [ "undo", "redo" ],
                [ "p","h3", "h4", "h5", "strong", "em" ],
                [ "link", "" ],
                [ "unorderedList", "orderedList" ],
                [ "removeformat" ],
                [ "fullscreen" ]
            ],
            "tagsToKeep" => [
                "p",
                "i",
                "blockquote",
                "b",
                "strong",
                "em",
                "br",
                "h3",
                "h4",
                "h5",
                "h6",
                "ol",
                "ul",
                "li",
                "a",
                "strike",
            ]
        ];
        Config::modify()->set(
            TrumbowygEditorField::class,
            'editor_options',
            $options
        );

        $dirtyHtml = <<<HTML
<h1>Not allowed header 1</h1>
<h4>Header 4</h4>
<p>Paragraph 1 <a href="javascript:console.log(1);">click here!</a></p>
<p>Paragraph 2 <a href="https://valid.example.com">valid link</a></p>
<p><span onclick="doSomething();">Paragraph 3</span></p>
<blockquote>This is allowed <cite>cite is not</cite></blockquote>
<p>Email links <a href="mailto:someone@example.com?subject=spam">should be allowed</a></p>
<ul><li>list item 1</li><li>list item 2</li></ul>
<ol><li>list item 1</li><li>list item 2</li></ol>
<p><strong>strong 1</strong><em>Emphasis 1</em></p>
<script>eval();</script>
<scr+ipt>brokenScript();</script>
HTML;

        $cleanHtml = <<<HTML
Not allowed header 1
<h4>Header 4</h4>
<p>Paragraph 1 <a>click here!</a></p>
<p>Paragraph 2 <a href="https://valid.example.com">valid link</a></p>
<p>Paragraph 3</p>
<blockquote>This is allowed cite is not</blockquote>
<p>Email links <a href="mailto:someone@example.com?subject=spam">should be allowed</a></p>
<ul><li>list item 1</li><li>list item 2</li></ul>
<ol><li>list item 1</li><li>list item 2</li></ol>
<p><strong>strong 1</strong><em>Emphasis 1</em></p>

brokenScript();
HTML;

        $field = TrumbowygEditorField::create("testFieldContentSanitisation", "test", $dirtyHtml);

        // sanitise the value
        $sanitisedValue = $field->dataValue();

        // validate
        $this->assertEquals($cleanHtml, $sanitisedValue, "The sanitised value should be the same as the expected clean HTML input");


    }

    /**
     * test custom config generation
     */
    public function testGenerateConfig() {
        $tags = "<p><i><u><h2>";
        Config::modify()->set(
            ContentSanitiser::class,
            'default_allowed_html_tags',
            $tags
        );
        $expectedGeneratedTags = ['p','i','u','h2'];
        $generatedTags = ContentSanitiser::getAllowedHTMLTagsAsArray();
        $this->assertEquals( $expectedGeneratedTags, $generatedTags, "Generated tags should match expected");

        $config = ContentSanitiser::generateConfig();
        $expected = [
            'Core.Encoding' => 'UTF-8',
            'HTML.AllowedElements' => $expectedGeneratedTags,
            'HTML.AllowedAttributes' => ['href'],
            'URI.AllowedSchemes' => ['http','https', 'mailto', 'callto'],
            'Attr.ID.HTML5' => true,
            'AutoFormat.RemoveEmpty.RemoveNbsp' => true,
            'AutoFormat.RemoveEmpty' => true
        ];
        $this->assertEquals( $expected, $config, "Configuration is not as expected" );
    }

    /**
     * test that only <p> tags are returned
     */
    public function testEmptyConfig() {
        $tags = "";
        Config::modify()->set(
            ContentSanitiser::class,
            'default_allowed_html_tags',
            $tags
        );
        $expectedGeneratedTags = ['p'];
        $generatedTags = ContentSanitiser::getAllowedHTMLTagsAsArray();
        $this->assertEquals( $expectedGeneratedTags, $generatedTags, "Generated tags should match expected");

        $config = ContentSanitiser::generateConfig();
        $expected = [
            'Core.Encoding' => 'UTF-8',
            'HTML.AllowedElements' => $expectedGeneratedTags,
            'HTML.AllowedAttributes' => ['href'],
            'URI.AllowedSchemes' => ['http','https', 'mailto', 'callto'],
            'Attr.ID.HTML5' => true,
            'AutoFormat.RemoveEmpty.RemoveNbsp' => true,
            'AutoFormat.RemoveEmpty' => true
        ];
        $this->assertEquals( $expected, $config, "Configuration is not as expected" );
    }

    /**
     * test that empty html sent by the library is ignored and treated as empty string
     */
    public function testEmptyHtml() {
        $content = [
            "<p><br></p>" => "",
            "<p> <br></p>" => "",
            "<p>foo</p>" => "<p>foo</p>",
            "<h1></h1><p>paragraph</p> " => "<p>paragraph</p> ",
            " " => "",
            " . " => " . ",
            "\n\n" => ""
        ];
        foreach($content as $in => $expected) {
            $field = TrumbowygEditorField::create("testEmptyHtml", "test", $in);
            $out = $field->dataValue();
            $this->assertEquals($expected, $out);
        }
    }

}
