CREATE TABLE /*_*/mt_cache_rules (
  `m_id` int(10) unsigned NOT NULL auto_increment PRIMARY KEY,
  `m_date_taken` datetime NOT NULL,
  `m_ip` VARCHAR(15) NOT NULL default '0',
  `m_html` blob
) /*$wgDBTableOptions*/;
CREATE INDEX /*i*/m_ip ON /*_*/mt_cache_rules (m_ip);