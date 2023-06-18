<?php

class HelloTest extends \PHPUnit\Framework\TestCase {
	/**
	 * This test is a dummy test that always passes.
     * It was implemented just to test the Git Workflow
     * that implements PHPUnit.
	 */
	/** @test */
     public function test_hello_test() {
		$expected = true;
        $actual = true;

        // Assertion
        if ($actual === $expected) {
            echo "Test Passed!";
        } else {
            echo "Test Failed!";
        }
	}
}