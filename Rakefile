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