<?php

require_once 'ConservaTestSuite.php';

class AllTests {

    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('Conserva project');

        $suite->addTest(ConservaTestSuite::suite());

        return $suite;
    }

}
