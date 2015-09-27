<?php

namespace Frozzare\Tests\Admin_Menu_Tabs;

use Frozzare\Admin_Menu_Tabs\Admin_Menu_Tabs;

class Admin_Menu_Tabs_Test extends \WP_UnitTestCase {

    public function setUp() {
        parent::setUp();
        $this->tabs = Admin_Menu_Tabs::instance();
    }

    public function tearDown() {
        parent::tearDown();
        unset( $this->tabs );
    }

    public function test_adminmenu() {
        $this->tabs->adminmenu();
        $this->expectOutputRegex( '/.*\S.*/' );
        $this->expectOutputRegex( '/admin\-menu\-tab\-edit\sactive/' );
    }

}
