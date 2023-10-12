<?php
namespace filebase\system\event\listener;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\WCF;

class FilebaseFileNameOrderListener implements IParameterizedEventListener {    
	/**
	 * @inheritDoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		$this->$eventName($eventObj);
	}

	protected function validateSortField($eventObj) {
		$eventObj->validSortFields[] = 'subject';
		$eventObj->defaultSortField = 'subject';
	}

	protected function beforeReadObjects($eventObj) {
		if ($eventObj->sortField == 'subject') {
			$join = ' INNER JOIN filebase'.WCF_N.'_file_content file_content ON file_content.fileID = file.fileID';
			$eventObj->objectList->sqlJoins .= $join;
			$eventObj->objectList->sqlConditionJoins .= $join;
			$eventObj->objectList->getConditionBuilder()->add('(file_content.languageID IS NULL OR file_content.languageID = ?)', [WCF::getLanguage()->languageID]);

			if (!empty($eventObj->objectList->sqlSelects)) $eventObj->objectList->sqlSelects .= ", ";
			$eventObj->objectList->sqlSelects .= 'file_content.subject';
			
			$eventObj->objectList->sqlOrderBy = 'subject ASC';
		}
	}
}
