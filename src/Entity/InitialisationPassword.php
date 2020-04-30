<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;


class InitialisationPassword
{

    private $id;

    private $ancienPassword;

    /**
     * @Assert\Type("string")
     * @Assert\Length(
     *      min = 6,
     *      minMessage = "Le mot de passe doit être au minimum de  {{ limit }} charactères.",
     * )
     */
    private $nouveauPassword;

    /**
     * @Assert\EqualTo(propertyPath="nouveauPassword", message="Le mot de passe ne correspond pas." )
     */
    private $confirmerPassword;

    public function getAncienPassword(): ?string
    {
        return $this->ancienPassword;
    }

    public function setAncienPassword(string $ancienPassword): self
    {
        $this->ancienPassword = $ancienPassword;

        return $this;
    }

    public function getNouveauPassword(): ?string
    {
        return $this->nouveauPassword;
    }

    public function setNouveauPassword(string $nouveauPassword): self
    {
        $this->nouveauPassword = $nouveauPassword;

        return $this;
    }

    public function getConfirmerPassword(): ?string
    {
        return $this->confirmerPassword;
    }

    public function setConfirmerPassword(string $confirmerPassword): self
    {
        $this->confirmerPassword = $confirmerPassword;

        return $this;
    }
}