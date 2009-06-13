#!/usr/bin/ruby

require 'cgi'
require 'rubygems'
require 'mysql'

# 将相对路径为rel_path的文件发给客户端
DocumentRoot = "/var/www/html"
def send_file(rel_path)
  path = DocumentRoot + rel_path
  data = File.open(path, "rb") {|f| f.read}
  $stdout.binmode
  $stdout.write(data)
end

# 在数据库中根据id查询FLV文件的相对路径
def lookup_path(id)
  path = ""
  begin
    db = Mysql.real_connect('localhost', 'root', 'wickedsick77', 'vod')
    res = db.query("SELECT path FROM flves WHERE id = #{id}")
    if row = res.fetch_row then path = row[0] end
    res.free
  rescue => e
    # puts e.class + ": " + e.message
  ensure
    db.close if db
    return path
  end
end

# 检验session是否有效（目前总是返回true）
def check_session(session)
    true
end

#--------------------------------------------------------------------

begin
  cgi = CGI.new
  id = cgi['id']
  session = cgi['session']
  if check_session(session)
    rel_path = lookup_path(id);
    if rel_path != nil && rel_path != ''
#      print "Content-Type: video/x-flv\n"
      print "Content-Type: application/octet-stream\n"
#
      fsize = File.stat(DocumentRoot+rel_path).size
      mtime = File.stat(DocumentRoot+rel_path).mtime
#      print "Last-Modified: Tue, 24 Mar 2009 18:05:24 GMT\n"
      print "Last-Modified: #{mtime}\n"
#      print "ETag: \"b-48225e-465e13a1f0100\"\n"
#      print "X-Pad: avoid browser bug\n";
      print "Accept-Ranges: bytes\n"
      print "Content-Length: #{fsize}\n\n"
      send_file(rel_path)
    else
      # 处理id无效的情况
      print "HTTP/1.0 404 Not Found"
      print "Server: physacco@gmail.com\n"  # 无效
      print "Content-Type: text/html; charset=utf-8\n";
      print "Connection: Close\n"
      print "Fuck: shit!\n"
      print "Damn: Monster\n\n"
      puts "<html><head><title>Error</title></head><body><h1>404 Not Found</h1></body></html>"
    end
  else
    # 处理session无效的情况
      print "Content-Type: text/html\n\n";
      puts "<html><head><title>Error</title></head><body><h1>403 Forbidden</h1></body></html>"
  end
rescue
  puts $!
end


=begin CGI的环境变量举例
"HTTP_HOST"=>"127.0.0.1"
"HTTP_USER_AGENT"=>"Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10"
"HTTP_ACCEPT"=>"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"
"HTTP_ACCEPT_LANGUAGE"=>"zh-cn,zh;q=0.5"
"HTTP_ACCEPT_ENCODING"=>"gzip,deflate"
"HTTP_ACCEPT_CHARSET"=>"gb2312,utf-8;q=0.7,*;q=0.7"
"HTTP_KEEP_ALIVE"=>"300"
"HTTP_CONNECTION"=>"keep-alive"
"HTTP_CACHE_CONTROL"=>"max-age=0"
"SERVER_SIGNATURE"=>""
"SERVER_SOFTWARE"=>"Apache/2.2.4 (Win32) PHP/5.2.5"
"SERVER_NAME"=>"127.0.0.1"
"SERVER_ADDR"=>"127.0.0.1"
"SERVER_PORT"=>"80"
"REMOTE_ADDR"=>"127.0.0.1"
"DOCUMENT_ROOT"=>"E:/programme/apache/htdocs"
"SERVER_ADMIN"=>"physacco@gmail.com"
"SCRIPT_FILENAME"=>"C:/program1/apache/cgi-bin/test.rb"
"REMOTE_PORT"=>"1952"
"GATEWAY_INTERFACE"=>"CGI/1.1"
"SERVER_PROTOCOL"=>"HTTP/1.1"
"REQUEST_METHOD"=>"GET"
"QUERY_STRING"=>""
"REQUEST_URI"=>"/cgi-bin/test.rb"
"SCRIPT_NAME"=>"/cgi-bin/test.rb"
=end
