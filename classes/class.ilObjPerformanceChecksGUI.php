<?php
/*
	+-----------------------------------------------------------------------------+
	| ILIAS open source                                                           |
	+-----------------------------------------------------------------------------+
	| Copyright (c) 1998-2009 ILIAS open source, University of Cologne            |
	|                                                                             |
	| This program is free software; you can redistribute it and/or               |
	| modify it under the terms of the GNU General Public License                 |
	| as published by the Free Software Foundation; either version 2              |
	| of the License, or (at your option) any later version.                      |
	|                                                                             |
	| This program is distributed in the hope that it will be useful,             |
	| but WITHOUT ANY WARRANTY; without even the implied warranty of              |
	| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
	| GNU General Public License for more details.                                |
	|                                                                             |
	| You should have received a copy of the GNU General Public License           |
	| along with this program; if not, write to the Free Software                 |
	| Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
	+-----------------------------------------------------------------------------+
*/


include_once("./Services/Repository/classes/class.ilObjectPluginGUI.php");

/**
* @author Timon Amstutz <timon.amstutz@ilub.unibe.ch>
*
*
* @ilCtrl_isCalledBy ilObjPerformanceChecksGUI: ilRepositoryGUI, ilAdministrationGUI, ilObjPluginDispatchGUI
* @ilCtrl_Calls ilObjPerformanceChecksGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilCommonActionDispatcherGUI
*
*/
class ilObjPerformanceChecksGUI extends ilObjectPluginGUI
{
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs_gui;

	/**
	 * @var ilCtrl $ctrl
	 */
	protected $ctrl;

	protected $checks = array();
	/**
	* Initialisation
	*/
	protected function afterConstructor()
	{
		foreach(array_diff(scandir("./Customizing/global/plugins/Services/Repository/RepositoryObject/PerformanceChecks/classes/checks/instances"), array('..', '.')) as $file){
			$class = ltrim(rtrim($file,".php"),".class");
			$this->checks[] = $class;
		}
	}
	
	/**
	* Get type.
	*/
	final function getType()
	{
		return "xpc0";
	}
	
	/**
	* Handles all commmands of this class, centralizes permission checks
	*/
	function performCommand($cmd)
	{
		switch ($cmd)
		{
			case "editProperties":
			case "updateProperties":
			//case "...":
				$this->checkPermission("write");
				$this->$cmd();
				break;
			
			case "showCheck":
			case "showCheckResult":
				$this->setSubTabs();
				$this->checkPermission("read");
				$this->tabs_gui->activateTab("content");
				$class = $_GET["check"]?$_GET["check"]:$this->checks[0];
				$this->tabs_gui->activateSubTab($class);
				include_once("checks/instances/class.".$class.".php");
				$checking_class = new $class($this);
				$checking_class->$cmd();
				break;
		}
	}

	public function publicTxt($key){
		return $this->txt($key);
	}

	/**
	* After object has been created -> jump to this command
	*/
	function getAfterCreationCmd()
	{
		return "editProperties";
	}

	/**
	* Get standard command
	*/
	function getStandardCmd()
	{
		return "showCheck";
	}

	/**
	* Set tabs
	*/
	function setTabs()
	{
		global $ilAccess;


		if ($ilAccess->checkAccess("read", "", $this->object->getRefId()))
		{
			$this->ctrl->setParameter($this,"check",$this->checks[0]);
			$this->tabs_gui->addTab("content", $this->txt("Content"), $this->ctrl->getLinkTarget($this, "showCheck"));
		}

		// standard info screen tab
		$this->addInfoTab();

		// a "properties" tab
		if ($ilAccess->checkAccess("write", "", $this->object->getRefId()))
		{
			$this->tabs_gui->addTab("properties", $this->txt("properties"), $this->ctrl->getLinkTarget($this, "editProperties"));
		}

		// standard epermission tab
		$this->addPermissionTab();
	}

	protected function setSubTabs(){
		foreach($this->checks as $check){
			$this->ctrl->setParameter($this,"check",$check);
			$this->tabs_gui->addSubTab($check, $this->txt($check), $this->ctrl->getLinkTarget($this, "showCheck"));
		}
	}
	

// THE FOLLOWING METHODS IMPLEMENT SOME EXAMPLE COMMANDS WITH COMMON FEATURES
// YOU MAY REMOVE THEM COMPLETELY AND REPLACE THEM WITH YOUR OWN METHODS.

//
// Edit properties form
//

	/**
	* Edit Properties. This commands uses the form class to display an input form.
	*/
	function editProperties()
	{
		global $tpl, $ilTabs;
		
		$ilTabs->activateTab("properties");
		$this->initPropertiesForm();
		$this->getPropertiesValues();
		$tpl->setContent($this->form->getHTML());
	}
	
	/**
	* Init  form.
	*
	* @param        int        $a_mode        Edit Mode
	*/
	public function initPropertiesForm()
	{
		global $ilCtrl;
	
		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$this->form = new ilPropertyFormGUI();
	
		// title
		$ti = new ilTextInputGUI($this->txt("title"), "title");
		$ti->setRequired(true);
		$this->form->addItem($ti);
		
		// description
		$ta = new ilTextAreaInputGUI($this->txt("description"), "desc");
		$this->form->addItem($ta);
		
		// online
		$cb = new ilCheckboxInputGUI($this->lng->txt("online"), "online");
		$this->form->addItem($cb);
		
		// option 1
		$ti = new ilHiddenInputGUI($this->txt("option_one"), "op1");
		//$ti->setMaxLength(10);
		//$ti->setSize(10);
		$this->form->addItem($ti);



		$this->form->addCommandButton("updateProperties", $this->txt("save"));
	                
		$this->form->setTitle($this->txt("edit_properties"));
		$this->form->setFormAction($ilCtrl->getFormAction($this));
	}
	
	/**
	* Get values for edit properties form
	*/
	function getPropertiesValues()
	{
		$values["title"] = $this->object->getTitle();
		$values["desc"] = $this->object->getDescription();
		$values["online"] = $this->object->getOnline();
		$values["op1"] = $this->object->getOptionOne();
		$this->form->setValuesByArray($values);
	}
	
	/**
	* Update properties
	*/
	public function updateProperties()
	{
		global $tpl, $lng, $ilCtrl;
	
		$this->initPropertiesForm();
		if ($this->form->checkInput())
		{
			$this->object->setTitle($this->form->getInput("title"));
			$this->object->setDescription($this->form->getInput("desc"));
			$this->object->setOptionOne($this->form->getInput("op1"));
			$this->object->setOnline($this->form->getInput("online"));
			$this->object->update();
			ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
			$ilCtrl->redirect($this, "editProperties");
		}

		$this->form->setValuesByPost();
		$tpl->setContent($this->form->getHtml());
	}
}
?>
