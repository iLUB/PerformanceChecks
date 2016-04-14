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
class ilContainerListCheckGUI extends ilPerformanceCheckGUI
{
	protected function performCheck(){
		$loops = intval($_POST["nr_entries"]);

		$block = new ilTemplate("tpl.container_list_block.html", true, true, "Services/Container/");
		$block->setVariable("BLOCK_HEADER_CONTENT", "content");

		for($i=0;$i<$loops;$i++){
			$block->setCurrentBlock("container_standard_row");

			$item = new ilTemplate("tpl.container_list_item.html", true, true, "Services/Container/");
			$block->setVariable("ROW_ID", "id=perfcheck"+$i );

			include_once("./Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php");
			$action_list = new ilAdvancedSelectionListGUI();
			$action_list->setId("id_action_list_" . $i);
			$action_list->setListTitle("Actions");
			$action_list->setAsynch(true);
			$item->setVariable("COMMAND_SELECTION_LIST", $action_list->getHTML());
			$item->setVariable("DIV_CLASS", "ilContainerListItemOuter");
			//$item->touchBlock("d_1");
			$item->setVariable("TXT_DESC","Sample Text");
			$item->setVariable("TXT_TITLE", "Testing Test ".($i+1));
			$item->setVariable("SRC_ICON", ilUtil::getImagePath('marked.svg'));

			$block->setVariable("BLOCK_ROW_CONTENT", $item->get());
			$block->parseCurrentBlock();

		}
		return $block->get();
	}
	protected function renderCheck(){
		include_once("./Services/Form/classes/class.ilTextInputGUI.php");
		$toolbar = new ilToolbarGUI();
		$input = new ilTextInputGUI($this->parent->publicTxt("nr_entries"),"nr_entries");
		if($_POST["nr_entries"]){
			$input->setValue(intval($_POST["nr_entries"]));
		}
		$toolbar->addInputItem($input);
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
