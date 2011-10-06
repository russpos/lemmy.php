<?php
require_once('mustache.php/Mustache.php');

/**
 * Lemmy: one bad-ass mustache
 *
 * @uses Mustache
 * @package
 * @version $id$
 */
class Lemmy extends Mustache {

    public function __construct() {
        date_default_timezone_set('UTC');
        $this->helper = new LemmyHelper();
        parent::__construct();
    }

    public function __toString() {
        return "{{Lemmy}}";
    }

    protected function _findVariableInContext($tag_name, $context) {
        if (substr($tag_name, 0, 1) == '?')
            return $this->_handleQuestion($tag_name, $context);
        return parent::_findVariableInContext($tag_name, $context);
    }

    protected function _handleQuestion($tag_name, $context) {
        $question = substr($tag_name, 1);
        $tokens = explode(' ', $question);

        $expanded = $this->_getVariable($tokens[0]);
        unset($tokens[0]);
        $arg = array_pop($tokens);
        $method = implode('_', $tokens);

        if (method_exists($this->helper, $method))
            return $this->helper->{$method}($expanded, $arg);
        return false;
    }

    protected function _renderEscaped($tag_name, $leading, $trailing) {
        $parts = array($tag_name);
        if (strpos($tag_name, '|')) {
            $parts = explode('|', $tag_name);
        }

        if (sizeOf($parts) <= 1)
            return $leading . htmlentities($this->_getVariable($tag_name), ENT_COMPAT, $this->_charset) . $trailing;
        $filter = $parts[1];
        $tag_name = $parts[0];
        unset($parts[0]);
        unset($parts[1]);
        $args = array_merge(array($this->_getVariable($tag_name)), $parts);
        return $leading.call_user_func_array(array($this->helper, $filter), $args).$trailing;
    }
}

class LemmyHelper {

    /* Filters */
    public function n($text) { return $text; }
    public function b($text) { return nl2br($text); }
    public function h($text) { return htmlspecialchars($text); }
    public function hb($text) { return $this->b($this->h($text)); }
    public function c($name) {
        $cleanName = str_replace(array('/','"', "'", '&', ',', '?', '=','+'), '', $name);
        $cleanName = str_replace(' ', '-', $cleanName);
        return $cleanName;
    }

    public function d($text, $fmt) {
        return strftime($fmt, strtotime($text));
    }

    public function ph($number) {
        $areaCode = substr($number, 0, 3);
        $region = substr($number, 3, 3);
        $lastFour = substr($number, 6, 4);
        return "({$areaCode}) {$region}-{$lastFour}";
    }

    /* Comparers */
    public function is($actual, $asked) { return ($actual == $asked); }
    public function less_than($actual, $asked) { return ($actual < $asked); }
    public function more_than($actual, $asked) { return ($actual > $asked); }


}
