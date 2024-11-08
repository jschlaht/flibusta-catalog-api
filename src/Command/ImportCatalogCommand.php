<?php

namespace App\Command;

use App\Entity\Autor;
use App\Entity\Book;
use App\Entity\Serie;
use App\Repository\AutorRepository;
use App\Repository\BookRepository;
use App\Repository\SerieRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-catalog',
    description: 'import current flibusta catalog',
)]
class ImportCatalogCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AutorRepository $autorRepository,
        private BookRepository $bookRepository,
        private SerieRepository $serieRepository
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
        $line = str_replace("litres;", "", $line);
        $parts = explode(";", $line);
        $data = [];

        $data['autorLastName'] = array_shift($parts);
        $data['autorFirstName'] = array_shift($parts);
        $data['autorMiddleName'] = array_shift($parts);
        $data['bookFlibustaId'] = array_pop($parts);
        $data['bookData'] = implode(';', $parts);

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
        if (strlen($data['bookYear']) !== 0) {
            $year = DateTime::createFromFormat('Y', $data['bookYear']);
            $book->setBookYear($year);
        }
        $book->setBookFlibustaId($data['bookFlibustaId']);

        $this->entityManager->persist($book);
        $output->writeln(sprintf('Book %s imported with id %s', $book->getBookTitle(), $book->getId()));

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
