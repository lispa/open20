<?php
/* SVN FILE: $Id: SassVariable.php 49 2010-04-04 10:51:24Z chris.l.yates $ */
/**
 * SassVariable class file.
 * @package      PHamlP
 * @subpackage  Sass.script.literals
 */

/**
 * SassVariable class.
 * @package      PHamlP
 * @subpackage  Sass.script.literals
 */
class SassScriptVariable
{
  /**
   * Regex for matching and extracting Variables
   */
  const MATCH = '/^(?<!\\\\)(?(?!!important\b)[!\$]([\w-]+))/';

  /**
   * @var string name of variable
   */
  private $name;

  /**
   * SassVariable constructor
   * @param string $value value of the Variable type
   */
  public function __construct($value)
  {
    $this->name = substr($value, 1);
  }

  /**
   * Returns the SassScript object for this variable.
   * @param SassContext $context context of the variable
   * @return SassLiteral the SassScript object for this variable
   */
  public function evaluate($context)
  {
    return $context->getVariable($this->name);
  }

  /**
   * Returns a value indicating if a token of this type can be matched at
   * the start of the subject string.
   * @param string $subject the subject string
   * @return mixed match at the start of the string or false if no match
   */
  public static function isa($subject)
  {
    // we need to do the check as preg_match returns a count of 1 if
    // subject == '!important'; the match being an empty match
    return (preg_match(self::MATCH, $subject, $matches) ? (empty($matches[0]) ? false : $matches[0]) : false);
  }
}
