<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class AssignRoleCommand extends Command
{
    protected static $defaultName = 'app:assign-role';

    private $entityManager;

    private $availableRoles = [
        'ROLE_USER',
        'ROLE_ADMIN',
        'ROLE_SUPER_ADMIN',
        'ROLE_PRODUCT_ADMIN',
        'ROLE_CUSTOMER_ADMIN',
        'ROLE_ORDER_ADMIN',
        'ROLE_CONTENT_ADMIN',
    ];

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Assigns a role to a user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Assign Role to User');

        $helper = $this->getHelper('question');

        // Get all users
        $users = $this->entityManager->getRepository(User::class)->findAll();
        if (empty($users)) {
            $io->error('No users found. Create a user first.');
            return Command::FAILURE;
        }

        // Format users for selection
        $userChoices = [];
        foreach ($users as $user) {
            $userChoices[$user->getId()] = sprintf(
                '%s (%s) - %s',
                $user->getFullName(),
                $user->getEmail(),
                implode(', ', $user->getRoles())
            );
        }

        // Ask for user
        $userQuestion = new ChoiceQuestion(
            'Please select a user:',
            $userChoices
        );
        $userId = $helper->ask($input, $output, $userQuestion);

        // Get selected user
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            $io->error('User not found.');
            return Command::FAILURE;
        }

        // Ask for role
        $roleQuestion = new ChoiceQuestion(
            'Please select a role to assign:',
            $this->availableRoles
        );
        $role = $helper->ask($input, $output, $roleQuestion);

        // Update user roles
        $currentRoles = $user->getRoles();
        if (!in_array($role, $currentRoles)) {
            $currentRoles[] = $role;
            $user->setRoles($currentRoles);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $io->success(sprintf(
                'Role "%s" assigned to user "%s" successfully.',
                $role,
                $user->getEmail()
            ));
        } else {
            $io->warning(sprintf(
                'User "%s" already has the role "%s".',
                $user->getEmail(),
                $role
            ));
        }

        return Command::SUCCESS;
    }
}