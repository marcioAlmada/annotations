# Prereqs:
# * Ruby
# * gem install watchr
# * gem install rb-inotify
# * pecl install xdebug // in case you want to generate coverage reports
 
# Usage:
# copy autotest to php project directory
# run watchr autotest

def clear
  puts "\e[H\e[2J" #clear console
end

def phpunit testpath=''
	clear
  system "phpunit"
  # system "phpunit #{testpath}"
  # system "phpunit --coverage-html /tmp/coverage/" # generates html coverage report
  # system "phpunit --coverage-text" # shows coverage report at terminal
  # system "phpunit --testdox"
end

def notify status, msg
  if status
  	title = 'PASS'
  	image = 'user-available'
  else
  	title = "FAIL"
  	image = 'user-busy'
  end
  system "notify-send #{title} #{msg} -i #{image}"
end
 
watch('test/.*Test\.php') do |md|
  clear
  status = phpunit "#{md[0]}"
  notify status, md[0]
end

watch('src/(.*)\.php') do |md|
  clear
  testpath = md[1] + "Test.php"
  status = phpunit "test/#{testpath}"
  notify status, testpath
end