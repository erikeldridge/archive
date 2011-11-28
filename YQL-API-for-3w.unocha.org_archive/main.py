#!/usr/bin/env python
#
# Copyright 2007 Google Inc.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
#


from google.appengine.ext import webapp
from google.appengine.ext.webapp import util
import urllib, urllib2 
import simplejson, sys
          
class ProxyHandler(webapp.RequestHandler):

  def get(self):  
      import os.path
      import cookielib  
      
      # This is a subclass of FileCookieJar that has useful load and save methods
      cj = cookielib.LWPCookieJar() 
      opener = urllib2.build_opener( urllib2.HTTPCookieProcessor( cj ) )
      urllib2.install_opener( opener )

      # If we want to save cookies later...
      #COOKIEFILE = 'cookies.lwp'          # the path and filename that you want to use to save your cookies in
      #if os.path.isfile(COOKIEFILE):
      #        cj.load(COOKIEFILE)

      def _load( url, postData = None, headers = {} ):
          headers = dict( headers )
          headers.setdefault( "User-Agent", 
                              "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7 for crisiscommons.org" )

          req = urllib2.Request( url, postData, headers )
          handle = urllib2.urlopen( req )

          #print 'These are the cookies we have received so far :'
          #for index, cookie in enumerate(cj):
          #    print index, '  :  ', cookie        
          #cj.save(COOKIEFILE)                     # save the cookies again

          return handle

        
      _setCookies_url = 'http://3w.unocha.org/WhoWhatWhere/?webFlag=1&uSite=ocha_na_ht'#+self.request.get('region')
      def load( url, postData = None, headers = {} ):
          response = _load( _setCookies_url, postData, headers )
          response = _load( url, postData, headers )

          return response


      if ( __name__ == '__main__' ):
          sys.stdout.write( ' ' )
          #raw: 'http://3w.unocha.org/WhoWhatWhere/projectReportFwt4.php?repId=3&adminLevel=1&mSno=2&tabId=a2&activeSection='
          #encoded:  
          import hashlib
          url = self.request.get('url')
          data_url = urllib.unquote(url) 
          html = load( data_url ).read()
          if (url == 'http%3A%2F%2F3w.unocha.org%2FWhoWhatWhere%2FprojectReportFwt4.php%3FrepId%3D3%26adminLevel%3D1%26mSno%3D2%26tabId%3Da2%26activeSection%3D'):
              import parser_orgs
              def conv( row ):
                  sector, org, dept, capital, coords = row
                  return {
                      'sector': sector,
                      'org': org,
                      'dept': dept,
                      'capital': capital,
                      'coords': coords,
                      }
              rows = [conv( row ) for row in rows]
              rows = list( parser_orgs.parse( html ) )
          else:
              import parser_contacts
              rows = list( parser_contacts.parse( html ) )
              # html = open( 'data2.html' ).read()
              rows = [parser_contacts.privacyFilter( row ) for row in rows]
          json = simplejson.dumps( rows )
          
      self.response.out.write( json )


def main():
  application = webapp.WSGIApplication([
    # ('/', MainHandler), 
    ('/proxy', ProxyHandler)],
                                       debug=True)
  util.run_wsgi_app(application)


if __name__ == '__main__':
  main()
