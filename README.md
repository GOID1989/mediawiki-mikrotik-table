# mediawiki-mikrotik-table

Tag extension for Mediawiki adding support parse <mikrotik /> html-like-tag on page.
Extension use external API php-lib to connect Mikrotik device and get data.

## Usage
Start editing page, add ```<mikrotik />``` tag with options:
 - IP - address of device. Required
 - Login - Username with access. Required
 - Password - Password of user. Required
 - Port - API port. Optional (default 8729)
 - Table - Which rules to scan: filter or nat. Optional (default filter)
 - Comment - comments style: separated line or column. Optional (default column)
 - Columns - list of rule properties to display. Optional (default all unique properties)
 - Lng - allow to set specific language for column names. Optional (default MW Language, avaliable en\ru)
 
Example:
```html
<mikrotik ip="192.168.88.1" port="8729" login="wm" password="bestpassword" lng="ru" table="nat" comment="line" columns=".id,chain,action,src-address,dst-address,to-ports,protocol,comment,dst-port,bytes,packets,to-addresses" />
```
 
## Features
 - Styling rows
 - Translating\renaming (column names)
 - Column reordering (use column parameter to reorder)
 
## Installation/Prerequisites
 - Copy files to MW extensions folder
 - Add ```wfLoadExtension( 'MikrotikTable' );``` to LocalSettings.php
 - PHP API Mikrotik https://github.com/BenMenking/routeros-api
 - Enable API on Mikrotik
 - Allow port access (8729 default)
 - Create user on Mikrotik (minimal access - read,api)
 
## TODO
 - [ ] Expanding access lists
 - [ ] Option to create tabbed control
 
## Tested
 - Mikrotik (ROS 6.40.5)
 - MediaWiki 1.28.2
 - PHP 5.6.31 (apache2handler)