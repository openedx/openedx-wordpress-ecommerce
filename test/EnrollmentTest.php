<?php

use App\model\Openedx_Woocommerce_Plugin_Enrollment;
use App\model\Openedx_Woocommerce_Plugin_Post_Type;


class EnrollmentTest extends \PHPUnit\Framework\TestCase {

    /**
     * This class contains a test case for the register_enrollment_custom_post_type() method in the EnrollmentTest class.
     * It verifies the registration of the enrollment custom post type by checking if the method is called correctly.
    */
    /** @test */
    public function test_register_enrollment_custom_post_type() {

        $admin = $this  ->getMockBuilder('App\admin\Openedx_Woocommerce_Plugin_Admin')
                        ->setConstructorArgs(['openedx-woocommerce-plugin', '1.0.0','test'])
                        ->onlyMethods(['createEnrollmentClass'])
                        ->getMock();        

        $admin = $this  ->getMockBuilder('App\admin\Openedx_Woocommerce_Plugin_Admin')
                        ->setConstructorArgs(['openedx-woocommerce-plugin', '1.0.0','test'])
                        ->onlyMethods(['createPostType'])
                        ->getMock();  
                        
        $admin = $this  ->getMockBuilder('App\admin\Openedx_Woocommerce_Plugin_Admin')
                        ->setConstructorArgs(['openedx-woocommerce-plugin', '1.0.0','test'])
                        ->onlyMethods(['register_post_type'])
                        ->getMock();    
        

        $enrollment_cpt_options = array(
            'public'            => false,
            'hierarchical'      => false,
            'show_ui'           => true,
            'show_in_menu'      => true,
            'show_in_nav_menus' => true,
            'supports'          => array( '' ),
            'menu_icon'         => 'dashicons-admin-post',
        );       

        $admin  ->expects($this->once())
                ->method('register_post_type')
                ->with('openedx_enrollment', 'Open edX Enrollment Requests', 'Open edX Enrollment Request', '', $enrollment_cpt_options)
                ->willReturn(Openedx_Woocommerce_Plugin_Post_Type::class);         

        $output = $admin->register_post_type('openedx_enrollment', 'Open edX Enrollment Requests', 'Open edX Enrollment Request', '', $enrollment_cpt_options);
        $this->assertEquals(Openedx_Woocommerce_Plugin_Post_Type::class, $output);
    }
}
