<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class InvalidYearValueTest extends KernelTestCase
{
    public function testExecute(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);
        $catalogFile = __DIR__ . '/../fixtures/catalog-invalid-year-value.txt';

        $command = $application->find('app:import-catalog');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'catalogfile' => $catalogFile,
        ]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Autor last name: (Брянчанинов)', $output);
        $this->assertStringContainsString('Autor first name: Святитель Игнатий', $output);
        $this->assertStringContainsString('Autor middle name:', $output);

        $this->assertStringContainsString('Book data: Слово о смерти. Слово о человеке;;ru;20111;Наследие русского святителя 0', $output);
        $this->assertStringContainsString('Book language: ru', $output);
        $this->assertStringContainsString('Book year: 2011', $output);
        $this->assertStringContainsString('Book flibusta id: 573701', $output);
    }
}
