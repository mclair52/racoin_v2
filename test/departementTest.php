<?php

namespace tests;
use PHPUnit\Framework\TestCase;
use db\connection;
use controller\getDepartment;

class getdepartementTest extends TestCase
{
    private $departement;
    protected function setUp(): void
    {
        connection::createConn();
        $this->departement = new getDepartment();
    }

    public function testGetAllDepartements()
    {
        $departements = departement::getAllDepartements();
        $this->assertIsArray($departements);
        $this->assertNotEmpty($departements);

        $this->assertArrayHasKey('id_departement', $departements[0]);
        $this->assertsame($departements[0]['id'], 1);
        $this->assertArrayHasKey('nom_departement', $departements[0]);
        $this->assertSame($departements[0]['name'], 'Ain');
        $this->assertArrayHasKey('id_region', $departements[0]);
        $this->assertSame($departements[0]['id_region'], 1);
    }
}
