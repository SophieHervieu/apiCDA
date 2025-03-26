<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\Account;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'create-user',
    description: 'Commande pour ajouter un utilisateur en BDD',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $hasher
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // configure an argument
            ->addArgument('firstname', InputArgument::REQUIRED, 'Prénon de l\'utilisateur')
            ->addArgument('lastname', InputArgument::REQUIRED, 'Nom de l\'utilisateur')
            ->addArgument('email', InputArgument::REQUIRED, 'Email de l\'utilisateur')
            ->addArgument('password', InputArgument::REQUIRED, 'Email de l\'utilisateur')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('firstname');
        $arg2 = $input->getArgument('lastname');
        $arg3 = $input->getArgument('email');
        $arg4 = $input->getArgument('password');

        try {
            if ($this->accountRepository->findOneBy(["email"=>$arg3])) {
                $io->error('Le compte existe déjà');
                return Command::INVALID;
            }

            $account = new Account();
            $account->setFirstname($arg1)->setLastname($arg2)->setEmail($arg3)->setRoles(["ROLE_USER"]);
            $hashedPassword = $this->hasher->hashPassword($account, $arg4);
            $account->setPassword($hashedPassword);
            $this->em->persist($account);
            $this->em->flush();
            $io->success('Le compte a été ajouté avec succès');
            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}
