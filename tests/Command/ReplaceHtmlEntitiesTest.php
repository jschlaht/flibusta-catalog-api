<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ReplaceHtmlEntitiesTest extends KernelTestCase
{
    public function testExecute(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);
        $catalogFile = __DIR__ . '/../fixtures/catalog-html-entities.txt';

        $command = $application->find('app:import-catalog');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'catalogfile' => $catalogFile,
        ]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Autor last name: Arthaud', $output);
        $this->assertStringContainsString('Autor first name: Florence', $output);
        $this->assertStringContainsString('Autor middle name:', $output);

        $this->assertStringContainsString('Book title: Cette nuit, la mer est noire', $output);
        $this->assertStringContainsString('Book subtitle:', $output);
        $this->assertStringContainsString('Book language: fr', $output);
        $this->assertStringContainsString('Book year: 2015', $output);
        $this->assertStringContainsString('Book serie: La Traversée des Mondes 0', $output);
        $this->assertStringContainsString('Book flibusta id: 510493', $output);
    }
}
