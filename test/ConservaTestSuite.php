<?php

class ConservaTestSuite {

    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('Conserva test suite');

        $unitTests = require_once 'tests.php';

        foreach ($unitTests as $unitTest) {
            $suite->addTestSuite($unitTest);
        }

        return $suite;
    }

}
