<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class EmptyLanguageTest extends KernelTestCase
{
    public function testExecute(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);
        $catalogFile = __DIR__ . '/../fixtures/catalog-empty-language.txt';

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
        $this->assertStringContainsString('Book data: Beginning Java MVC 1.0;;;2021;', $output);
        $this->assertStringContainsString('Book language:', $output);
        $this->assertStringContainsString('Book year: 2021', $output);
        $this->assertStringContainsString('Book flibusta id: 611430', $output);
    }
}
