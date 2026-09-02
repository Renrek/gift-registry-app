<?php declare(strict_types=1);

namespace App\Command;

use App\Service\InvitationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:user:invite', description: 'Create a system invitation for an email, without an inviter or automatic friend connection')]
class CreateSystemInvitationCommand extends Command
{
    public function __construct(private readonly InvitationService $invitationService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'Email address to invite');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = (string) $input->getArgument('email');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $io->error(sprintf('"%s" is not a valid email address.', $email));

            return Command::FAILURE;
        }

        $invitation = $this->invitationService->createSystemInvitation($email);

        $io->success(sprintf(
            'Invitation created for %s. Register at /registration with this code: %s',
            $email,
            $invitation->getInvitationCode(),
        ));

        return Command::SUCCESS;
    }
}
