require 'rubygems'

# HTML parser, http://nokogiri.org/
require 'nokogiri'

# Haml parser, gem install haml, http://haml-lang.com/
# Note: to parse Textile, you'll need RedCloth installed, get install RedCloth, http://redcloth.org/
require 'haml'

# Haml parser wrapper, gem install tilt, http://github.com/rtomayko/tilt
require 'tilt'

# settings
slide_content_file_path = 'slides.haml'
custom_css_file_path = 'examples/haml/custom.css'
base_html_file_path = '../../main.html'
out_file_path = '../../index.html'

# load & render haml template
slide_html = Tilt.new( slide_content_file_path ).render

# load base html
base_html_file = File.open( base_html_file_path )
parsed_base_html = Nokogiri::parse( base_html_file )
base_html_file.close

# parse slide html & add it to base
parsed_slide_html = Nokogiri::XML::DocumentFragment::parse( slide_html )
parsed_base_html.at('.slides').add_child( parsed_slide_html )

# add custom css, if defined, as include in head
if defined? custom_css_file_path
  parsed_base_html.at('head link').add_next_sibling( Nokogiri::XML::DocumentFragment::parse( 
    '<link rel="stylesheet" type="text/css" href="%s" media="screen, projection" />' % custom_css_file_path 
  ))
end

# add custom js, if defined, as include at bottom of file
if defined? custom_js_file_path
  parsed_base_html.at('body script').add_next_sibling( Nokogiri::XML::DocumentFragment::parse( 
    '<script type="text/javascript" charset="utf-8" src="%s"></script>' % custom_js_file_path 
  ))
end

File.open( out_file_path, 'w' ).write( parsed_base_html.to_html );

