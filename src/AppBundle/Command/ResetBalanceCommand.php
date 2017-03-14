<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use SelExchangeBundle\Entity\Exchange;

class ResetBalanceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:reset-balance')

            // the short description shown while running "php bin/console list"
            ->setDescription('Resets all user\'s balance to 50, except for Super Admin.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command will loop all users and set balance to 50. Except for Super Admin.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Run only if it's the first day of the year (0 = first day, 365 = last day of leap year)
        if ( date('z') !== '0' ) {return;}
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        $userManager = $this->getContainer()->get('fos_user.user_manager');

        $users = $userManager->findUsers();

        $admin = null;

        foreach ($users as $user) {
            /* @var $user \AppBundle\Entity\User */
            if ($user->hasRole('ROLE_SUPER_ADMIN')) {
                continue;
            }

            $user->setBalance(50);
            $entityManager->persist($user);
        }

        $entityManager->flush();
    }
}
