<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class RemoveLitresFromTitleTest extends KernelTestCase
{
    public function testExecute(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);
        $catalogFile = __DIR__ . '/../fixtures/catalog-remove-litres-from-title.txt';

        $command = $application->find('app:import-catalog');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'catalogfile' => $catalogFile,
        ]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Autor last name: Булычев', $output);
        $this->assertStringContainsString('Autor first name: Кир', $output);
        $this->assertStringContainsString('Autor middle name:', $output);

        $this->assertStringContainsString('Book title: Покушение на Тесея', $output);
        $this->assertStringContainsString('Book subtitle: = Похищение Тесея', $output);
        $this->assertStringContainsString('Book language: ru', $output);
        $this->assertStringContainsString('Book year: 2005', $output);
        $this->assertStringContainsString('Book serie: Галактическая полиция [= Цикл «Кора» ИнтерГпол Кора Орват] 3', $output);
        $this->assertStringContainsString('Book flibusta id: 108239', $output);
    }
}
