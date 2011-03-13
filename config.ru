require 'rubygems'
require 'router'
require 'mustache'
require 'json'

# read: http://mislav.uniqpath.com/2011/03/click-hijack/

use Rack::CommonLogger
use Rack::ShowExceptions
use Rack::Lint
use Rack::Static, :urls => ["/static"]

Mustache.template_path = 'static'

run Router.new([
  {
    :pattern => %r{^/github$}, 
    :controller => lambda do |env, match|
      
      class Index < Mustache
        
        def initialize(username)
          @username = username
        end
        
        def host
          ENV['host'] || 'localhost:9393'
        end
        
        def timeline
          json = IO.readlines('static/timeline_github.json').first
          timeline = JSON.parse json
        end
      
        self.template_file = self.template_path + '/index.mustache'
      
      end

      [ 200, {'Content-Type' => 'text/html'}, Index.new(match[1]).render ]

    end
  },
  {
    :pattern => %r{^/}, 
    :controller => lambda do |env, match|
    
      class Index < Mustache
        
        def host
          ENV['host'] || 'localhost:9393'
        end
        
        def timeline
          json = IO.readlines('static/timeline_yahoo.json').first
          timeline = JSON.parse json
        end
      
        self.template_file = self.template_path + '/index.mustache'
      
      end
    
      [ 200, {'Content-Type' => 'text/html'}, Index.render ]
  
    end
  }
  
]);