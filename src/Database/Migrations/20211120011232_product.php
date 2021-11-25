<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Product extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('product');
        $table->addColumn('name', 'string', ['limit' => 100])
              ->addColumn('photo', 'string', ['limit' => 45])
              ->addColumn('description', 'text')
              ->addColumn('price', 'double')
              ->addColumn('category_id', 'integer')
              ->addColumn('company_id', 'integer')
              ->addForeignKey('category_id', 'category', ['id'])
              ->addForeignKey('company_id', 'company', ['id'])
              ->create();
    }
}
