<?php
/**
 * Hooks for MikrotikTable extension
 *
 * @file
 * @ingroup Extensions
 */
require_once( 'routeros_api.class.php' );

class MikrotikTableHooks {
	private $comment_style = "column";

	public static function onParserFirstCallInit( Parser $parser ) {
		$parser->setHook( 'mikrotik', array ('MikrotikTableHooks','mikrotikTableRender') );
		return true;
	}

	public function buildRow(array $row_data, array $columns)
	{
		$row_style = "";
		global $comment_style;
		
		if(array_key_exists("disabled", $row_data)){
			$row_style = $row_data["disabled"] == "true" ? 'class="mt-tr-disabled"' : 'class="mt-tr-default"';
		}

		$row_comment = "";
		if($comment_style == "line") {
			if(array_key_exists("comment", $row_data)){
				$row_comment = "<tr $row_style><td colspan='".count($columns)."'>;;; ".$row_data["comment"]."</td>";
			}
		}

		$row = $row_comment."<tr $row_style>";
		foreach($columns as $column) {
			$div_icon = "";
			if($column == "action") {
				switch ($row_data[$column]) {
				case "accept":
					$div_icon = '<div class="mt-div-default action-accept"></div>';
					break;
				case "drop":
					 $div_icon = '<div class="mt-div-default action-drop"></div>';
					break;
				case "fasttrack-connection":
					 $div_icon = '<div class="mt-div-default action-fasttrack"></div>';
					break;
				case "passthrough":
					 $div_icon = '<div class="mt-div-default action-passthrough"></div>';
					break;
				case "masquerade":
				case "redirect":
					 $div_icon = '<div class="mt-div-default action-masquerade"></div>';
					break;
				case "src-nat":
				case "dst-nat":
				case "netmap":
					 $div_icon = '<div class="mt-div-default action-nat"></div>';
					break;
				}
			}

			$column_value = "";
			if(array_key_exists($column, $row_data))
			{
				if($column == "bytes") { $column_value = (new self)->formatBytes($row_data[$column]); }
				else { $column_value = $row_data[$column]; }

				$row .= "<td>".$div_icon.$column_value."</td>";
			}
			else {
				$row .= "<td>".$div_icon."</td>";
			}
		}
		return $row."</tr>";
	}
	
	public function formatBytes($bytes, $precision = 2) { 
		$units = array('B', 'KB', 'MB', 'GB', 'TB'); 

		$bytes = max($bytes, 0); 
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
		$pow = min($pow, count($units) - 1); 

		$bytes /= pow(1024, $pow);

		return round($bytes, $precision) . ' ' . $units[$pow]; 
	} 
	
	public static function mikrotikTableRender( $input, array $args, Parser $parser, PPFrame $frame ) {
		static $hasRun = false;
		if ($hasRun) return;
		$hasRun = true;

		$parser->disableCache();
		$parser->getOutput()->addModuleStyles( array('ext.mikrotikTable') );

		global $wgLanguageCode;
		global $comment_style;
		$language = $wgLanguageCode;
		$allowed_columns = "";
		$firewall_table = "nat";

		# Checks minimal conf
		if(!isset($args['ip'])) { return "IP not set"; }
		if(!isset($args['login'])) { return "Login not set"; }
		if(!isset($args['password'])) { return "Password not set"; }

		if(isset($args['comment'])) { $comment_style = $args['comment'] == "line" ? "line" : $comment_style; }
		if(isset($args['table'])) { $firewall_table = $args['table'] == "filter" ? "filter" : $firewall_table; }
		if(isset($args['lng'])) { $language = $args['lng']; }
		#Show only specified columns names
		if(isset($args['columns'])) { $allowed_columns = explode(",", $args['columns']); } 
		
		$API = new RouterosAPI();
		#$API->debug = true;
		$API->attempts = 1;
		$API->ssl = true;
		$API->port = isset($args['port']) ? $args['port'] : 8729;

		if ($API->connect($args['ip'], $args['login'], $args['password'])) {
			$API->write("/ip/firewall/".$firewall_table."/print");
			$READ = $API->read(false);
			$ARRAY = $API->parseResponse($READ);

			# Collect all (unique) columns
			$columns = array();
			# Rewrite this. Need check passed names
			if(count($allowed_columns) > 0) { $columns = $allowed_columns; }
			else {
				foreach($ARRAY as $arr){
					foreach($arr as $key => $value){
						#skip comment column if user set $comment_style as 'line'
						if($comment_style == "line" and $key == "comment") { continue; }

						if(!in_array($key, $columns)) { $columns[] = $key; }
					}
				}
			}
			
			$tbl2 = "<table class='wikitable mt-table-default'>";
			# Prepare table headers
			foreach($columns as $column){ $tbl2.= "<th>".(wfMessage( $column )->inLanguage( $language )->isBlank() == 1 ? 
															$column : wfMessage( $column )->inLanguage( $language ) )."</th>"; }
			foreach( $ARRAY as $arr) {
				$tbl2 .= (new self)->buildRow($arr, $columns);
			}
			$API->disconnect();
			return $tbl2."</table>". htmlspecialchars( $input );;
		}
		else { return wfMessage( "mikrotiktable-connection-error" ); }
	}
}