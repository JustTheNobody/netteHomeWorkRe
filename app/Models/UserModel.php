<?php

namespace App\Models;

use Nette\Database\Explorer;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;
use Nette\Security\Authenticator;
/**
 * Commented out at lines 31 & 42, for the user frendly output msg
 */ 
use Nette\Security\AuthenticationException;

class UserModel implements Authenticator
{
    private $database;
    private $passwords;
    public $sessionStorage;

    public function __construct(Explorer $database, Passwords $passwords) 
    {
        $this->database = $database;
        $this->passwords = $passwords;
    }

    public function authenticate(string $username, string $password): SimpleIdentity
    {   
        $row = $this->database->table('users')
            ->where('firstname', $username)
            ->fetch();

        if (!$row) {
            //throw new AuthenticationException('User not found.');
            //just for user fendly output, I'm sure that is the best practice
            $autStatus = 'fail';
            $autError = 'user';
            return new SimpleIdentity(
                $autStatus,
                $autError   
            );
        }

        if (!$this->passwords->verify($password, $row->passwords)) {
            //throw new AuthenticationException('Invalid password.');
            //just for user fendly output, I'm sure that is the best practice
            $autStatus = 'fail';
            $autError = 'password';
            return new SimpleIdentity(
                $autStatus,
                $autError   
            );
        }

        return new SimpleIdentity(
            $row->id,
            ['role' => 'user'],
            ['name' => $row->firstname]
        );
    }

    public function registerUser($values)
    {
            $this->database->query(
                'INSERT INTO users ?', [
                'email' => $values->email,
                'firstname' => $values->f_name,
                'lastname' => $values->l_name,
                'passwords' => $values->password ]
            );
            // return auto-increment of the inserted row
            return $this->database->getInsertId();
    }
    
    public function getValue($email)
    {
        return $this->database->fetchField('SELECT email FROM users WHERE email = ?', $email);
    }
}