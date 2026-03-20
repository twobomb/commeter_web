<?php

namespace tests\unit\models;

use app\models\User1;

class UserTest extends \Codeception\Test\Unit
{
    public function testFindUserById()
    {
        verify($user = User1::findIdentity(100))->notEmpty();
        verify($user->username)->equals('admin');

        verify(User1::findIdentity(999))->empty();
    }

    public function testFindUserByAccessToken()
    {
        verify($user = User1::findIdentityByAccessToken('100-token'))->notEmpty();
        verify($user->username)->equals('admin');

        verify(User1::findIdentityByAccessToken('non-existing'))->empty();
    }

    public function testFindUserByUsername()
    {
        verify($user = User1::findByUsername('admin'))->notEmpty();
        verify(User1::findByUsername('not-admin'))->empty();
    }

    /**
     * @depends testFindUserByUsername
     */
    public function testValidateUser()
    {
        $user = User1::findByUsername('admin');
        verify($user->validateAuthKey('test100key'))->notEmpty();
        verify($user->validateAuthKey('test102key'))->empty();

        verify($user->validatePassword('admin'))->notEmpty();
        verify($user->validatePassword('123456'))->empty();        
    }

}
