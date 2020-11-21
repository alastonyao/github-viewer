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

class ImportRepositoryCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:import-repo';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('app:import-repo')
            ->setDescription('clone Github Repository')
            ->addArgument('repository', InputArgument::REQUIRED, 'The url Github repository.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Importation Github Repository',
            '============',
            '',
        ]);
        $em = $this->entityManager;

        $output->writeln('Repository: ' . $input->getArgument('repository'));
        $url = $input->getArgument('repository');

        if ($url !== '') {
            $myDate = new DateTime();
            $folder = "C:\\wamp64\\www\\gitrepoviewer-alas\\temp\\gitClone" .  $myDate->format("u");
            $repository = $em->getRepository("App:Repositories");

            $result = GitRepository::cloneRepository($url, $folder);
            
            $repo = $repository->findOneBy(['url' => $url]);
            if ($repo == null) {
                $repo = new Repositories();
                $repo->setName(basename($url, ".git"))
                    ->setPath($folder)
                    ->seturl($url);

                $em->persist($repo);
            }

            $commitManager = $em->getRepository("App:Commits");

            $commits = $commitManager->findall();
            foreach ($commits as $commit) {
                if ($commit->getUrlRepo() == $url) {
                    $commit->setIsDelete(false);
                    $em->persist($commit);
                }
            }
            $em->flush();

            if ($result !== '') {
                $output->writeln('Repositories successful created');
            } else {
                $output->writeln('Something wrong on the repository creation ');
            }

            return Command::SUCCESS;
        }
        $output->writeln('Something wrong on the repository creation ');
        return 0;
    }
}
