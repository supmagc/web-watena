<?php namespace Watena\Libs\Base\Ipco;

use Watena\Core\Encoding;

class Parser extends Base {
	
	const STATE_DEFAULT 	= 0;
	const STATE_IPCO 		= 1;
	const STATE_QUOTE 	= 3;
	const STATE_VAR 	= 4;
	const STATE_BQUOTE	= 5;
	const REGION_MAIN		= '__MainRegion__';
	const WHITESPACEFILTER_NONE		= 0;
	const WHITESPACEFILTER_SMART	= 1;
	const WHITESPACEFILTER_ALL		= 2;
	
	private $m_sIdentifier;
	private $m_sClassName;
	private $m_sContent;
	private $m_nDepth;
	private $m_aEndings;
	private $m_nWhitespaceFilter = self::WHITESPACEFILTER_NONE;
	private $m_oContentParser = null;
	private $m_sExtendsTemplate = null;
	private $m_sExtendsFilePath = null;
	private $m_aRegions = array();
	private $m_oRegion = null;
	
	public function __construct($sIdentifier, &$sContent, IPCO $ipco) {
		parent::__construct($ipco);
		$this->m_sIdentifier = $sIdentifier;
		$this->m_sClassName = parent::getIpco()->getTemplateClassName($sIdentifier);
		$this->m_sContent = $sContent;
	}
	
	public function setWhitespaceFilter($nWhitespaceFilter) {
		$this->m_nWhitespaceFilter = $nWhitespaceFilter;
	}
	
	public function setContentParser(IContentParser $oContentParser) {
		$this->m_oContentParser = $oContentParser;
	}
	
	public function getIdentifier() {
		return $this->m_sIdentifier;
	}
	
	public function getClassName() {
		return $this->m_sClassName;
	}
	
	public function getExtendsTemplate() {
		return $this->m_sExtendsTemplate;
	}
	
	public function getExtendsFilePath() {
		return $this->m_sExtendsFilePath;
	}
	
	public function getWhitespaceFilter() {
		return $this->m_nWhitespaceFilter;
	}
	
	public function parse() {
		$this->m_nDepth = 0;
		$this->m_aEndings = array();
		$this->m_sExtends = null;
		$this->m_aRegions = array();
		$this->m_oRegion = new ParserRegion(self::REGION_MAIN, parent::getIpco());
		$this->m_aRegions[$this->m_oRegion->getName()] = $this->m_oRegion;
		$nMark = 0;
		$aBuffer = array(); //ParserSettings::getPageHeader($this->m_sClassName, 'Processor'));
		$nState = self::STATE_DEFAULT;
		$nLength = Encoding::length($this->m_sContent);
 		
		for($i=0 ; $i<$nLength ; ++$i) {
			
			$char1 = Encoding::substring($this->m_sContent, $i, 1);
			$char2 = Encoding::substring($this->m_sContent, $i, 2);
			
			switch($nState) {
				case self::STATE_DEFAULT : 
					if($char2 === ParserSettings::TAG_OPEN) {
						$this->interpretContent(Encoding::substring($this->m_sContent, $nMark, $i-$nMark));
						$nMark = 1 + (++$i);
						$nState = self::STATE_IPCO;
					}
					else if($char2 === ParserSettings::TAG_VAR_OPEN) {
						$this->interpretContent(Encoding::substring($this->m_sContent, $nMark, $i-$nMark));
						$nMark = 1 + (++$i);
						$nState = self::STATE_VAR;
					}
					break;
					
				case self::STATE_IPCO : 
					if($char2 === ParserSettings::TAG_CLOSE) {
						$this->interpretFilter(Encoding::substring($this->m_sContent, $nMark, $i-$nMark));
						$nMark = 1 + (++$i);
						$nState = self::STATE_DEFAULT;
					}
					else if($char1 === '\'') {
						$nState = self::STATE_QUOTE;
					}
					break;
					
				case self::STATE_QUOTE :
					if($char1 === '\'') {
						$nState = self::STATE_IPCO;
					}
					else if($char1 === '\\') {
						$nState = self::STATE_BQUOTE;
					}
					break;
					
				case self::STATE_VAR :
					if($char2 === ParserSettings::TAG_VAR_CLOSE) {
						$this->interpretVariable(Encoding::substring($this->m_sContent, $nMark, $i-$nMark));
						$nMark = 1 + (++$i);
						$nState = self::STATE_DEFAULT;
					}
					break;
					
				case self::STATE_BQUOTE :
					$nState = self::STATE_QUOTE;
					break;
			}
		}
		$this->interpretContent(Encoding::substring($this->m_sContent, $nMark, $nLength-$nMark));
		
		$aBuffer = array();
		$aBuffer []= ParserSettings::getPageHeader($this->m_sClassName, $this->m_sExtendsFilePath ? parent::getIpco()->getTemplateClassName($this->m_sExtendsFilePath) : 'Processor');
		if($this->m_oRegion->hasContent())
			$aBuffer []= ParserSettings::getPageGenerator(self::REGION_MAIN);
		foreach($this->m_aRegions as $oRegion) {
			if($oRegion->getName() != self::REGION_MAIN || $oRegion->hasContent())
				$aBuffer []= $oRegion->build();
		}
		$aBuffer []= ParserSettings::getPageFooter();
		
		return implode('', $aBuffer);
	}
	
	private function interpretContent($sContent) {
		if(Encoding::length($sContent) > 0) {
			$aReturn = array();
			$aContentParserParts = $this->getIpco()->getContentParser()->parseContent($sContent);
			$nOffset = 0;
			if(is_array($aContentParserParts)) {
				foreach($aContentParserParts as $oContentParserPart) {
					$sTrimmable = Encoding::substring($sContent, $nOffset, $oContentParserPart->getStart() - $nOffset);
					if(Encoding::length($sTrimmable = $this->removeWhitespaces($sTrimmable)) > 0)
						$this->m_oRegion->addLine($this->getDepthOffset() . ParserSettings::getContent($sTrimmable));
					$this->m_oRegion->addLine($this->getDepthOffset() . ParserSettings::getContentParserPart(
						$oContentParserPart->getMethod(),
						$oContentParserPart->getParams()
					));
					$nOffset = $oContentParserPart->getStart() + $oContentParserPart->getLength();
				}
			}
			$sTrimmable = Encoding::substring($sContent, $nOffset);
			if(Encoding::length($sTrimmable = $this->removeWhitespaces($sTrimmable)) > 0)
				$this->m_oRegion->addLine($this->getDepthOffset() . ParserSettings::getContent($sTrimmable));
		}
	}
	
	private function interpretFilter($sContent) {
		// FIXME: what about quotes ?
		$aParts = explode_trim(' ', Encoding::trim($sContent));
		$sName = array_shift($aParts);
		switch($sName) {
			case 'if' : $this->interpretIf(new Expression(implode(' ', $aParts), parent::getIpco())); break;
			case 'for' :
			case 'foreach' : $this->interpretForeach(new Expression(implode(' ', $aParts), parent::getIpco())); break;
			case 'while' : $this->interpretWhile(new Expression(implode(' ', $aParts), parent::getIpco())); break;
			case 'else' : $this->interpretElse(); break;
			case 'elseif' : $this->interpretElseIf(new Expression(implode(' ', $aParts), parent::getIpco())); break;
			case 'end' : $this->interpretEnd(count($aParts) > 0 ? $aParts[0] : null); break;
			case 'extends' : $this->interpretExtends(count($aParts) > 0 ? $aParts[0] : null); break;
			case 'include' : $this->interpretInclude(count($aParts) > 0 ? $aParts[0] : null); break;
			case 'region' : $this->interpretRegion($aParts); break;
			case 'call' : $this->interpretCall($aParts); break;
			case 'var' : $this->interpretVar($aParts); break;
		}
	}
	
	public function interpretVariable($sContent) {
		$oCondition = new Expression($sContent, parent::getIpco());
		$this->m_oRegion->addLine($this->getDepthOffset() . ParserSettings::getVariable($oCondition->__toString()));
	}
	
	public function interpretIf(Expression $oCondition) {
		array_push($this->m_aEndings, 'if');
		$this->m_oRegion->addLine($this->getDepthOffset(0, 1) . ParserSettings::getFilterIf($oCondition->__toString()));
	}
	
	public function interpretElseIf(Expression $oCondition) {
		$this->m_oRegion->addLine($this->getDepthOffset(-1, 1) . ParserSettings::getFilterElseIf($oCondition->__toString()));
	}
	
	public function interpretElse() {
		$this->m_oRegion->addLine($this->getDepthOffset(-1, 1) . ParserSettings::getFilterElse());
	}
	
	public function interpretForeach(Expression $oCondition) {
		array_push($this->m_aEndings, 'foreach');
		$this->m_oRegion->addLine($this->getDepthOffset(0, 1) . ParserSettings::getFilterForeach($oCondition->__toString()));
	}
	
	public function interpretWhile(Expression $oCondition) {
		array_push($this->m_aEndings, 'while');
		$this->m_oRegion->addLine($this->getDepthOffset(0, 1) . ParserSettings::getFilterWhile($oCondition->__toString()));
	}
	
	public function interpretEnd($aParts) {
		$sExpectedEnding = array_pop($this->m_aEndings);
		$sGivenEnding = Encoding::toLower(Encoding::trim($aParts));
		if(Encoding::length($sGivenEnding) > 0 && $sExpectedEnding != $sGivenEnding) {
			throw new Exception('Invalid IPCO-tag nesting.', Exception::INVALID_NESTING);
		}
		switch($sExpectedEnding) {
			case 'if' : $this->m_oRegion->addLine($this->getDepthOffset(-1, 0) . ParserSettings::getFilterEndIf()); break;
			case 'foreach' : $this->m_oRegion->addLine($this->getDepthOffset(-1, 0) . ParserSettings::getFilterEndForeach()); break;
			case 'while' : $this->m_oRegion->addLine($this->getDepthOffset(-1, 0) . ParserSettings::getFilterEndWhile()); break;
		}
	}

	public function interpretExtends($sName = null) {
		$sName = Encoding::trim($sName);
		$this->m_sExtendsTemplate = Encoding::length($sName) > 0 ? $sName : null;
		$this->m_sExtendsFilePath = $this->getIpco()->getCallbacks()->getFilePathForTemplate($this->m_sExtendsTemplate);
		if(!file_exists($this->m_sExtendsFilePath) || !is_readable($this->m_sExtendsFilePath))
			throw new Exception(Exception::FILTER_EXTENDS_INVALIDFILE);
	}
	
	public function interpretInclude($sName = null) {
		//$sIncludeFilePath = $this->getIpco()->getCallbacks()->getFilePathForTemplate($sName);
		$this->m_oRegion->addLine(ParserSettings::getCallInclude($sName));
	}
	
	public function interpretRegion(array $aParts) {
		if(count($aParts) > 0) {
			$sOperator = $aParts[0];
			$sName = count($aParts) > 1 ? $aParts[1] : null; 
			if($sOperator === 'end' || $sOperator === 'close' || $sOperator === 'stop') {
				if($sName !== null && $sName !== $this->m_oRegion->getName())
					throw new Exception(Exception::FILTER_REGION_ENDCURRENTMISMATCH);
				if($this->m_oRegion->getName() === self::REGION_MAIN)
					throw new Exception(Exception::FILTER_REGION_ENDNONECURRENT);
				$this->m_oRegion = $this->m_aRegions[self::REGION_MAIN];
			}
			else if($sOperator === 'begin' || $sOperator === 'open' || $sOperator === 'start') {
				if($this->m_oRegion->getName() !== self::REGION_MAIN)
					throw new Exception(Exception::FILTER_REGION_BEGINHASCURRENT);
				if($sName === null)
					throw new Exception(Exception::FILTER_REGION_NONAME);
				$this->m_oRegion = new ParserRegion($sName, parent::getIpco());
				$this->m_aRegions[$this->m_oRegion->getName()] = $this->m_oRegion;
			}
			else if($sOperator === 'use' || $sOperator === 'include') {
				if($sName === null)
					throw new Exception(Exception::FILTER_REGION_NONAME);
				$this->m_oRegion->addLine($this->getDepthOffset() . ParserSettings::getCallRegion($sName));
			}
		}
		else {
			throw new Exception(Exception::FILTER_REGION_NOCOMMAND);
		}
	}
	
	public function interpretCall(array $aParts) {
		if(count($aParts) > 0) {
			$sExpression = count($aParts) > 0 ? ('' . new Expression(implode(' ', $aParts), parent::getIpco())) : 'null';
			$this->m_oRegion->addLine($this->getDepthOffset() . ParserSettings::getCallCall($sExpression));
		}
		else {
			throw new Exception(Exception::FILTER_CALL_NODATA);
		}
	}
	
	public function interpretVar(array $aParts) {
		if(count($aParts) > 0) {
			$sCommand = array_shift($aParts);
			if(count($aParts) > 0) {
				$sName = array_shift($aParts);
				$sExpression = count($aParts) > 0 ? ('' . new Expression(implode(' ', $aParts), parent::getIpco())) : 'null';
				if($sCommand === 'set') {
					$this->m_oRegion->addLine($this->getDepthOffset() . ParserSettings::getCallVarSet($sName, $sExpression));
				}
				if($sCommand === 'increase') {
					$this->m_oRegion->addLine($this->getDepthOffset() . ParserSettings::getCallVarIncrease($sName, $sExpression));
				}
				if($sCommand === 'decrease') {
					$this->m_oRegion->addLine($this->getDepthOffset() . ParserSettings::getCallVarDecrease($sName, $sExpression));
				}
			}
			else {
				throw new Exception(Exception::FILTER_VAR_NONAME);
			}
		}
		else {
			throw new Exception(Exception::FILTER_VAR_NOCOMMAND);
		}
	}
	
	public function removeWhitespaces($sContent) {
		switch($this->m_nWhitespaceFilter) {
			case self::WHITESPACEFILTER_ALL : return Encoding::trim($sContent);
			case self::WHITESPACEFILTER_SMART : return !$this->m_oRegion->hasContent() ? Encoding::trim($sContent) : $sContent;
			default : return $sContent;
		}		
	}
	
	public function getDepthOffset($nPreChange = 0, $nPostChange = 0) {
		$sReturn = "\t\t\t";
		$this->m_nDepth += $nPreChange;
		for($i=0 ; $i<$this->m_nDepth ; ++$i) {
			$sReturn .= "\t";
		}
		$this->m_nDepth += $nPostChange;
		return $sReturn;
	}
}

?>