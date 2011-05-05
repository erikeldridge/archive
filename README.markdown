# Validate-arguments

Validates JavaScript function argument types

## Quickstart

1. Get [`validate` code](https://github.com/erikeldridge/validate-arguments/blob/master/validate.js)
1. Use it:
    <pre><code>function foo(bar, baz){
      validate(arguments, 'number', 'string');
      //...
    }</code></pre>

## Goal

Catch errors early. `validate` belongs to the fail-fast-and-loud school of thought.

## Usage

1. Include the [`validate` code](https://github.com/erikeldridge/validate-arguments/blob/master/validate.js)
1. Define your function
1. Call `validate` inside your function, passing:
  * the function's _arguments_ object
  * the types of each argument to validate

For example:

    function foo(bar, baz){
      validate(arguments, 'number', 'string');
      //...
    }

### Argument subset

If _n_ arguments are passed into a function, but _m_ types are passed into `validate`, where _n_ > _m_, only the first _m_ arguments will be validated.

For example:

    function foo(bar, baz, bax){
      // only validate "bar" arg as a number
      validate(arguments, 'number');
    }

### Valid types

* 'undefined'
* 'nan'
* 'object'
* 'number'
* 'string'
* 'boolean'
* 'array'
* 'date'
* 'regexp'
* 'function'

### Suppression

Set `validate.isActive = false;` to suppress validation.

## Output

The `validate` function throws an error if an argument has an invalid type, and also pretty-prints the error's fields to the console for convenience. The error contains the following fields:

* _name_. The name of the error object
* _arguments_. The arguments passed into the validated function
* _type_. The error type
* _message_. A human-readable error message
* _fileName_. The name of the file generating the error. Firefox 4 only.
* _lineNumber_. The line number of the file generating the error. Firefox 4 only.
* _stack_. The execution stack. Because error generation is abstracted into _validate.js_, all file and line references, which would be super-helpful, simply point back to _validate.js_. The stack allows us to see the file name and line number where _validate_ failed.

## Copyright and License

Copyright 2011 Erik Eldridge

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this work except in compliance with the License.
You may obtain a copy of the License in the LICENSE file, or at:

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

License text from https://github.com/twitter/LICENSE.
Twitter is not connected with this project in any way.
