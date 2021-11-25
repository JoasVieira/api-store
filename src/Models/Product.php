<?php

declare(strict_types=1);

namespace Api\Models;

use Api\Orm\Table;
use Api\Orm\Column;
use Api\Orm\Entity;
use Api\Orm\PrimaryKey;

#[Table(name: 'product')]
class Product extends Entity
{
    #[PrimaryKey]
    #[Column]
    public int $id;
    #[Column]
    public string $name;
    #[Column]
    public string $photo;
    #[Column]
    public string $description;
    #[Column]
    public float $price;

    #[Column]
    public int $category_id;
    #[Column]
    public int $company_id;
}
