<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';

    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure()
    {
        $this
            ->setName('app:create-user')
            ->setDescription('Creates a new user')
            ->addOption('admin', null, InputOption::VALUE_NONE, 'Create an admin user with ROLE_SUPER_ADMIN');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create User');

        $helper = $this->getHelper('question');

        // Ask for email
        $emailQuestion = new Question('Please enter the email: ');
        $emailQuestion->setValidator(function ($answer) {
            if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new \RuntimeException('Please enter a valid email address');
            }

            // Check if email already exists
            $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $answer]);
            if ($existingUser) {
                throw new \RuntimeException('A user with this email already exists');
            }

            return $answer;
        });
        $email = $helper->ask($input, $output, $emailQuestion);

        // Ask for first name
        $firstNameQuestion = new Question('Please enter the first name: ');
        $firstName = $helper->ask($input, $output, $firstNameQuestion);

        // Ask for last name
        $lastNameQuestion = new Question('Please enter the last name: ');
        $lastName = $helper->ask($input, $output, $lastNameQuestion);

        // Ask for password
        $passwordQuestion = new Question('Please enter the password: ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);
        $password = $helper->ask($input, $output, $passwordQuestion);

        // Create user
        $user = new User();
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        // Assign roles
        if ($input->getOption('admin')) {
            $user->setRoles(['ROLE_SUPER_ADMIN']);
            $io->note('Creating an admin user with ROLE_SUPER_ADMIN');
        } else {
            $user->setRoles(['ROLE_USER']);
            $io->note('Creating a regular user with ROLE_USER');
        }

        // Save user
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('User "%s" created successfully.', $email));

        return Command::SUCCESS;
    }
}