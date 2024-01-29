<?php

namespace RealviaApi\Model;

use RealviaApi\Attribute\Column;
use RealviaApi\Attribute\Table;

#[Table(name: "dm_realvia_realestate")]
class Realestate extends Base {
    #[Column(type: "int", field: "exportId", primary: true)]
    private ?int $id;

    #[Column(type: "varchar", length: 255, field: "name.sk")]
    private ?string $name;

    #[Column(type: "text", field: "text.sk")]
    private ?string $text;

    #[Column(type: "varchar", length: 255, field: "Základné údaje.Status")]
    private ?string $status;

    #[Column(type: "varchar", length: 255, field: "Základné údaje.Typ")]
    private ?string $type;

    #[Column(type: "varchar", length: 255, field: "Základné údaje.Druh")]
    private ?string $kind;

    #[Column(type: "float", field: "Základné údaje.Cena")]
    private ?float $price;

    #[Column(type: "varchar", length: 10, field: "Základné údaje.Typ ceny")]
    private ?string $currency;

    #[Column(type: "varchar", length: 10, field: "Základné údaje.Vlastníctvo")]
    private ?string $ownership;

    #[Column(type: "tinyint", field: "Základné údaje.Exkluzívne")]
    private ?bool $exclusive;

    #[Column(type: "json")]
    private ?array $images;

    #[Column(type: "relation", relationClass: Broker::class, relationField: "id")]
    private ?int $broker;

    #[Column(type: "text")]
    private ?string $rawData;

    public function getFormattedPrice(): ?string
    {
        return number_format($this->price, 2, ",", " ") . " " . $this->currency;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText($text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getKind(): ?string
    {
        return $this->kind;
    } 

    public function setKind(string $kind): self
    {
        $this->kind = $kind;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getOwnership(): ?string
    {
        return $this->ownership;
    }

    public function setOwnership(string $ownership): self
    {
        $this->ownership = $ownership;

        return $this;
    }

    public function getExclusive(): bool
    {
        return $this->exclusive;
    }

    public function setExclusive(bool $exclusive): self
    {
        $this->exclusive = $exclusive;

        return $this;
    }

    public function getRawData(): ?string
    {
        return $this->rawData;
    }

    public function setRawData(string $rawData): self
    {
        $this->rawData = $rawData;

        return $this;
    }

    public function getBroker(): ?int
    {
        return $this->broker;
    }

    public function setBroker(int|Broker $broker): self
    {
        $this->broker = $broker instanceof Broker ? $broker->getId() : $broker;

        return $this;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }
}