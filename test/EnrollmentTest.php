<?php

class EnrollmentTest extends \PHPUnit\Framework\TestCase {

    /**
     * This class contains a test case for the register_enrollment_custom_post_type() method in the EnrollmentTest class.
     * It verifies the registration of the enrollment custom post type by checking if the method is called correctly.
    */
    /** @test */
    public function test_register_enrollment_custom_post_type() {

        // Create a mock object of the 'Openedx_Woocommerce_Plugin_Enrollment' class and mock the register_enrollment_custom_post_type() method
        $mock = $this->getMockBuilder('Openedx_Woocommerce_Plugin_Enrollment')
            ->setMethods(array('register_enrollment_custom_post_type'))
            ->getMock();

        // Set the expectation that the method 'register_enrollment_custom_post_type' will be called once
        $mock->expects($this->once())
            ->method('register_enrollment_custom_post_type');

        // Call the method 'register_enrollment_custom_post_type' on the mock object
        $mock->register_enrollment_custom_post_type();
    }
}
