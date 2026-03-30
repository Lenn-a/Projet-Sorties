<?php

namespace App\Entity;

use App\Repository\OutingUserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OutingUserRepository::class)]
class OutingUser
{
    //pas de clés primaire id

    #[ORM\Id]
    #[ORM\Column(name: "outing_id")]
    private ?int $outingId = null;

    #[ORM\Id]
    #[ORM\Column(name: "user_id")]
    private ?int $userId = null;

    public function getOutingId(): ?int
    {
        return $this->outingId;
    }

    public function setOutingId(?int $outingId): static
    {
        $this->outingId = $outingId;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }
}
