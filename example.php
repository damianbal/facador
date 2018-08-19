<?php 

include_once 'vendor/autoload.php';

use damianbal\Facador\Facade;
use damianbal\Facador\BaseContainer as Container;

/**
 * Mail Interface
 */
interface MailInterface {
    public function send($message, $to);
}

/**
 * Mail implementation
 */
class Mail implements MailInterface
{
    public function send($message, $to) {
        echo "Sending $message to $to"."<br>";
    }
}

/**
 * Faster mail implementation
 */
class FasterMail implements MailInterface 
{
    public function send($message, $to) {
        echo "Faster sending $message to $to"."<br>";
    }
}

/**
 * Facade for mail
 */
class MailFacade extends damianbal\Facador\Facade {
    protected static function getDependencyName()
    {
        return 'mail';
    }  
}

/**
 * User class
 */
class User {
    public function __construct($name, $age, MailInterface $mail)
    {
    }
}

/**
 * Bind mail
 */
Container::getInstance()->set('mail', new Mail());

/**
 * Send mail using facade (Mail implementation)
 */
MailFacade::send("hello world", "blabla@blanet.com");

/**
 * Change mail implemenation 
 */
Container::getInstance()->set('mail', new FasterMail());


/**
 * Send mail using facade (Faster Mail implementation)
 */
MailFacade::send("hello world", "blabla@blanet.com");

