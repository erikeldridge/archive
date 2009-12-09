# The Asynchronous Unit Tester

The _asynchronous unit tester_ is useful when we need to test something, but we don't have access to the command line, and the item to test is available on a network.  This "framework" is asynchronous in that results from the tests are returned asynchronously.
 
## Prerequisites

* A netwok connection is required to load YUI as-is.  If you would like to run tests offline, download YUI from [developer.yahoo.com](http://developer.yahoo.com) and edit the script _src_ attribute value in _client.php_.

## Usage

1. Define a class called _Tests_ that consists of the test functions to run in _tests.php_
2. Run the tests by loading the file _client.php_ in a browser.

## License

Asynchronous Unit Tester

* package: http://github.com/erikeldridge/asynchronous-unit-tester
* author: Erik Eldridge
* copyright: Copyrights for code authored by Erik Eldridge is licensed under the following terms:
* license: BSD Open Source License

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.