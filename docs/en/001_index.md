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
- h1
- h2
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

These are also used when saving the field value in the backend.

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
        [ "formatting" ], // basic formatting
        [ "strong", "em" ], // semantic strong and emphasis
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

## Modifying the configuration

Be aware of cross-site scripting issues if certain tags are configured to be allowed. Good resources are:
+ https://html5sec.org/
+ https://owasp.org/www-community/xss-filter-evasion-cheatsheet
