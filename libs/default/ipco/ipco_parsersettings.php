<?php

class IPCO_ParserSettings extends IPCO_Base {
	
	const PAGE_HEADER			= "\nclass %s extends %s {\n";
	const PAGE_FOOTER			= "}\n";
	const PAGE_GENERATOR		= "\tpublic function generate() {\n\t\treturn self::callRegion__%s();\n\t}\n";
	
	const FILTER_IF 			= "if(%s) {\n";
	const FILTER_ELSEIF 		= "} else if(%s) {\n";
	const FILTER_ELSE			= "} else {\n";
	const FILTER_FOREACH		= "foreach(%s as %s) {parent::componentPush(%s);\n";
	const FILTER_WHILE			= "while(%s) {\n";
	const FILTER_REGION			= "\tprotected function callRegion__%s() {\n\t\ttry {\n\t\t\t\$_ob = array();\n";
	
	const FILTER_END_IF			= "}\n";
	const FILTER_END_FOREACH	= "parent::componentPop();}\n";
	const FILTER_END_WHILE		= "}\n";
	const FILTER_END_REGION		= "\t\t\treturn implode('', \$_ob);\n\t\t} catch(Exception \$e) { throw new WatCeption('Unable to create template-output.', array(), null, \$e); }\n\t}\n";
	
	const CALL_METHOD			= "parent::processMethod('%s', %s, %s)";
	const CALL_MEMBER			= "parent::processMember('%s', %s)";
	const CALL_SLICE			= "parent::processMember(%s, %s)";
	const CALL_REGION			= "\$_ob []= '' . method_exists(\$this, 'callRegion__%s') ? \$this->callRegion__%s() : '';\n";
	const CALL_INCLUDE			= "\$_ob []= '' . parent::callInclude('%s');\n";
	
	const CONTENT				= "\$_ob []= '' . %s;\n";
	const CONTENTPARSERPART		= "\$_ob []= '' . parent::callContentParser(%s, %s);\n";
	
	const VARIABLE				= "\$_ob []= '' . %s;\n";
	
	public static function getPageHeader($sClassName, $sExtendsName) {
		return sprintf(self::PAGE_HEADER, $sClassName, $sExtendsName);
	}
	
	public static function getPageFooter() {
		return sprintf(self::PAGE_FOOTER);
	}
	
	public static function getPageGenerator($sRegion) {
		return sprintf(self::PAGE_GENERATOR, $sRegion);
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
		return sprintf(self::FILTER_REGION, $sName);
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
		return sprintf(self::FILTER_END_REGION);
	}
	
	public static function getVariable($sExpression) {
		return sprintf(self::VARIABLE, $sExpression);
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
	
	public static function getCallInclude($sIncludePath) {
		return sprintf(self::CALL_INCLUDE, $sIncludePath);
	}

	public static function getCallRegion($sName) {
		return sprintf(self::CALL_REGION, $sName, $sName);
	}
	
	public static function getContent($sContent) {
		return sprintf(self::CONTENT, var_export($sContent, true));
	}
	
	public static function getContentParserPart($sMethod, $aParams) {
		return sprintf(self::CONTENTPARSERPART, var_export($sMethod, true), var_export($aParams, true));
	}
}

?>