<?php
declare (strict_types = 1);

use PHPUnit\Framework\TestCase;

use damianbal\Facador\BaseContainer;
use damianbal\Facador\Facade;

/**
 * Fake Auth class
 */
class Auth
{
    public function check() 
    {
        return false;
    }

    public function id()
    {
        return 3;
    }
}

/**
 * Facade for Auth
 */
class AuthFacade extends Facade
{
    protected static function getDependencyName()
    {
        return 'auth';
    }
}

final class FacadeTest extends TestCase
{
    public function setUp()
    {
        BaseContainer::getInstance()->set("auth", new Auth);
    }

    public function testCallMethod()
    {
        $this->assertEquals(false, AuthFacade::check());
        $this->assertEquals(3, AuthFacade::id());
    }
}
