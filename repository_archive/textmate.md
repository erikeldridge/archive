TextMate
========

* GetBundles bundle manager: http://solutions.treypiepmeier.com/2009/02/25/installing-getbundles-on-a-fresh-copy-of-textmate/
* Find by file name: command + T; type first letter of each part of file name eg "ttto" matches "this\_that_the.other"
* Format html: select all --> option + command + left-square-bracket
* Type "js" in AckMates Options input box to limit search to .js files
* Scope AckMate search by only opening subset of project
* TextMate Basics Tutorial: http://projects.serenity.de/textmate/tutorials/basics/
* Strip whitespace on save: http://blogobaggins.com/2009/03/31/waging-war-on-whitespace.html
* Highlight whitespace:
    * Go to Bundle Editor => Language => Ruby
    * Add this pattern in your patterns block for your language:

    <pre><code>{
        name = 'invalid.brittspace';
        match = '\s+$';
    }
    </code></pre>

    * Go to Textmate => Preferences => Fonts & Colors
    * Add Element
    * Name this element Brittspace
    * Set scope selector: invalid.brittspace
* Run ctrl + shift + V to lint ruby in Textmate
* JSHint bundle: https://github.com/rondevera/jslintmate
* Toggle btwn file editor and project drawer: ctrl + tab