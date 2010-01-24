#!/usr/local/bin/perl -w
# Generate a circle of radius r, with a center in (lat, lon), and output to an OSM area file
# See data at http://spreadsheets.google.com/ccc?key=0AsDoEp3Q6-4MdHJZc1ZkV3Y4cFdLMTlPRW5hRjcxSmc&hl=en
# OSM format described at http://wiki.openstreetmap.org/wiki/.osm
use strict;
use constant PI => 4 * atan2(1, 1);
use constant OSM_USER => 'dandv';
use DateTime;


# config
my $npoints = 20;

# main program
my $rad = 0;

my ($sec,$min,$hour,$mday,$mon,$year) = gmtime;
my $timestamp = sprintf("%d-%02d-%02dT%02d:%02d:%02dZ", $year+1900, $mon+1, $mday, $hour, $min, $sec);

sub circle {
    my $output = '';
    my ($lat, $lon, $radius, $npoints) = @_;
    $npoints |= 20;
    
    for (my $point = 0; $point < $npoints; $point++) {
        $rad += 2 * PI / $npoints;
        my $latp = $lat + $radius * cos($rad);
        my $lonp = $lon + $radius * sin($rad);
        $output .= sprintf('<node id="-%d" lat="%g" lon="%g" version="1" user="%s" visible="true" timestamp="%s"/>' . "\n",
            $point+1, $latp, $lonp, OSM_USER, $timestamp
        );
    }
    return $output;
}

print circle(18.533333, -72.333333, 0.01);
