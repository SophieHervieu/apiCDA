<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get; 
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Put;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/product/{id}', 
            requirements: ['id' => '\d+'],
            ),
        new GetCollection(
            uriTemplate: '/products',
            ),
        new Post(
            uriTemplate: '/product',
            status: 201
        ),
        new Delete(
            uriTemplate: '/product/{id}',
            requirements: ['id' => '\d+'],
            status: 204
        ),
        new Put(
            uriTemplate: '/product/{id}',
            requirements: ['id' => '\d+'],
            status: 201
        ),
    ],
    order: ['id' => 'ASC', 'name' => 'ASC'],
    paginationEnabled: true
)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

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
}
