<?php

namespace App\Command;

use App\Entity\Importer\Autor;
use App\Entity\Importer\Book;
use App\Repository\AutorRepository;
use App\Repository\BookRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ZipArchive;

#[AsCommand(
    name: 'app:import-catalog',
    description: 'import current flibusta catalog',
)]
class ImportCatalogCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AutorRepository $autorRepository,
        private BookRepository $bookRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('catalogfile', InputArgument::OPTIONAL, 'path to catalog file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $catalogFile = $input->getArgument('catalogfile');
        if (is_null($catalogFile)) {
            $catalogFolder = '/tmp/flibusta-catalog/';
            $catalogFile = $catalogFolder . 'catalog.txt';
            $catalogZipFile = '/tmp/catalog.zip';
            $zipCatalog = file_get_contents('https://flibusta.is/catalog/catalog.zip');
            file_put_contents($catalogZipFile, $zipCatalog);
            $zip = new ZipArchive;
            if ($zip->open($catalogZipFile) === TRUE) {
                $zip->extractTo($catalogFolder);
                $zip->close();
                echo 'ok';
            } else {
                echo 'failed';
            }
        }

        if ($catalogFile) {
            $io->note(sprintf('You passed an argument: %s', $catalogFile));
            if (file_exists($catalogFile)) {
                $catalogData = file($catalogFile, FILE_IGNORE_NEW_LINES);
                $numberAllDataLines = count($catalogData);
                $io->note(sprintf('Number of lines: %s', $numberAllDataLines));
                foreach ($catalogData as $key => $line) {
                    if ($key === 0) {
                        continue;
                    }
                    $io->note(sprintf('First line: %s', $line));
                    $data = $this->parseData($line);

                    $io->note(sprintf('Autor last name: %s', $data['autorLastName']));
                    $io->note(sprintf('Autor first name: %s', $data['autorFirstName']));
                    $io->note(sprintf('Autor middle name: %s', $data['autorMiddleName']));
                    $io->note(sprintf('Book data: %s', $data['bookData']));
                    $io->note(sprintf('Book language: %s', $data['bookLanguage']));
                    $io->note(sprintf('Book year: %s', $data['bookYear']));
                    $io->note(sprintf('Book flibusta id: %s', $data['bookFlibustaId']));

                    $this->importData($data, $output);
                    if ($key % 8 === 0) {
                        $this->entityManager->getUnitOfWork()->clear();
                    }
                    unset($data);
                    $io->note(sprintf('Imported %s from %s', $key, $numberAllDataLines));
                }
                unlink($catalogFile);
                rmdir($catalogFolder);
                unlink($catalogZipFile);
            } else {
                $io->note(sprintf('You need a catalogfile for import'));
                return Command::INVALID;
            }
        } else {
            $io->note(sprintf('You need a catalogfile for import'));
            return Command::INVALID;
        }

        $io->success('Catalog import command was successful!');

        return Command::SUCCESS;
    }

    private function parseData(string $line): array
    {
        $line = html_entity_decode($line);
        //$line = str_replace("litres;", "", $line);
        $parts = explode(";", $line);
        $data = [];

        $data['autorLastName'] = array_shift($parts);
        $data['autorFirstName'] = array_shift($parts);
        $data['autorMiddleName'] = array_shift($parts);
        $data['bookFlibustaId'] = array_pop($parts);
        $data['bookData'] = implode(';', $parts);
        $data['bookLanguage'] = null;
        $data['bookYear']  = null;

        $matches_language = [];
        $matches_year = [];
        preg_match("/;([a-zA-Z]{2,3});/", $data['bookData'], $matches_language);
        if (array_key_exists(1, $matches_language)) {
            $data['bookLanguage'] = strtolower($matches_language[1]);
        }
        preg_match("/;([1-2][0-9]{3,4});/", $data['bookData'], $matches_year);
        if (array_key_exists(1, $matches_year)) {
            if (strlen($matches_year[1]) > 4) {
                $data['bookYear'] = substr($matches_year[1], 0, 4);
            } else {
                $data['bookYear'] = $matches_year[1];
            }
        }

        return $data;
    }
    private function importData(array $data, OutputInterface $output): void
    {
        $book = $this->bookRepository->findOneBy(['bookFlibustaId' => $data['bookFlibustaId']]);
        if (is_null($book)) {
            $book = new Book();
        }
        $book->setBookData($data['bookData']);
        $book->setBookLanguage($data['bookLanguage']);
        if ((!is_null($data['bookYear'])) && (strlen($data['bookYear']) !== 0)) {
            $year = DateTime::createFromFormat('Y', $data['bookYear']);
            $book->setBookYear($year);
        }
        $book->setBookFlibustaId($data['bookFlibustaId']);

        $this->entityManager->persist($book);
        $output->writeln(sprintf('Book %s imported with id %s', $book->getBookData(), $book->getId()));

        if (strlen($data['autorLastName']) !== 0) {
            $autor = $this->autorRepository->findOneBy(['autorLastName' => $data['autorLastName'], 'autorFirstName' => $data['autorFirstName'], 'autorMiddleName' => $data['autorMiddleName']]);
            if (is_null($autor)) {
                $autor = new Autor();
                $autor->setAutorLastName($data['autorLastName']);
                $autor->setAutorFirstName($data['autorFirstName']);
                $autor->setAutorMiddleName($data['autorMiddleName']);
            }
            $autor->addAutorBook($book);
            $this->entityManager->persist($autor);
            $output->writeln(sprintf('Autor %s imported with id %s', $autor->getAutorLastName(), $autor->getId()));
        }

        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
