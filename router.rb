# See http://erikeldridge.wordpress.com/2010/02/21/simple-ruby-rack-router/
class Router
  
  def initialize(routes)    
    @routes = routes
  end
  
  def default
    [ 404, {'Content-Type' => 'text/plain'}, 'file not found' ]
  end
  
  def call(env)
    @routes.each do |route|
      
      match = env['REQUEST_PATH'].match(route[:pattern])
      
      if match && ( route[:condition].nil? || route[:condition].call(env) )
        
        return route[:controller].call( env, match )
      end
      
    end
    default
  end
end