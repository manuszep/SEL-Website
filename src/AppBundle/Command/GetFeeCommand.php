<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use SelExchangeBundle\Entity\Exchange;

class GetFeeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:get-fee')

            // the short description shown while running "php bin/console list"
            ->setDescription('Takes 0.25 units from every user account and send it to admin user account')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command will loop all users and take 0.25 units from their account. It will then add-up everything and send it to the admin account. This should be executed each month.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        $userManager = $this->getContainer()->get('fos_user.user_manager');

        $users = $userManager->findUsers();

        $admin = null;

        foreach ($users as $user) {
            /* @var $user \AppBundle\Entity\User */
            if ($user->hasRole('ROLE_SUPER_ADMIN')) {
                $admin = $user;
                continue;
            }
        }

        foreach ($users as $user) {
            /* @var $user \AppBundle\Entity\User */
            if ($user->hasRole('ROLE_SUPER_ADMIN')) {
                continue;
            }

            $exchange = new Exchange();

            $exchange->setDebitUser($user);
            $exchange->setCreditUser($admin);
            $exchange->setAmount(0.25);
            $exchange->setTitle('Prélèvement mensuel');
            $exchange->setHidden(true);
            $entityManager->persist($exchange);
        }

        $entityManager->flush();
    }
}
