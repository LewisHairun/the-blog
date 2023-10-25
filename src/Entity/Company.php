<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use App\Traits\TimeStampTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Company
{
    use TimeStampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 70)]
    private ?string $name = null;

    #[ORM\OneToOne(mappedBy: 'company', cascade: ['persist', 'remove'])]
    private ?User $userCompany = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getUserCompany(): ?User
    {
        return $this->userCompany;
    }

    public function setUserCompany(User $userCompany): static
    {
        // set the owning side of the relation if necessary
        if ($userCompany->getCompany() !== $this) {
            $userCompany->setCompany($this);
        }

        $this->userCompany = $userCompany;

        return $this;
    }
}
