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

include_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/PerformanceChecks/classes/checks/class.ilPerformanceCheckGUI.php");
/**
*
* @author Timon Amstutz <timon.amstutz@ilub.unibe.ch>
*
*/
class ilRBACCheckGUI extends ilPerformanceCheckGUI
{
	/**
	 * @var ilRbacSystem
	 */
	protected $rbacsystem = null;

	/**
	 * @var ilUser
	 */
	protected $user = null;


	protected function performCheck(){
		global $rbacsystem;

		$this->rbac = $rbacsystem;

		$user_id = intval($_POST["user_id"]);
		$start_id = intval($_POST["start_id"]);
		$nr_objects = intval($_POST["nr_objects"]);

		try{
			$this->user = new ilObjUser($user_id);
		}catch (Exception $e){
			throw new Exception("There is no User with id: ".$user_id);
		}
		$login = $this->user->getLogin();


		$html = "";


		for($i=$start_id;$i<$nr_objects+$start_id;$i++) {
			try{
				$type = ilObject::_lookupType($i,true);
				if(!$type){
					throw new Exception("Invalid type of object with ref id: ".$i." does the object exist?");
				}
				$access = $this->rbac->checkAccessOfUser($user_id, "visible", $i);
				$html .= "User ".$login." has acces to ref id ".$i. ", type: ".$type.",: ".json_encode($access)."</br>";
			}catch (Exception $e){

				throw new Exception($e->getMessage());
			}

		}

		return $html;
	}
	protected function renderCheck(){
		$toolbar = new ilToolbarGUI();
		include_once("./Services/Form/classes/class.ilTextInputGUI.php");
		$user_input = new ilTextInputGUI($this->parent->publicTxt("user_id"),"user_id");
		$start_input = new ilTextInputGUI($this->parent->publicTxt("start_id"),"start_id");
		$nr_input = new ilTextInputGUI($this->parent->publicTxt("nr_objects"),"nr_objects");

		if($_POST["user_id"]){
			$user_input->setValue(intval($_POST["user_id"]));
		}
		if($_POST["start_id"]){
			$start_input->setValue(intval($_POST["start_id"]));
		}
		if($_POST["nr_objects"]){
			$nr_input->setValue(intval($_POST["nr_objects"]));
		}

		$toolbar->addText($this->parent->publicTxt("rbac_entries"));
		$toolbar->addInputItem($user_input);
		$toolbar->addInputItem($start_input);
		$toolbar->addInputItem($nr_input);


		$reload_btn = ilSubmitButton::getInstance();
		$reload_btn->setCaption('Run',false);
		$reload_btn->setCommand("showCheckResult");
		$this->ctrl->setParameter($this->parent,"check",get_class($this));
		$toolbar->setFormAction($this->ctrl->getLinkTarget($this->parent, 'showCheckResult'));
		$toolbar->addButtonInstance($reload_btn);
		return $toolbar->getHTML();

	}
}
?>
