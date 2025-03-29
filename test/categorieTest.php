<?php

use PHPUnit\Framework\TestCase;
use db\connection;
use controller\getCategory;

class CategorieTest extends TestCase
{
    private $categorie;

    protected function setUp(): void
    {
        connection::createConn();
        $this->categorie = new getCategory();
    }

    public function testGetAllCategories()
    {
        $categories = $this->categorie->getAllCategories();
        $this->assertIsArray($categories);
        $this->assertNotEmpty($categories);

        $this->assertArrayHasKey('id_categorie', $categories[0]);
        $this->assertSame($categories[0]['id_categorie'], 1);
        $this->assertArrayHasKey('nom_categorie', $categories[0]);
        $this->assertSame($categories[0]['nom_categorie'], 'Informatique');
    }
}