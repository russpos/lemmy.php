<?php

class Tester {

    public $assertions = 0;
    public $failures   = array();
    public $passes     = array();

    public function beforeEach() {}
    public function afterEach()  {}
    public function beforeAll()  {}
    public function afterAll()   {}

    protected function expect($item) {
        return new Expect($item, $this);
    }

    public function __construct() {
        $this->beforeAll();
        $this->_runTests(get_class_methods($this));
        $this->afterAll();
        $this->summarize();
    }

    public function summarize() {
        $failures = sizeOf($this->failures);
        $passes = $this->assertions - $failures;
        $case = ucfirst($this->toWords(get_class($this)));
        echo "\n{$case} - {$passes}/{$this->assertions}\n=====================\n";
        foreach ($this->failures as $fail) {
            echo $fail;
        }
        foreach ($this->passes as $pass) {
            echo $pass;
        }
        if (empty($this->failures)) echo "  -> All tests passed!\n";
    }

    protected function _runTests($methods) {
        foreach ($methods as $method) {
            if (strpos($method, 'it') !== false)
                $this->_runTest($method);
        }
    }

    public function toWords($called) {
        return strtolower(preg_replace('/(?!^)[[:upper:]]/',' \0', $called));
    }

    public function assert($val) {
        $this->assertions[] = $val;
    }

    protected function _runTest($method) {
        $this->method = $method;
        $this->beforeEach();
        $this->{$method}();
        $this->afterEach();
    }
}

class Expect {

    public function __construct($value, $tester) {
        $this->value = $value;
        $this->tester = $tester;
        $this->invert = false;
    }

    public function __get($thing) {
        if ($thing == 'not') return $this->inverse();
        call_user_func(array($this, $thing));
    }

    public function __call($called, $args) {
        $method = '_'.$called;
        $val = call_user_func_array(array($this, $method), $args);
        $this->tester->assertions++;
        if ($this->invert) $val = !$val;

        $text = $this->tester->toWords($called);
        $when = ucfirst($this->tester->toWords(get_class($this->tester)));
        $it   = $this->tester->toWords($this->tester->method);
        if (empty($args)) $args = "''";
        $args = $args[0];
        $desc = " : $when, $it : Expected {$this->value} {$text} {$args}\n";

        if (!$val) {
            $this->tester->failures[] = $this->red('FAIL').$desc;
        } else {
            $this->tester->passes[] = $this->green('PASS').$desc;
        }
    }

    protected function green($text) {
        return chr(27).'[0;32m'.$text.chr(27).'[0m'.chr(27);
    }

    protected function red($text) {
        return chr(27).'[0;31m'.$text.chr(27).'[0m'.chr(27);
    }

    public function inverse() {
        $this->invert = true;
        return $this;
    }

    public function _toBeTruthy() {
        return $this->value == true;
    }

    public function _toBeFalsy() {
        return $this->value == false;
    }

    public function _toBe($val) {
        return $this->value === $val;
    }

    public function _toEqual($val) {
        return $this->value == $val;
    }

    public function _toHave($val) {
        return isset($this->value[$val]);
    }

    public function _toHaveCount($val) {
        return count($this->value) == $val;
    }

    public function _toHaveMethod($val) {
        return method_exists($this->value, $val);
    }
}

