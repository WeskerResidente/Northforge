<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Index(name: 'IDX_INVENTORY_MOVEMENT_ITEM_MOVEMENT', columns: ['movement_id'])]
class InventoryMovementItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: InventoryMovement::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?InventoryMovement $movement = null;

    #[ORM\Column(length: 20)]
    private ?string $side = null;

    #[ORM\Column(length: 40)]
    private ?string $reference = null;

    #[ORM\Column(length: 180)]
    private ?string $name = null;

    #[ORM\Column]
    private int $quantity = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $itemValue = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMovement(): ?InventoryMovement
    {
        return $this->movement;
    }

    public function setMovement(InventoryMovement $movement): static
    {
        $this->movement = $movement;

        return $this;
    }

    public function getSide(): ?string
    {
        return $this->side;
    }

    public function setSide(string $side): static
    {
        $this->side = $side;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getItemValue(): ?string
    {
        return $this->itemValue;
    }

    public function setItemValue(string $itemValue): static
    {
        $this->itemValue = $itemValue;

        return $this;
    }
}
