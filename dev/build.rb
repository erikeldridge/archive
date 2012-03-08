css = File.readlines("dev/css/bootstrap-no-single-quotes-min.css")

js = {}
Dir['dev/js/lib/*min.js', 'dev/js/*.js'].each do |path|
  name = File.basename(path, '.js').sub('-min', '')
  js[name] = File.readlines path
end

templates = {}
Dir['dev/templates/*'].each do |path|
  name = File.basename path, '.mustache'
  templates[name] = File.readlines(path).map {|line| line.gsub('\'', '"').chop}
end

File.open('gerrit.js', 'w') do |output|
  output.write <<-END
  (function(){

    #{js['mustache']}
    #{js['jquery.cookie']}

    var bootstrap = '#{css}';

    var templates = {};
    templates.app = '#{templates['app']}';
    templates.mine = '#{templates['mine']}';
    templates.change = '#{templates['change']}';

    #{js['config']}
    #{js['show']}
    #{js['rpc']}
    #{js['nav']}
    #{js['init']}

  )();
  END
end