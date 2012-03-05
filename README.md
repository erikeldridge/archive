# Gerrit.js

Gerrit.js skins [Gerrit](http://code.google.com/p/gerrit/) using [Twitter Bootstrap](http://twitter.github.com/bootstrap) and [dotjs](http://defunkt.io/dotjs/).

## Status

Rough. The gerrit.js can talk to the rpc service, but only the #mine & #signin pages have rendering logic. Please check out the [issues](https://github.com/erikeldridge/gerrit.js/issues) list for more details.

## Installation

### Dependencies

* dotjs

### General

1. Copy the _gerrit.js_ file from this project into your _.js_ folder
1. Rename _gerrit.js_ to match the host name of your gerrit installation

### Developers

1. Fork & clone the gerrit.js repo
1. Create a symlink named to match the host name of your gerrit installation, pointing at the _gerrit.js_file in the cloned repo

Now you can develop, commit, repeat (loop, DCRL, anyone?), without copy/pasting

## Acknowledgements

Thanks, [@mattknox](https://twitter.com/#!/mattknox), for suggesting dotjs.

## Author

Gerrit.js was designed and built with all the love in the world by [@erikeldridge](http://twitter.com/erikeldridge).

## License

Code licensed under the Apache License v2.0.