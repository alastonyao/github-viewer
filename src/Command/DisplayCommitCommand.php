<?php

// src/Command/ExampleCommand.php
namespace App\Command;

use App\Entity\Commits;
use App\Entity\Repositories;
use Cz\Git\GitRepository;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;

class DisplayCommitCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:display-commits';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('app:display-commits')
            ->setDescription('display commits Github Repository')
            ->addArgument('repository', InputArgument::REQUIRED, 'The url Github repository.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Display Commits Github Repository',
            '============',
            '',
        ]);
        $em = $this->entityManager;

        $output->writeln('Repository: ' . $input->getArgument('repository'));
        $url = $input->getArgument('repository');

        if ($url !== '') {
            $myDate = new DateTime();
            $folder = "C:\\wamp64\\www\\gitrepoviewer-alas\\temp\\gitClone" .  $myDate->format("u");

            $commitManager = $em->getRepository("App:Commits");

            $commits = $commitManager->findall();
            foreach ($commits as $commit) {
                if ($commit->getUrlRepo() == $url) {
                    $output->writeln(('==========================='));
                    $output->writeln('Commit ID : ' . $commit->getCommitId());
                    $output->writeln('Subject: ' . $commit->getSubject());
                    $output->writeln('Message: ' . trim($commit->getMessage()));
                    $output->writeln('Author: ' . $commit->getAuthor());
                    $output->writeln('Date: ' . $commit->getDate());
                    $output->writeln(('==========================='));
                }
            }
            $output->writeln('===========================');
            $output->writeln('===========================');
            $output->writeln('Commit successful Displayed');

            return Command::SUCCESS;
        }
        $output->writeln('Something wrong on display Commit');
        return 0;
    }
}
