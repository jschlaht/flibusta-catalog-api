<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ImportCatalogCommandTest extends KernelTestCase
{
    public function testExecute(): void
       {
           self::bootKernel();
           $application = new Application(self::$kernel);
           $catalogFile = __DIR__.'/../fixtures/catalog-normal.txt';

           $command = $application->find('app:import-catalog');
           $commandTester = new CommandTester($command);
           $commandTester->execute([
               'catalogfile' => $catalogFile,
           ]);

           $commandTester->assertCommandIsSuccessful();

           $output = $commandTester->getDisplay();
           $this->assertStringContainsString('Autor last name:', $output);
           $this->assertStringContainsString('Autor first name:', $output);
           $this->assertStringContainsString('Autor middle name:', $output);
           $this->assertStringContainsString('Autor last name: Дворянкин', $output);
           $this->assertStringContainsString('Autor first name: Александр', $output);
           $this->assertStringContainsString('Autor middle name: Владимирович', $output);

           $this->assertStringContainsString('Book title: Beginning Java MVC 1.0', $output);
           $this->assertStringContainsString('Book title: «Если», 2005 № 01', $output);

           $this->assertStringContainsString('Book subtitle:', $output);
           $this->assertStringContainsString('Book subtitle: 143', $output);

           $this->assertStringContainsString('Book language: en', $output);
           $this->assertStringContainsString('Book language: ru', $output);

           $this->assertStringContainsString('Book year: 2021', $output);
           $this->assertStringContainsString('Book year: 2005', $output);

           $this->assertStringContainsString('Book serie:', $output);
           $this->assertStringContainsString('Book serie: Если, 2005 1|Журнал «Если» 143', $output);

           $this->assertStringContainsString('Book flibusta id: 611430', $output);
           $this->assertStringContainsString('Book flibusta id: 309034', $output);
       }
}
