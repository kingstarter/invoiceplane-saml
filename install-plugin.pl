#!/usr/bin/perl

# Install the InvoicePlaneSaml plugin using this script.
# The script will copy all necessary files into the project. Some files
# will be directly overwritten, some modified.
#
# Author: Steve Hegenbart (steve.hegenbart@kingstarter.de)
# Copyright (c) 2017 Steve Hegenbart und Philipp Schmidt - KingStarter GbR
# All rights reserved

require 5.8.2;

##########################################################################
# Initialization
##########################################################################

### Includes
use strict;
use warnings;

use Getopt::Long;
use File::Basename;
use File::Path qw(make_path);
use File::Copy;
use File::Find;

### Script variables
my $vendordir   = 'vendor/kingstarter/invoiceplane-saml';   # Vendor basedir
my $verbose     = undef;                                    # Verbose flag
my $help        = undef;                                    # Print help option

my $usage = basename($0).' [ --verbose | --help ]';

# Get options
GetOptions(
    # Help options
    'verbose|v'         => \$verbose,
    'help|h|?'          => \$help,
);

if ($help) {
    print << "USAGE_STRING";
  Usage : $usage

    Install the InvoicePlaneSaml plugin using this script.
    The script will copy all necessary files into the project. Some files
    will be directly overwritten, some modified.
    
    Specifically all files within 
    vendor/kingstarter/invoiceplane-saml/src/modules will be copied to 
    their corresponding location within application/modules. Also the 
    application/language/english/custom_lang.php file will be filled with
    plugin specific language strings.

  Script options

    -v | --verbose                Verbose flag. Add more messages. Also print
                                  messages to stdout.

    -h | -? | --help              Print this help message.

USAGE_STRING
    exit 0;
}

##########################################################################
# Supportive functions
##########################################################################

# Make verbose print messages
sub vprint {
    print '[INFO] ' . $_[0] . "\n" if $verbose;
}

# Log error message and die
sub logdie {
    print "\n[ERROR] " . $_[0] . "\n\n";
    die "\n USAGE: $usage\n\n" if $verbose;
    exit 1;
}

# Read file contents
sub fin {
    my ($file) = @_;
    open my $in, '<:encoding(UTF-8)', $file or logdie "Could not open $file for reading $!";
    local $/ = undef;
    my $all = <$in>;
    close $in;
    return $all;
}

# Write content to file 
sub fout {
    my ($file, $content) = @_;
    open my $out, '>:encoding(UTF-8)', $file or die "Could not open $file for writing $!";;
    print $out $content;
    close $out;
    return;
}

##########################################################################
# Evaluation
##########################################################################

# Check if vendor base directory exists 
# (script must be called from IP root dir)
logdie "Vendor basedir lookup failed: $vendordir\n"
     . '  Unable to locate vendor directory, please call the script from the '
     . 'InvoicePlane root directory.'
     unless -d $vendordir;

##########################################################################
# Main routine
##########################################################################

my @files;

### Copy module files
# Collect all package sourcefiles
find ( sub {
    return unless -f;
    my $curr = $File::Find::name; 
    $curr =~ s/$vendordir\/src\///;
    push @files, $curr;
    vprint 'Found file for copying: ' . $curr;
}, $vendordir . '/src/modules');

# Copy all package files to their application location
foreach(@files) {
    copy ($vendordir . '/src/' . $_, 'application/' . $_);
}

### Add API endpoint URL within csrf_exclude_uris
# Read in file
my $conffile = 'application/config/config.php';
my $config = fin($conffile);
# Append to empty array
$config =~ s/\[\'csrf_exclude_uris\'\] = array\(\)/\[\'csrf_exclude_uris\'\] = array\(\'sessions\/samlauth\'\)/;
# Append to non-empty array as first attribute
$config =~ s/\[\'csrf_exclude_uris\'\] = array\(\h*\n/\[\'csrf_exclude_uris\'\] = array\(\n    \'sessions\/samlauth\',\n/;
# Make a check that the entry is not added twice
$config =~ s/\'sessions\/samlauth\',\s*\'sessions\/samlauth\'/\'sessions\/samlauth\'/;
# Write all back
fout($conffile, $config);

### Copy language strings
# Check language strings, append if missing
my $addlang = 0;
my $langfile = 'application/language/english/custom_lang.php';
open(DSTFILE, '<', $langfile);
unless (grep {/saml_sp/} <DSTFILE>) {
    # Language strings not yet included, copy from basefile
    $addlang = 1;
    vprint 'Checked language file, add missing saml plugin strings';
}
close DSTFILE;
if ($addlang) {
    # Reopen all files (the dstfile for resetting the index)
    my $srcfile = $vendordir . '/src/language/saml_lang.php';
    my $dstfile = $langfile;
    my $tmpfile = 'saml.lang.tmp';
    
    vprint 'Make temporary language file ' . $tmpfile; 

    open(SRCFILE, '<', $srcfile) or logdie "Unable to open $srcfile for reading: $!";
    open(DSTFILE, '<', $dstfile) or logdie "Unable to open $dstfile for reading: $!";
    open(TMPFILE, '>', $tmpfile) or logdie "Unable to open $dstfile for writing: $!";
    
    # Copy first part from destination file until the array starts
    while(<DSTFILE>) {
        print TMPFILE;
        if (/^\$lang = array/) {
            last;
        }
    }
    # Copy array contents of saml language file
    my $skip = 1;
    while(<SRCFILE>) {
        if (/^\$lang = array/) {
            $skip = 0;
            next;
        }
        next if $skip;
        last if /^\)\;/;
        print TMPFILE;
    }
    # Copy rest of destination file
    while(<DSTFILE>) {
        print TMPFILE;
    }
    close TMPFILE;
    close SRCFILE;
    close DSTFILE;
    
    # Move temp file to destination file
    vprint 'Move temporary language file to ' . $dstfile;
    File::Copy::move('saml.lang.tmp', 'application/language/english/custom_lang.php') or logdie "Unable to move $tmpfile to $dstfile: $!";
}

exit 0
