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
     * @ORM\Column(name="USER_NAME", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userName;

    /**
     * @var string
     *
     * @ORM\Column(name="PASSWORD", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $password;


    /**
     * Get userName
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return HrUsers
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
}

