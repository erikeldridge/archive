import re
import simplejson

from BeautifulSoup import BeautifulSoup

import sys
reload( sys )
sys.setdefaultencoding( 'utf-8' ) # BeautifulSoup gacks otherwise...

#html = "<html><p>Para 1<p>Para 2<blockquote>Quote 1<blockquote>Quote 2"
def decode( s ):
    for encoding in ('utf-8', 'cp1252'):
        try:
            return unicode( s, encoding )
        except UnicodeDecodeError, ude:
            continue

def replaceEscapes( src ):
    src = src.replace( '&ndash;', '-' )
    src = src.replace( '&amp;', '&' )

    return src

def parse( html ):    
    soup = BeautifulSoup( html )#, fromEncoding = 'utf-8' )

    def match( tag ):
        if ( not tag.name == 'table' ):
            return False
        attrs = dict( tag.attrs )
        classes = attrs.get( 'class', '' ).split( ' ' )
        return 'n3' in classes

    tables = soup.findAll( match ) # should only be one table

    _re_prefix = re.compile( u'(\xa0*)(.*)(\xa0*)' )
    for table in tables:
        rows = table.findAll( lambda tag: tag.name == 'tr' )[ 1 : ]

        org = None
        for i in xrange( len( rows ) ):
            row = rows[ i ]
            if ( row.th ):
                tag = row.th
                text = ''.join( tag.findAll( text = True ) )
                text = text.replace( '&nbsp;', ' ' ).strip()
                text = replaceEscapes( text )
                org = text
                #print 'org', text
            elif ( row.td ):
                labels = ['name', 'title', 'phone', 'mobile', 'email']
                cells = row.findAll( name = 'td', recursive = False )

                data = {}
                data[ 'org' ] = org
                for j in xrange( len( cells ) ):
                    text = ''.join( cells[ j ].findAll( text = True ) )
                    text = text.replace( '&nbsp;', ' ' ).strip()
                    text = replaceEscapes( text )
                    if ( text ):
                        data[ labels[ j ] ] = text
                    #print 'person', labels[ j ], text

                yield data
    
def privacyFilter( row ):
    row = dict( row )
    for key in ('phone', 'mobile', 'email'):
        if ( key in row ):
            del row[ key ]

    return row

# if ( __name__ == '__main__' ):
#     html = open( 'data2.html' ).read()
#     rows = list( parse( html ) )
#     rows = [privacyFilter( row ) for row in rows]
#     print simplejson.dumps( rows )
    #for row in rows:
    #    print '\t'.join( row )