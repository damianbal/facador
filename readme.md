# Facador
> Simple Dependency Resolver for PHP with Facade support

## Features
* Create class with dependency injection
* Invoke method with dependency injection
* Simple Facade class which allows to create Facades for dependencies in container
* Class binding (bind class/interface to a function which returns a object)

## How to use Facador?

How to create class and have dependency injected?
```php
$myClass = DependencyResolver::createClass(YourClassName::class, ['page' => 3]);
```

How to invoke method on class and have dependency injected?
```php
DependencyResolver::invoke($myClass, 'update', ['username' => 'MynewName']);
```

How to bind?
```php
BaseContainer::getInstance()->bind(YourInterface::class, function() {
    return new InterfaceImplementation();
});
```

How to set dependency?
```php
BaseContainer::getInstance()->set("dependencyname", new SomeClass(3));
```

## Running tests

```sh
vendor/bin/phpunit --bootstrap="vendor/autoload.php" tests/
```

## Meta

Damian Balandowski – balandowski@icloud.com