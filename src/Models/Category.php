<?php

declare(strict_types=1);

namespace Api\Models;

use Api\Orm\Table;
use Api\Orm\Column;
use Api\Orm\Entity;
use Api\Orm\PrimaryKey;

#[Table(name: 'category')]
class Category extends Entity
{
    #[PrimaryKey]
    #[Column]
    public int $id;
    #[Column]
    public string $name;
}
