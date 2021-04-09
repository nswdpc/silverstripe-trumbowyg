# Configuration

The editor configuration defines a restricted set of tags for saving.

> There is no allowance now or in the future roadmap for asset linking or uploads.

## Attribute restrictions

All attributes are removed upon save, except for:

+ the href attribute of the <a> tag

Additionally, "javascript:" is removed from the href attribute

## Tag restrictions

By default the following tags are allowed in the editor (see _config/config.yml)

```yaml
- p
- i
- blockquote
- b
- strong
- em
- br
- h3
- h4
- h5
- h6
- ol
- ul
- li
- a
- strike
```

Only the `href` attribute is allowed (for links), with http or https schemes.

If no configuration value `tagsToKeep` is available or it is empty, a default set is used. The fallback condition is to restrict to '<p>' tags only.

The editor is provided a set of `tagsToRemove` for client-side editing (see _config/config.yml). This configuration is not used in saving the value, as value saving is determined by the `tagsToKeep` only.

## Options

If no configuration is provided, the following configuration is set:

```php
$options = [
    "semantic" => true, // Generates a better, more semantic oriented HTML
    "removeformatPasted" => true, // remove pasted styles from Word and friends
    "resetCss" => true, // ref: https://alex-d.github.io/Trumbowyg/documentation/#reset-css
    "autogrow" => true, // allow the text edit zone to extend
    "buttons" => [
        [ "undo", "redo" ],
        [ "p","h3", "h4", "h5", "strong", "em" ], // basic formatting
        [ "link", "" ], // support adding <a> links
        [ "unorderedList", "orderedList" ], // ul and ol
        [ "removeformat" ], // clear all formatting to assist with removing cruft
        [ "fullscreen" ] // go full screen edit
    ],
    "tagsToKeep" => [
        "p" //  only keep <p> tags by default
    ]
];
```

## Basic example

In this example, we are collecting a submission in basic HTML from a `UserSubmissionController`. The field setup is the same as a standard `TextareaField`

```php
namespace MyApp;


use NSWDPC\Utilities\Trumbowyg\TrumboywgEditorField;
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\Fieldlist;
use SilverStripe\Forms\FormAction;

class UserSubmissionController extends ContentController
{
    /**
     * @var array
     */
    private static $allowed_actions = [
        'UserEditForm' => true,
    ];

    /**
     * Return the form for accepting data
     */
    public function UserEditForm() : Form {
        return Form::create(
            $this,
            'UserEditForm',
            Fieldlist::create(
                TrumboywgEditorField::create(
                    'UserProvidedContent', // field name
                    'Write something' // title
                )->setDescription(
                    // optional
                    "This is a description"
                )->setRightTitle(
                    // optional
                    "This is a right title"
                )
            ),
            Fieldlist::create(
                FormAction::create(
                    'doSubmission'
                )
            )
        );
    }

    /**
     * Handle the submitted content
     */
    public function doSubmission($data, $form) {
        if(empty($data['UserProvidedContent'])) {
            // error - no content
        }
        // UserProvidedContent will be return via
        // TrumboywgEditorField::dataValue()
        $sanitised = $data['UserProvidedContent'];
        // save the content somewhere
    }
}
```

In your template, render the form:

```template
<% if $UserEditForm %>
    <h1>Provide some information</h1>
    <section>
        {$UserEditForm}
    </section>
<% end_if %>
```

## Modifying the configuration

Be aware of cross-site scripting issues if certain tags and/or attributes are configured to be allowed.

Good resources are:
+ https://html5sec.org/
+ https://owasp.org/www-community/xss-filter-evasion-cheatsheet
