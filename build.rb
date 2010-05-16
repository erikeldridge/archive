require 'rubygems'

# http://nokogiri.org/
require 'nokogiri'

# http://deveiate.org/projects/BlueCloth/
# sudo gem install bluecloth
require 'bluecloth'

slide_markdown_file_path = 'slides.markdown'
template_html_file_path = 'template.html'
out_file_path = 'index.html'

slide_md = IO.read( slide_markdown_file_path )
slide_html_strings = BlueCloth.new( slide_md ).to_html.split('<hr/>')

template_html_file = File.open( template_html_file_path )
parsed_template = Nokogiri::parse( template_html_file )
template_html_file.close

parsed_template.at('.intro').inner_html = slide_html_strings.shift

slide_node = parsed_template.css('.slide')[1]

slide_html_strings.reverse.each { | html_string | 
  clone = slide_node.clone()
  
  parsed_html = Nokogiri::XML::DocumentFragment::parse( html_string )
  
  h3 = parsed_html.at('h3').remove()
  
  if h3
    clone.at('header').add_child( h3 )
  end
  
  clone.at('section').add_child( parsed_html )
  
  slide_node.add_next_sibling( clone )
}

slide_node.remove()

File.open( out_file_path, 'w' ).write( parsed_template.to_html );

