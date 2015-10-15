<?php
class XSTokenizerXyz implements XSTokenizer
{
    private $delim = ' '; // 默认按 - 分割
 
    public function __construct($arg = null)
    {
        if ($arg !== null && $arg !== '')
            $this->delim = $arg;
    }

    public function getTokens($value, XSDocument $doc = null)
    {
	return array($value);
    }

}
