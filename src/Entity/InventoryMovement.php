<?php

namespace App\Entity;

use App\Repository\InventoryMovementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventoryMovementRepository::class)]
#[ORM\Index(name: 'IDX_INVENTORY_MOVEMENT_OWNER', columns: ['owner_id'])]
class InventoryMovement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $owner = null;

    #[ORM\Column(length: 40)]
    private ?string $reference = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $movementDate = null;

    #[ORM\Column(length: 80)]
    private ?string $quality = null;

    #[ORM\Column(length: 120)]
    private ?string $carrier = null;

    /**
     * @var list<string>
     */
    #[ORM\Column]
    private array $categories = [];

    #[ORM\Column]
    private int $quantity = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $movementValue = null;

    #[ORM\Column(length: 20)]
    private ?string $balanceDirection = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $balanceAmount = null;

    /**
     * @var Collection<int, InventoryMovementItem>
     */
    #[ORM\OneToMany(mappedBy: 'movement', targetEntity: InventoryMovementItem::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getMovementDate(): ?\DateTimeImmutable
    {
        return $this->movementDate;
    }

    public function setMovementDate(\DateTimeImmutable $movementDate): static
    {
        $this->movementDate = $movementDate;

        return $this;
    }

    public function getQuality(): ?string
    {
        return $this->quality;
    }

    public function setQuality(string $quality): static
    {
        $this->quality = $quality;

        return $this;
    }

    public function getCarrier(): ?string
    {
        return $this->carrier;
    }

    public function setCarrier(string $carrier): static
    {
        $this->carrier = $carrier;

        return $this;
    }

    /**
     * @return list<string>
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param list<string> $categories
     */
    public function setCategories(array $categories): static
    {
        $this->categories = array_values($categories);

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getMovementValue(): ?string
    {
        return $this->movementValue;
    }

    public function setMovementValue(string $movementValue): static
    {
        $this->movementValue = $movementValue;

        return $this;
    }

    public function getBalanceDirection(): ?string
    {
        return $this->balanceDirection;
    }

    public function setBalanceDirection(string $balanceDirection): static
    {
        $this->balanceDirection = $balanceDirection;

        return $this;
    }

    public function getBalanceAmount(): ?string
    {
        return $this->balanceAmount;
    }

    public function setBalanceAmount(string $balanceAmount): static
    {
        $this->balanceAmount = $balanceAmount;

        return $this;
    }

    /**
     * @return Collection<int, InventoryMovementItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * @return InventoryMovementItem[]
     */
    public function getOfferedItems(): array
    {
        return $this->items->filter(static fn (InventoryMovementItem $item): bool => $item->getSide() === 'offered')->toArray();
    }

    /**
     * @return InventoryMovementItem[]
     */
    public function getReceivedItems(): array
    {
        return $this->items->filter(static fn (InventoryMovementItem $item): bool => $item->getSide() === 'received')->toArray();
    }

    public function getOfferedTotal(): float
    {
        return $this->sumItems($this->getOfferedItems());
    }

    public function getReceivedTotal(): float
    {
        return $this->sumItems($this->getReceivedItems());
    }

    /**
     * @param InventoryMovementItem[] $items
     */
    private function sumItems(array $items): float
    {
        return array_reduce(
            $items,
            static fn (float $total, InventoryMovementItem $item): float => $total + (float) $item->getItemValue(),
            0.0,
        );
    }
}
