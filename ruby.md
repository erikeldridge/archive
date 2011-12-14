Ruby
====

* Rails docs w/ nice search: http://www.railsbrain.com/
* gdb
    * Use "debugger" keyword in script to launch gbd
    * Set autolist to see context: `set autolist`
* HTML/CSS sanitizer https://github.com/rgrove/sanitize/
* Run spec w/ -c option for satisfying red/green output
* Use rake to rebuild db: `rake db:rebuild`
* Show all rake tasks: `rake -T`
* Install gems w/ rake: `rake gems:install`
* Show rake stack trace on error: `rake <task> --trace`
* Build gem: `gem build <path to gemspec>`
* Create rvm environment (ruby vm + gems) for ruby v1.8.7:
    1. `rvm install 1.8.7`
    2. `rvm gemset create <gemset name>`
* Show all rvm environments: `rvm list`
* Show all rvm gemsets: `rvm list gemsets`
* Select an rvm environment: `rvm use <rvm name>@<gemset name>`
* Use .rvmrc to tell rvm which gemset to use on a per-directory basis
* List gems w/ details: `gem list -d`
* Install gem w/o docs: `gem install <gem name> --no-rdoc --no-ri`, e.g., `gem install rails --version 2.3.8 --no-rdoc --no-ri`
* Generate rails migration timestamp: `ruby -e "p Time.new.strftime('%Y%m%d%H%M%S')"`
* Magic variable to last result: `_`, eg `x = 2 * 2; y = _`
* Use `script/console` to load rails app in irb
* Bundler uses the gem sources listed in the Gemfile, not necessarily those listed in `gem sources`