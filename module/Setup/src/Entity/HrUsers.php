<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrUsers
 *
 * @ORM\Table(name="HR_USERS")
 * @ORM\Entity
 */
class HrUsers
{
    /**
     * @var string
     *
     * @ORM\Column(name="USER_NAME", type="string", length=20, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userName;

    /**
     * @var string
     *
     * @ORM\Column(name="PASSWORD", type="string", length=20, nullable=false)
     */
    private $password;

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }


}

