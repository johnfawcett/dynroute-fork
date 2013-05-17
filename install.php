<?php
// Dynamic routing modules
// Copied from ivr and calleridlookup modules
// John Fawcett Sept 2009

if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }

require_once dirname(__FILE__)."/functions.inc.php";

if (! function_exists("out")) {
	function out($text) {
		echo $text."<br />";
	}
}

if (! function_exists("outn")) {
	function outn($text) {
		echo $text;
	}
}

global $db;
global $amp_conf;
$autoincrement = (($amp_conf["AMPDBENGINE"] == "sqlite") || ($amp_conf["AMPDBENGINE"] == "sqlite3")) ? "AUTOINCREMENT":"AUTO_INCREMENT";

$sql = "
	CREATE TABLE IF NOT EXISTS dynroute ( 
		`dynroute_id` INTEGER NOT NULL PRIMARY KEY $autoincrement, 
		`displayname` VARCHAR(50) not null, 
		`sourcetype` VARCHAR(100) default NULL, 
		`mysql_host` varchar(60) default NULL,
		`mysql_dbname` varchar(60) default NULL,
		`mysql_query` text,
		`mysql_username` varchar(30) default NULL,
		`mysql_password` varchar(30) default NULL,
		`odbc_func` varchar(100) default NULL,
		`odbc_query` text,
                `enable_dtmf_input` varchar(8), 
                `timeout` INT, 
                `announcement_id` INT(11),
                `chan_var_name` varchar(255), 
                `chan_var_name_res` varchar(255) 
	);
	";
sql($sql);

$sql = "
CREATE TABLE IF NOT EXISTS dynroute_dests 
( 
	`dynroute_id` INT NOT NULL, 
	`selection` VARCHAR(255), 
	`default_dest` CHAR(1) default 'n',
	`dest` VARCHAR(50) 
)
";
sql($sql);

$sql = "SELECT enable_dtmf_input FROM dynroute";
$check = $db->getRow($sql, DB_FETCHMODE_ASSOC);
if(DB::IsError($check)) {
        // add new field
        $sql = "ALTER TABLE dynroute ADD COLUMN `enable_dtmf_input` VARCHAR(8);";
        $result = $db->query($sql);
        if(DB::IsError($result)) { die_freepbx($result->getDebugInfo()); }
}

$sql = "SELECT timeout FROM dynroute";
$check = $db->getRow($sql, DB_FETCHMODE_ASSOC);
if(DB::IsError($check)) {
        // add new field
        $sql = "ALTER TABLE dynroute ADD COLUMN `timeout` INT;";
        $result = $db->query($sql);
        if(DB::IsError($result)) { die_freepbx($result->getDebugInfo()); }
}

$sql = "SELECT announcement_id FROM dynroute";
$check = $db->getRow($sql, DB_FETCHMODE_ASSOC);
if(DB::IsError($check)) {
        // add new field
        $sql = "ALTER TABLE dynroute ADD COLUMN `announcement_id` INT(11);";
        $result = $db->query($sql);
        if(DB::IsError($result)) { die_freepbx($result->getDebugInfo()); }
}

$sql = "SELECT chan_var_name FROM dynroute";
$check = $db->getRow($sql, DB_FETCHMODE_ASSOC);
if(DB::IsError($check)) {
        // add new field
        $sql = "ALTER TABLE dynroute ADD COLUMN `chan_var_name` VARCHAR(255);";
        $result = $db->query($sql);
        if(DB::IsError($result)) { die_freepbx($result->getDebugInfo()); }
}

$sql = "SELECT chan_var_name_res FROM dynroute";
$check = $db->getRow($sql, DB_FETCHMODE_ASSOC);
if(DB::IsError($check)) {
        // add new field
        $sql = "ALTER TABLE dynroute ADD COLUMN `chan_var_name_res` VARCHAR(255);";
        $result = $db->query($sql);
        if(DB::IsError($result)) { die_freepbx($result->getDebugInfo()); }
}

$sql = "SELECT odbc_func FROM dynroute";
$check = $db->getRow($sql, DB_FETCHMODE_ASSOC);
if(DB::IsError($check)) {
        // add new field
        $sql = "ALTER TABLE dynroute ADD COLUMN `odbc_func` VARCHAR(100);";
        $result = $db->query($sql);
        if(DB::IsError($result)) { die_freepbx($result->getDebugInfo()); }
}

$sql = "SELECT odbc_query FROM dynroute";
$check = $db->getRow($sql, DB_FETCHMODE_ASSOC);
if(DB::IsError($check)) {
        // add new field
        $sql = "ALTER TABLE dynroute ADD COLUMN `odbc_query` TEXT;";
        $result = $db->query($sql);
        if(DB::IsError($result)) { die_freepbx($result->getDebugInfo()); }
}

$sql = "SELECT default_dest FROM dynroute_dests";
$check = $db->getRow($sql, DB_FETCHMODE_ASSOC);
if(DB::IsError($check)) {
        // add new field
        $sql = "ALTER TABLE dynroute_dests ADD COLUMN `default_dest` CHAR(1) default 'n';";
        $result = $db->query($sql);
        if(DB::IsError($result)) { die_freepbx($result->getDebugInfo());
	$sql = "UPDATE dynroute_dests set default_dest='y',selection='' WHERE selection='default';";
	$result = $db->query($sql);
        if(DB::IsError($result)) { die_freepbx($result->getDebugInfo()); }
}

dynroute_init();

