<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class SubtitleAdditionalDelimiterTest extends KernelTestCase
{
    public function testExecute(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);
        $catalogFile = __DIR__ . '/../fixtures/catalog-subtitle-with-additional-delimiter.txt';

        $command = $application->find('app:import-catalog');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'catalogfile' => $catalogFile,
        ]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Autor last name: Arthur', $output);
        $this->assertStringContainsString('Autor first name: A.', $output);
        $this->assertStringContainsString('Autor middle name: C.', $output);

        $this->assertStringContainsString('Book title: Part of Me', $output);
        $this->assertStringContainsString('Book subtitle:', $output);
        $this->assertStringContainsString('Book language: en', $output);
        $this->assertStringContainsString('Book year: 2014', $output);
        $this->assertStringContainsString('Book serie: Shadow Shifters Damaged Hearts 2', $output);
        $this->assertStringContainsString('Book flibusta id: 370442', $output);
    }
}
