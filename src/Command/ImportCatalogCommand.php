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
                    $io->note(sprintf('Book title: %s', $data['bookTitle']));
                    $io->note(sprintf('Book subtitle: %s', $data['bookSubTitle']));
                    $io->note(sprintf('Book language: %s', $data['bookLanguage']));
                    $io->note(sprintf('Book year: %s', $data['bookYear']));
                    $io->note(sprintf('Book serie: %s', $data['bookSerie']));
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

        $data['autorLastName'] = $parts[0];
        $data['autorFirstName'] = $parts[1];
        $data['autorMiddleName'] = $parts[2];

        switch (count($parts)) {
            case 10:
                if ((strlen($parts[6]) == 2) || (strlen($parts[7]) == 4)) {
                    $data['bookTitle'] = trim($parts[3]) . ' ' . trim($parts[4]);
                    $data['bookSubTitle'] = trim($parts[5]);
                    $data['bookLanguage'] = $parts[6];
                    $data['bookYear'] = $parts[7];
                    $data['bookSerie'] = $parts[8];
                } else if ((strlen($parts[5]) == 2) || (strlen($parts[6]) == 4)) {
                    $data['bookTitle'] = trim($parts[3]);
                    $data['bookSubTitle'] = trim($parts[4]);
                    $data['bookLanguage'] = $parts[5];
                    $data['bookYear'] = $parts[6];
                    $data['bookSerie'] = trim($parts[7]) . ' ' . trim($parts[8]);
                }
                break;
            case 11:
                if ((strlen($parts[7]) == 2) || (strlen($parts[8]) == 4)) {
                    $data['bookTitle'] = trim($parts[3]) . ' ' . trim($parts[4]) . ' ' . trim($parts[5]);
                    $data['bookSubTitle'] = trim($parts[6]);
                    $data['bookLanguage'] = $parts[7];
                    $data['bookYear'] = $parts[8];
                    $data['bookSerie'] = array_slice($parts, -2, 1)[0];
                } else if ((strlen($parts[5]) == 2) || (strlen($parts[6]) == 4)) {
                    $data['bookTitle'] = trim($parts[3]);
                    $data['bookSubTitle'] = trim($parts[4]);
                    $data['bookLanguage'] = $parts[5];
                    $data['bookYear'] = $parts[6];
                    $data['bookSerie'] = trim($parts[7]) . ' ' . trim($parts[8]) . ' ' . trim($parts[9]);
                }
                break;
            default:
                $data['bookTitle'] = trim($parts[3]);
                $data['bookSubTitle'] = trim(array_slice($parts, -5, 1)[0]);
                $data['bookLanguage'] = array_slice($parts, -4, 1)[0];
                $data['bookYear'] = array_slice($parts, -3, 1)[0];
                $data['bookSerie'] = trim(array_slice($parts, -2, 1)[0]);

                break;
        }

        if (strlen($data['bookYear']) > 4) {
            $data['bookYear'] = substr($data['bookYear'], 0, 4);
        }
        $data['bookFlibustaId'] = array_slice($parts, -1, 1)[0];

        return $data;
    }
    private function importData(array $data, OutputInterface $output): void
    {
        $book = $this->bookRepository->findOneBy(['bookFlibustaId' => $data['bookFlibustaId']]);
        if (is_null($book)) {
            $book = new Book();
        }
        $book->setBookTitle($data['bookTitle']);
        $book->setBookSubtitle($data['bookSubTitle']);
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

        if (strlen($data['bookSerie']) !== 0) {
            $serie = $this->serieRepository->findOneBy(['serieName' => $data['bookSerie']]);
            if (is_null($serie)) {
                $serie = new Serie();
                $serie->setSerieName($data['bookSerie']);
            }
            $serie->addSerieBook($book);
            $this->entityManager->persist($serie);
            $output->writeln(sprintf('Serie %s imported with id %s', $serie->getSerieName(), $serie->getId()));
        }

        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
