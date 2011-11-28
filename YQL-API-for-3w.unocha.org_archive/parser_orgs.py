import re
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


def getDeptData():
    deptData_str = \
u"""Artibonite	Gona\xefves	19.45, -72.683333
Centre	Hinche	19.15, -72.016667
Grand'Anse	J\xe9r\xe9mie	18.65, -74.116667
Nippes	Mirago\xe2ne	18.445833, -73.09
Nord	Cap-Ha\xeftien	19.75, -72.2
Nord-Est	Fort-Libert\xe9	19.667778, -71.839722
Nord-Ouest	Port-de-Paix	19.95, -72.833333
Ouest	Port-au-Prince	18.533333, -72.333333
Sud-Est	Jacmel	18.235278, -72.536667
Sud	Les Cayes	18.2, -73.75"""
    rows_in = deptData_str.split( '\n' )
    rows_out = []
    for row_in in rows_in:
        row_out = row_in.split( '\t' )
        rows_out.append( row_out )
        
    return rows_out


def parse( html ):    
    soup = BeautifulSoup( html )#, fromEncoding = 'utf-8' )

    def match( tag ):
        if ( not tag.name == 'table' ):
            return False
        attrs = dict( tag.attrs )
        classes = attrs.get( 'class', '' ).split( ' ' )
        return 'n3' in classes

    tables = soup.findAll( match ) # should only be one table

    deptData = getDeptData()
    dataByDept = dict(
        (row[ 0 ], 
         {'capital': row[ 1 ],
          'coords': row[ 2 ],
          }) 
        for row in deptData
        )
    
    _re_prefix = re.compile( u'(\xa0*)(.*)(\xa0*)' )
    for table in tables:
        rows = table.findAll( lambda tag: tag.name in ('th', 'td') )

        sector = org = dept = capital = coords = None
        for row in rows:
            text = ''.join( row.findAll( text = True ) )

            text = text.replace( '&nbsp;', u'\xa0' ).rstrip( u'\xa0' )
            match = _re_prefix.match( text )
            if ( not match ):
                raise ValueError, 'regex match failed'
            prefix, name, suffix = match.groups()
            name = name.replace( '\t', ' ' ).strip()

            if ( len( prefix ) == 0 ):
                sector = name
            elif ( len( prefix ) == 6 ):
                org = name
            elif ( len( prefix ) == 12 ):
                dept = name
            else:
                raise ValueError, 'bad prefix: %r' % prefix

            if ( any( not x for x in [sector, org, dept] ) ):
                continue

            if ( dept == 'Grande-Anse' ):
                dept = "Grand'Anse"

            capital = dataByDept[ dept ][ 'capital' ]
            coords = dataByDept[ dept ][ 'coords' ]
                
            yield [sector, org, dept, capital, coords]

    
# if ( __name__ == '__main__' ):
#     html = open( 'data.html' ).read()
#     rows = list( parse( html ) )
#     def conv( row ):
#         sector, org, dept, capital, coords = row
#         return {
#             'sector': sector,
#             'org': org,
#             'dept': dept,
#             'capital': capital,
#             'coords': coords,
#             }
#     rows = [conv( row ) for row in rows]
#     print simplejson.dumps( rows )
    #for row in rows:
    #    print '\t'.join( row )