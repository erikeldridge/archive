namespace :css do
  desc "Strip css"
  task :strip do
    output = File.open('/tmp/bootstrap-2.1.0-no-quotes.css', 'w')
    File.open('dev/css/bootstrap-2.1.0.css') do |input|
      while line = input.gets

        # strip double-quotes so we can assign css to var
        output.write line.gsub('\'', '"')

      end
    end
  end
  desc "Compress css"
  task :compress do
    cmd = "yuicompressor -o 'dev/css/bootstrap-2.1.0-no-quotes-min.css' /tmp/bootstrap-2.1.0-no-quotes.css"
    results = %x{#{cmd}}
    unless results =~ /- OK/
      puts results
      exit 1
    end
  end
end