<?php

namespace App\Entity;

use App\Repository\InterventionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterventionRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_INTERVENTION_PROPERTY_DATE', fields: ['property', 'businessDate'])]
#[ORM\Index(name: 'IDX_INTERVENTION_PROPERTY', columns: ['property_id'])]
#[ORM\Index(name: 'IDX_INTERVENTION_CREATOR', columns: ['created_by_id'])]
#[ORM\Index(name: 'IDX_INTERVENTION_BUSINESS_DATE', columns: ['business_date'])]
class Intervention
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Property $property;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Worker $createdBy;

    #[ORM\Column(type: 'date_immutable')]
    private \DateTimeImmutable $businessDate;

    // Traveler exit (not used for conformity status)
    #[ORM\Column(nullable: true)]
    private ?bool $exitOnTime = null;

    #[ORM\Column(nullable: true)]
    private ?bool $instructionsRespected = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $exitComment = null;

    // Cleaning checks (drive conformity status)
    #[ORM\Column(options: ['default' => false])]
    private bool $checkBedMade = false;

    #[ORM\Column(options: ['default' => false])]
    private bool $checkFloorClean = false;

    #[ORM\Column(options: ['default' => false])]
    private bool $checkBathroomOk = false;

    #[ORM\Column(options: ['default' => false])]
    private bool $checkKitchenOk = false;

    #[ORM\Column(options: ['default' => false])]
    private bool $checkLinenChanged = false;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $cleaningComment = null;

    /**
     * @var Collection<int, InterventionPhoto>
     */
    #[ORM\OneToMany(mappedBy: 'intervention', targetEntity: InterventionPhoto::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $photos;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->photos = new ArrayCollection();
        // businessDate will be set by the app using Europe/Paris (e.g. today)
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProperty(): Property
    {
        return $this->property;
    }

    public function setProperty(Property $property): static
    {
        $this->property = $property;

        return $this;
    }

    public function getCreatedBy(): Worker
    {
        return $this->createdBy;
    }

    public function setCreatedBy(Worker $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getBusinessDate(): \DateTimeImmutable
    {
        return $this->businessDate;
    }

    public function setBusinessDate(\DateTimeImmutable $businessDate): static
    {
        $this->businessDate = $businessDate;

        return $this;
    }

    public function getExitOnTime(): ?bool
    {
        return $this->exitOnTime;
    }

    public function setExitOnTime(?bool $exitOnTime): static
    {
        $this->exitOnTime = $exitOnTime;

        return $this;
    }

    public function getInstructionsRespected(): ?bool
    {
        return $this->instructionsRespected;
    }

    public function setInstructionsRespected(?bool $instructionsRespected): static
    {
        $this->instructionsRespected = $instructionsRespected;

        return $this;
    }

    public function getExitComment(): ?string
    {
        return $this->exitComment;
    }

    public function setExitComment(?string $exitComment): static
    {
        $this->exitComment = $exitComment;

        return $this;
    }

    public function isCheckBedMade(): bool
    {
        return $this->checkBedMade;
    }

    public function setCheckBedMade(bool $checkBedMade): static
    {
        $this->checkBedMade = $checkBedMade;

        return $this;
    }

    public function isCheckFloorClean(): bool
    {
        return $this->checkFloorClean;
    }

    public function setCheckFloorClean(bool $checkFloorClean): static
    {
        $this->checkFloorClean = $checkFloorClean;

        return $this;
    }

    public function isCheckBathroomOk(): bool
    {
        return $this->checkBathroomOk;
    }

    public function setCheckBathroomOk(bool $checkBathroomOk): static
    {
        $this->checkBathroomOk = $checkBathroomOk;

        return $this;
    }

    public function isCheckKitchenOk(): bool
    {
        return $this->checkKitchenOk;
    }

    public function setCheckKitchenOk(bool $checkKitchenOk): static
    {
        $this->checkKitchenOk = $checkKitchenOk;

        return $this;
    }

    public function isCheckLinenChanged(): bool
    {
        return $this->checkLinenChanged;
    }

    public function setCheckLinenChanged(bool $checkLinenChanged): static
    {
        $this->checkLinenChanged = $checkLinenChanged;

        return $this;
    }

    public function getCleaningComment(): ?string
    {
        return $this->cleaningComment;
    }

    public function setCleaningComment(?string $cleaningComment): static
    {
        $this->cleaningComment = $cleaningComment;

        return $this;
    }

    /**
     * @return Collection<int, InterventionPhoto>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(InterventionPhoto $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setIntervention($this);
        }

        return $this;
    }

    public function removePhoto(InterventionPhoto $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            // orphanRemoval=true => suppression automatique
        }

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isConform(): bool
    {
        return $this->checkBedMade
            && $this->checkFloorClean
            && $this->checkBathroomOk
            && $this->checkKitchenOk
            && $this->checkLinenChanged;
    }
}
