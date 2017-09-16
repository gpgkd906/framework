<?php
namespace Framework\Module\Cngo\Admin\Model;

use Framework\ModelManager\AbstractModel;

class AdminUser extends AbstractModel
{
    private $adminUsersId;
    private $login;
    private $name;
    private $email;
    private $password;
    private $profileUrl;
    private $profileEditUrl;
    private $avatar;

    public function getAdminUsersId()
    {
        return $this->adminUsersId;
    }

    public function setAdminUsersId($adminUsersId)
    {
        $this->adminUsersId = $adminUsersId;
        return $this;
    }

    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getProfileUrl()
    {
        return $this->profileUrl;
    }

    public function setProfileUrl($profileUrl)
    {
        return $this->profileUrl = $profileUrl;
    }

    public function getProfileEditUrl()
    {
        return $this->profileEditUrl;
    }

    public function setProfileEditUrl($profileEditUrl)
    {
        return $this->profileEditUrl = $profileEditUrl;
    }
}
