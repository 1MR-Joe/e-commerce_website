<?php
declare(strict_types=1);

namespace App\Entities;

use App\Entities\Traits\HasTimestamps;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'comments')]
#[HasLifecycleCallbacks]
class Comment
{
    use HasTimestamps;
    #[Id, Column, GeneratedValue]
    private int $id;

    #[ManyToOne, JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Customer $customer;
    // the customer will be nullable in case the customer deleted there account
    // this is an option that can be changed according to the application policies

    #[ManyToOne, JoinColumn(onDelete: 'CASCADE')]
    private Product $product;
    #[Column]
    private \DateTime $timestamp;
    #[Column(type: Types::STRING, length: 1000)]
    private string $content;
    #[Column(type: Types::DECIMAL, precision: 2, scale: 1)]
    private float $rating;

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getTimestamp(): \DateTime
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTime $timestamp): Comment
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): Comment
    {
        $this->content = $content;
        return $this;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function setRating(float $rating): Comment
    {
        $this->rating = $rating;
        return $this;
    }

}