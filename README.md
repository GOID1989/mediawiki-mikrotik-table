# mediawiki-mikrotik-table

Rewrited extension to use extension.json for setup\register plugin in Mediawiki
Add module styling, translating, table cache

## Prerequisites
 - PHP API Mikrotik https://github.com/BenMenking/routeros-api
 - Enable API on Mikrotik
 - Allow port access (8729 default)
 - Create user on Mikrotik (minimal access - read,api)
 - Run update.php to prepare MW Database
 
## TODO
 [] Expanding lists
 [] Option to create tabbed control
 [x] Store "cache" in DB
 
## Tested
 - Mikrotik (ROS 6.40.5)
 - MediaWiki 1.28.2
 - PHP 5.6.31 (apache2handler)