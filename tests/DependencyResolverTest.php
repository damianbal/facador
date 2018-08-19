<?php
declare (strict_types = 1);

use PHPUnit\Framework\TestCase;

use damianbal\Facador\BaseContainer;
use damianbal\Facador\Facade;
use damianbal\Facador\DependencyResolver;

/**
 * Fake Auth class
 */
class UserAuth
{
    protected $signed_in = false;
    protected $user_id = 0;

    public function __construct($id) {
        $this->signed_in = true;
        $this->user_id = $id;
    }

    public function setId($id)
    {
        $this->user_id = $id;
    }

    public function check()
    {
        return $this->signed_in;
    }

    public function id()
    {
        return $this->user_id;
    }
}

class UserAuthFacade extends Facade
{
    protected static function getDependencyName() {
        return 'auth';
    }
}

class UserController 
{
    public $user_id;
    
    public function __construct(UserAuth $auth, $id)
    {
        $this->user_id = $id;
        $auth->setId($this->user_id);

    }

    public function update(UserAuth $auth, $id)
    {
        $this->user_id = $id;
        $auth->setId($this->user_id);
    }
}

interface UserRepositoryInterface {
    public function getUserById($id);
}

class UserRepository implements UserRepositoryInterface 
{
    public function getUserById($id) {
        return [
            'id' => $id,
            'name' => 'User'
        ];
    }
}

class OtherUserRepository implements UserRepositoryInterface
{
    public function getUserById($id) {
        return [
            'id' => $id,
            'name' => 'UserFromOtherRepository'
        ];
    }
}

class UserService 
{
    public $user;

    public function __construct(UserRepositoryInterface $userRepository) 
    {
        $this->user = $userRepository->getUserById(9);    
    }
}

final class DependencyResolverTest extends TestCase
{
    protected $dependencyResolver;

    public function setUp()
    {
        BaseContainer::getInstance()->set("auth", new UserAuth(32));

        $this->dependencyResolver = new DependencyResolver;

        BaseContainer::getInstance()->bind(UserAuth::class, function() {
            return new UserAuth;
        });
    }

    public function testResolveConstructorDependencies()
    {
        $newUserId = 3;

        $a = $this->dependencyResolver->createClass(UserController::class, ['id' => $newUserId]);

        $this->assertEquals($newUserId, $a->user_id);

        // should be $newUserId as UserController updates Auth dependency as well
        $this->assertEquals($newUserId, UserAuthFacade::id());
    }

    public function testResolveMethodDependenciesWithArguments()
    {
        $a = $this->dependencyResolver->createClass(UserController::class, ['id' => 99]);

        $this->dependencyResolver->invoke($a, 'update', ['id' => 32]);

        $this->assertEquals(32, $a->user_id);
        $this->assertEquals(32, UserAuthFacade::id());
    }

    public function testResolveClass()
    {
        BaseContainer::getInstance()->bind(UserRepositoryInterface::class, function () {
            return new OtherUserRepository;
        });
        
        $userService = DependencyResolver::createClass(UserService::class, []);

        $this->assertEquals(9, $userService->user['id']);
    }

    public function testResolveClassWithBinding()
    {
        BaseContainer::getInstance()->reset();

        BaseContainer::getInstance()->bind(UserRepositoryInterface::class, function() {
            return new OtherUserRepository;
        });

        $userService = DependencyResolver::createClass(UserService::class, []);

        $this->assertEquals("UserFromOtherRepository", $userService->user['name']);
    }
}
