<?php

include_once("./Services/Repository/classes/class.ilRepositoryObjectPlugin.php");
 
/**
* Example repository object plugin
*
* @author Alex Killing <alex.killing@gmx.de>
* @version $Id$
*
*/
class ilPerformanceChecksPlugin extends ilRepositoryObjectPlugin
{
	function getPluginName()
	{
		return "PerformanceChecks";
	}

	public function uninstallCustom(){

	}
}
?>
