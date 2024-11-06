<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class MissingYearValueTest extends KernelTestCase
{
    public function testExecute(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);
        $catalogFile = __DIR__ . '/../fixtures/catalog-line-without-year.txt';

        $command = $application->find('app:import-catalog');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'catalogfile' => $catalogFile,
        ]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Autor last name: (Асмус)', $output);
        $this->assertStringContainsString('Autor first name: Священник Михаил', $output);
        $this->assertStringContainsString('Autor middle name:', $output);

        $this->assertStringContainsString('Book title: «О ВСЕХ И ЗА ВСЯ» В АНАФОРЕ УТОЧНЕНИЕ СМЫСЛА', $output);
        $this->assertStringContainsString('Book subtitle:', $output);
        $this->assertStringContainsString('Book language: ru', $output);
        $this->assertStringContainsString('Book year:', $output);
        $this->assertStringContainsString('Book serie: Электронная библиотека студента Православного Гуманитарного Университета 0', $output);
        $this->assertStringContainsString('Book flibusta id: 426583', $output);
    }
}
