# Gerrit.js

Gerrit.js skins [Gerrit](http://code.google.com/p/gerrit/) using [Twitter Bootstrap](http://twitter.github.com/bootstrap) and [dotjs](http://defunkt.io/dotjs/).

## Status

The #mine, #change,{id}, #q (search), and #signin pages are roughly functional. Please check out the [issues](https://github.com/erikeldridge/gerrit.js/issues) list for more details.

## Installation

### General

1. Install [dotjs](http://defunkt.io/dotjs/)
1. Copy the _gerrit.js_ file from this project into your _.js_ folder
1. Rename _gerrit.js_ to match the host name of your gerrit installation

### Developers

1. Install [dotjs](http://defunkt.io/dotjs/)
1. Fork & clone the [gerrit.js repo](https://github.com/erikeldridge/gerrit.js)
1. Create a symlink named to match the host name of your gerrit installation, pointing at the _gerrit.js_ file in the cloned repo
1. Optional: install [rvm](https://rvm.beginrescueend.com/rvm/install/) if you want rvm to manage the gems installed by Bundler
1. Install bundler: `gem install bundler`
1. Run bundler: `bundle`
1. Run `rake gen:auto` to regenerate _gerrit.js_ after each change
1. Optional: Install [homebrew](https://rvm.beginrescueend.com/rvm/install/) to make yuicompressor easier to install
1. Optional: install [yuicompressor](http://developer.yahoo.com/yui/compressor/) if you intend to modify js/css libs: `brew install yuicompressor`

Now you can develop, commit, repeat (loop, DCRL, anyone?), without copy/pasting

All the libs used by this project are stashed in the _dev_ directory. Check out the rakefile (`$ rake -T`) for handy utils when working with these libs.

## Acknowledgements

Thanks, [@mattknox](https://twitter.com/#!/mattknox), for suggesting dotjs.

## Author

Gerrit.js was designed and built with all the love in the world by [@erikeldridge](http://twitter.com/erikeldridge).

## License

Code licensed under the Apache License v2.0.