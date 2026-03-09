<?php
/**
 * This is just a test example using MaplePHP Unitary testing framework
 * Read more: https://maplephp.github.io/Unitary/
 */

use MaplePHP\Unitary\{Expect, TestCase};

group("Your test subject", function (TestCase $case) {

	// Test example 1 - simple
	$case->expect("YourValue")->isEqualTo("YourValue");

	// Test example 2 - Encapsulate
	$case->describe("Validate a string value")
		->expect(function (Expect $expect) {

			$expect->expect("YourValue")
				->isLength(9)
				->isString()
				->assert("Value is not valid");
		});

});
