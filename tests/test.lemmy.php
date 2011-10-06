<?php
require_once('../tpt/tpt/tpt.php');
require_once('lemmy.php');

class WhenUsingLemmy extends TPTest {

    function beforeEach() {
        $this->lemmy = new Lemmy();
    }

    function itShouldExist() {
        $this->expect($this->lemmy)->toBeTruthy;
    }

    function itShouldBeAbleToRender() {
        $this->expect($this->lemmy)->toHaveMethod('render');
    }

    function itShouldRenderMustache() {
        $fixture = loadFixture('mustache');
        foreach ($fixture as $case) {
            $r = $this->lemmy->render($case[0], $case[1]);
            $this->expect($r)->toBe($case[2]);
        }
    }
}

class WhenUsingLemmyHelpers extends TPTest {

    function beforeEach() {
        $this->lemmy = new Lemmy();
    }

    function itShouldFilter() {
        $fixture = loadFixture('filters');
        foreach ($fixture as $case) {
            $r = $this->lemmy->render($case[0], $case[1]);
            $this->expect($r)->toBe($case[2]);
        }
    }

    function isShouldCondition() {
        $fixture = loadFixture('conditions');
        foreach ($fixture as $case) {
            $r = $this->lemmy->render($case[0], $case[1]);
            $this->expect($r)->toBe($case[2]);
        }
    }
}

function loadFixture($name) {
    return json_decode(file_get_contents("tests/fixtures/$name.json"));
}


new WhenUsingLemmy();
new WhenUsingLemmyHelpers();
?>
