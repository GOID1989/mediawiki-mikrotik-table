CREATE TABLE /*_*/mt_cache_rules (
  `m_id` int(10) unsigned NOT NULL auto_increment,
  `m_date_taken` datetime NOT NULL,
  `m_ip` VARCHAR(15) NOT NULL default '0' UNIQUE,
  `m_html` blob
) /*$wgDBTableOptions*/;
CREATE INDEX /*i*/m_ip ON /*_*/mt_cache_rules (m_ip);