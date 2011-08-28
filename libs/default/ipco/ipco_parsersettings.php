<?php

class IPCO_ParserSettings extends IPCO_Base {
	
	const PAGE_HEADER			= '
class %s extends %s {

	public function generate() {
		try {
			$_ob = \'\';
';
	const PAGE_FOOTER			= '			return $_ob;
		}
		catch(Exception $e) {
			throw new WatCeption(\'Unable to create template-output.\', array(), null, $e); 
		}
	}
}
';
	
	const FILTER_IF 			= "if(%s) {\n";
	const FILTER_ELSEIF 		= "} else if(%s) {\n";
	const FILTER_ELSE			= "} else {\n";
	const FILTER_FOREACH		= "foreach(%s as %s) {parent::componentPush(%s);\n";
	const FILTER_WHILE			= "while(%s) {\n";
	const FILTER_BEGIN_REGION	= "\tprotected function %s() {\n";
	
	const FILTER_END_IF			= "}\n";
	const FILTER_END_FOREACH	= "parent::componentPop();}\n";
	const FILTER_END_WHILE		= "}\n";
	const FILTER_END_REGION		= "\t}\n";
	
	const CALL_METHOD			= "parent::processMethod('%s', %s, %s)";
	const CALL_MEMBER			= "parent::processMember('%s', %s)";
	const CALL_SLICE			= "parent::processMember(%s, %s)";
	
	const CONTENT				= '$_ob .= \'%s\';
';
	const CONTENTPARSERPART		= '$_ob .= \'\' . parent::callContentParser(\'%s\', %s);
';
	
	public static function getPageHeader($sClassName, $sExtendsName) {
		return sprintf(self::PAGE_HEADER, $sClassName, $sExtendsName);
	}
	
	public static function getPageFooter() {
		return sprintf(self::PAGE_FOOTER);
	}
	
	public static function getFilterIf($sCondition) {
		return sprintf(self::FILTER_IF, $sCondition);
	}
	
	public static function getFilterElseIf($sCondition) {
		return sprintf(self::FILTER_ELSEIF, $sCondition);
	}
	
	public static function getFilterElse() {
		return sprintf(self::FILTER_ELSE);
	}
	
	public static function getFilterForeach($sCondition) {
		return sprintf(self::FILTER_FOREACH, $sCondition, '$_comp', '$_comp');
	}
	
	public static function getFilterWhile($sCondition) {
		return sprintf(self::FILTER_WHILE, $sCondition);
	}
	
	public static function getFilterRegion($sName) {
		return sprintf(self::FILTER_REGION_BEGIN, $sName);
	}
	
	public static function getFilterEndIf() {
		return sprintf(self::FILTER_END_IF);
	}
	
	public static function getFilterEndForeach() {
		return sprintf(self::FILTER_END_FOREACH);
	}
	
	public static function getFilterEndWhile() {
		return sprintf(self::FILTER_END_WHILE);
	}
	
	public static function getFilterEndRegion() {
		return sprintf(self::FILTER_REGION_END);
	}
	
	public static function getCallMethod($sName, $sParams, $sBase) {
		return sprintf(self::CALL_METHOD, $sName, $sParams, $sBase);
	}
	
	public static function getCallMember($sName, $sBase) {
		return sprintf(self::CALL_MEMBER, $sName, $sBase);
	}
	
	public static function getCallSlice($sSlice, $sBase) {
		return sprintf(self::CALL_SLICE, $sSlice, $sBase);
	}

	public static function getCallRegion($sName) {
		return '';
	}
	
	public static function getContent($sContent) {
		return sprintf(self::CONTENT, addcslashes($sContent, '\''));
	}
	
	public static function getContentParserPart($sMethod, $aParams) {
		$sParams = 'unserialize(\'' . addcslashes(serialize($aParams), '\'') . '\')';
		return sprintf(self::CONTENTPARSERPART, addcslashes($sMethod, '\''), $sParams);
	}
}

?>