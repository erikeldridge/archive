namespace :css do

  desc "Strip css"
  task :strip do
    input_path = 'dev/css/bootstrap.css'
    output_path = '/tmp/bootstrap-no-single-quotes.css'
    puts "stripping #{input_path}", "writing to #{output_path}"
    output = File.open(output_path, 'w')
    File.open(input_path) do |input|
      while line = input.gets

        # strip double-quotes so we can assign css to var
        output.write line.gsub('\'', '"')

      end
    end
    puts "done", '='*50
  end

  desc "Compress css"
  task :compress do
    input = '/tmp/bootstrap-no-single-quotes.css'
    output = 'dev/css/bootstrap-no-single-quotes-min.css'
    puts "compressing #{input}", "writing to #{output}"
    cmd = "yuicompressor -o '#{output}' #{input}"
    results = %x{#{cmd}}
    puts "done", '='*50
  end
end

namespace :js do
  desc "Compress js libs"
  task :compress do
    Dir['dev/js/lib/*.js'].reject{|path| path =~ /min\.js/}.each do |input|
      output = input.sub('.js', '-min.js')
      cmd = "yuicompressor -o '#{output}' #{input}"
      puts "compressing #{input}", "writing to #{output}"
      results = %x{#{cmd}}
      puts "done", '='*50
    end
  end
end

desc "Generate gerrit.js"
task :gen do
  puts "generating gerrit.js from"

  puts "* dev/css/bootstrap-no-single-quotes-min.css"
  css = File.readlines("dev/css/bootstrap-no-single-quotes-min.css")

  js = {}
  Dir['dev/js/lib/*min.js', 'dev/js/*.js'].each do |path|
    puts "* #{path}"
    name = File.basename(path, '.js').sub('-min', '')
    js[name] = File.readlines path
  end

  templates = {}
  Dir['dev/templates/*'].each do |path|
    puts "* #{path}"
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

  puts "done", '='*50
end

task :default => ['js:compress', 'css:strip', 'css:compress', 'gen']