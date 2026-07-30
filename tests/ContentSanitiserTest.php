<?php

declare(strict_types=1);

namespace NSWDPC\Utilities\Trumbowyg\Tests;

use NSWDPC\Utilities\Trumbowyg\ContentSanitiser;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;

class ContentSanitiserTest extends SapphireTest
{
    /**
     * @var bool
     */
    protected $usesDatabase = false;

    /**
     * Test default configured handling
     */
    public function testDefaultClean(): void
    {

        Config::modify()->set(
            ContentSanitiser::class,
            'default_allowed_html_tags',
            ['a','p','ol','ul','li']
        );

        Config::modify()->set(
            ContentSanitiser::class,
            'default_allowed_attributes',
            []
        );

        Config::modify()->set(
            ContentSanitiser::class,
            'default_allowed_css_properties',
            []
        );

        $html = <<<HTML
<h1>Not allowed header 1</h1>
<h4>Header 4</h4>
<p>Paragraph 1 <a href="javascript:console.log(1);">click here!</a></p>
<p>Paragraph 2 <a href="https://valid.example.com">valid link</a></p>
<p><span onclick="doSomething();">Paragraph 3</span></p>
<blockquote>This is not allowed and <cite>cite is not</cite></blockquote>
<p>Email links <a href="mailto:someone@example.com?subject=spam">should be allowed</a></p>
<ul><li>list item 1</li><li>list item 2</li></ul>
<ol><li>list item 1</li><li>list item 2</li></ol>
<p><strong>strong 1</strong><em>Emphasis 1</em></p>
<script>eval();</script>
<scr+ipt>brokenScript();</script>
HTML;

        $expected = <<<HTML
Not allowed header 1
Header 4
<p>Paragraph 1 <a>click here!</a></p>
<p>Paragraph 2 <a href="https://valid.example.com">valid link</a></p>
<p>Paragraph 3</p>
This is not allowed and cite is not
<p>Email links <a href="mailto:someone@example.com?subject=spam">should be allowed</a></p>
<ul><li>list item 1</li><li>list item 2</li></ul>
<ol><li>list item 1</li><li>list item 2</li></ol>
<p>strong 1Emphasis 1</p>

brokenScript();
HTML;

        $cleaned = ContentSanitiser::clean($html);
        $this->assertEquals($expected, $cleaned);

    }

    /**
     * Test allowed tags argument
     */
    public function testAllowedTags(): void
    {

        Config::modify()->set(
            ContentSanitiser::class,
            'default_allowed_html_tags',
            []
        );

        Config::modify()->set(
            ContentSanitiser::class,
            'default_allowed_attributes',
            []
        );

        Config::modify()->set(
            ContentSanitiser::class,
            'default_allowed_css_properties',
            []
        );

        $html = <<<HTML
<h1>Not allowed header 1</h1>
<h4>Header 4</h4>
<p>Paragraph 1 <a href="javascript:console.log(1);">click here!</a></p>
<p>Paragraph 2 <a href="https://valid.example.com">valid link</a></p>
<p><span onclick="doSomething();">Paragraph 3</span></p>
<blockquote>This is not allowed and <cite>cite is not</cite></blockquote>
<p>Email links <a href="mailto:someone@example.com?subject=spam">should be allowed</a></p>
<ul><li>list item 1</li><li>list item 2</li></ul>
<ol><li>list item 1</li><li>list item 2</li></ol>
<p><strong>strong 1</strong><em>Emphasis 1</em></p>
<script>eval();</script>
<scr+ipt>brokenScript();</script>
HTML;
        $allowedTags = ['a', 'p', 'ul', 'ol', 'li'];

        $expected = <<<HTML
Not allowed header 1
Header 4
<p>Paragraph 1 <a>click here!</a></p>
<p>Paragraph 2 <a href="https://valid.example.com">valid link</a></p>
<p>Paragraph 3</p>
This is not allowed and cite is not
<p>Email links <a href="mailto:someone@example.com?subject=spam">should be allowed</a></p>
<ul><li>list item 1</li><li>list item 2</li></ul>
<ol><li>list item 1</li><li>list item 2</li></ol>
<p>strong 1Emphasis 1</p>

brokenScript();
HTML;

        $cleaned = ContentSanitiser::clean($html, $allowedTags);
        $this->assertEquals($expected, $cleaned);
    }

}
