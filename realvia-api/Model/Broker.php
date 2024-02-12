<?php

namespace RealviaApi\Model;

use RealviaApi\Attribute\Column;
use RealviaApi\Attribute\Table;

#[Table(name: "dm_realvia_broker")]
class Broker extends Base {
    #[Column(type: "int", field: "broker_id", primary: true)]
    private ?int $id;

    #[Column(type: "varchar", length: 255, field: "name")]
    private ?string $name;

    #[Column(type: "varchar", length: 255, field: "phone")]
    private ?string $phone;

    #[Column(type: "varchar", length: 255, field: "emailAddress")]
    private ?string $email;

    #[Column(type: "varchar", length: 255, field: "position")]
    private ?string $position;

    #[Column(type: "varchar", length: 255, field: "text.sk")]
    private ?string $text;

    #[Column(type: "tinyint", field: "isHidden")]
    private ?bool $isHidden;

    #[Column(type: "varchar", length: 255)]
    private ?string $nick = null;

    #[Column(type: "varchar", length: 255)]
    private ?bool $photo = null;

    #[Column(type: "int")]
    private ?int $branch = null;

    #[Column(type: "text")]
    private ?string $rawData;

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

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getIsHidden(): ?bool
    {
        return $this->isHidden;
    }

    public function setIsHidden(bool $isHidden): self
    {
        $this->isHidden = $isHidden;

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

    public function getNick(): ?string
    {
        return $this->nick;
    }

    public function setNick(?string $nick): self
    {
        $this->nick = $nick;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getBranch(): ?int
    {
        return $this->branch;
    }

    public function setBranch(?int $branch): self
    {
        $this->branch = $branch;

        return $this;
    }
}