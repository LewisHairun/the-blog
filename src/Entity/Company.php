<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 70)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: User::class)]
    private Collection $userCompany;

    public function __construct()
    {
        $this->userCompany = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, User>
     */
    public function getUserCompany(): Collection
    {
        return $this->userCompany;
    }

    public function addUserCompany(User $userCompany): static
    {
        if (!$this->userCompany->contains($userCompany)) {
            $this->userCompany->add($userCompany);
            $userCompany->setCompany($this);
        }

        return $this;
    }

    public function removeUserCompany(User $userCompany): static
    {
        if ($this->userCompany->removeElement($userCompany)) {
            // set the owning side to null (unless already changed)
            if ($userCompany->getCompany() === $this) {
                $userCompany->setCompany(null);
            }
        }

        return $this;
    }
}
