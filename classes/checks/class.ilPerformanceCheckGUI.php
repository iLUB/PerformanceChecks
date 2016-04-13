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


/**
*
* @author Timon Amstutz <timon.amstutz@ilub.unibe.ch>
*
*/
abstract class ilPerformanceCheckGUI
{
	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var ilCtrl $ctrl
	 */
	protected $ctrl;

	/**
	 * @var ilObjPerformanceChecksGUI $parent
	 */
	protected $parent;

	public function __construct($parent) {
		global $ilCtrl, $tpl;

		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->parent = $parent;
	}

	public function showCheck(){
		$this->tpl->setContent($this->renderCheck());
	}

	public function showCheckResult(){

		try{
			$time_start = microtime(true);
			$output = $this->performCheck();
			$time_end = microtime(true);
			ilUtil::sendInfo($this->parent->publicTxt("total_exec_time")." ".($time_end - $time_start)."s");
		}catch (Exception $e){
			ilUtil::sendFailure($e->getMessage());
		}

		$this->tpl->setContent($this->renderCheck().$output);
	}

	abstract protected function performCheck();
	abstract protected function renderCheck();

}
?>