# [RPiWeb](https://sites.google.com/site/davidemadrisan/opensource/rpiweb)

RPiWeb is a web application used to control your Raspberry Pi running on [openmamba](http://openmamba.org),
It is intended to be used with PHP 5.4 and lighttpd.

RPiWeb is a forking of [Bioshox/Raspcontrol](https://github.com/Bioshox/Raspcontrol).

## How to setup:

### Getting the source

If you have Git installed you can clone the repo

	git clone https://github.com/madrisan/RPiWeb.git

### Getting it running

Install the openmamba packages lighttpd and php

	smart install lighttpd php

Configure the lighttpd web server by uncommenting the line

	include "conf.d/fastcgi.conf"

in the configuration file */etc/lighttpd/modules.conf* and by appending the following lines

	fastcgi.server = ( 
	  ".php" => ((
	    "socket" => socket_dir + "/php-fastcgi.socket",
	    "bin-path" => "/usr/bin/php-cgi",
	    "docroot" => "/srv/www/htdocs/"
	  )))
	fastcgi.map-extensions = ( ".html" => ".php" )

to the file */etc/lighttpd/conf.d/fastcgi.conf*.

Make sure than *cgi.fix_pathinfo* is set to the default value

	cgi.fix_pathinfo = 1

in the */etc/php/php.ini* file.

The PHP module for configuring the wireless interface requires the following configuration 
for *sudo*

	Defaults:lighttpd !requiretty
	lighttpd  ALL = NOPASSWD: /usr/sbin/config-rpi-wireless, \
	                          /usr/sbin/iwlist wlan0 scanning

