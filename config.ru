require 'router'
require 'mustache'
require 'net/http'
require 'json'
require 'pp'

# read: http://mislav.uniqpath.com/2011/03/click-hijack/

use Rack::CommonLogger
use Rack::ShowExceptions
use Rack::Lint
use Rack::Static, :urls => ["/static"]

Mustache.template_path = '/Users/erik/Sites/test/static'

run Router.new([
  {
    :pattern => %r{^/github$}, 
    :controller => lambda do |env, match|
      
      PP.pp env['SERVER_NAME']
      
      class Index < Mustache
        def initialize(username)
          @username = username
        end
        def timeline
          url = "http://api.twitter.com/1/statuses/user_timeline.json?screen_name=github"
          res = Net::HTTP.get_response URI.parse url
          timeline = JSON.parse res.body
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
        def timeline
          url = 'http://api.twitter.com/1/statuses/user_timeline.json?screen_name=yahoo'
          res = Net::HTTP.get_response URI.parse url
          timeline = JSON.parse res.body
        end
      
        self.template_file = self.template_path + '/index.mustache'
      
      end
    
      [ 200, {'Content-Type' => 'text/html'}, Index.render ]
  
    end
  }
  
]);