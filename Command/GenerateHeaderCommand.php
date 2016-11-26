<?php

/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 01.11.16
 * Time: 10:52
 */
namespace Mittax\WsseBundle\Command;

use Doctrine\DBAL\Exception\InvalidArgumentException;
use Mittax\WsseBundle\DependencyInjection\MittaxWsseExtension;
use Mittax\WsseBundle\Security\Header\WsseHeader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\ContainerBuilder;


class GenerateHeaderCommand extends ContainerAwareCommand
{
    // ...
    protected function configure()
    {
        $this
            ->setName('mittax:wsse:generate-header')

            ->setDescription('Generates a wsse header from username and apikey')

            ->setHelp("This command allows you to create wsse headers for a user");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * Get configured encoderClass
         */
        $container = $this->getContainer();
        $encoderClass = $container->getParameter('mittax.wsse.encoder');


        $helper = $this->getHelper('question');

        $question = new Question('Please enter the username you want to create the header for: ');
        $username = $helper->ask($input, $output, $question);


        $usertablename = $container->getParameter('mittax.wsse.usertablename');

        $passwordcolumn = $container->getParameter('mittax.wsse.passwordcolumn');

        $usernamecolumn = $container->getParameter('mittax.wsse.usernamecolumn');


        $sql = "SELECT {$passwordcolumn} from {$usertablename} where {$usernamecolumn} = '{$username}'";

        $commandLineSqlProcessor = $container->get('mittax_wsse.commandlinesqlprocessor');

        $commandLineSqlProcessor->setEntityManager($container->get('doctrine')->getManager());

        $result = $commandLineSqlProcessor->executeQuery($sql);

        if(empty($result)) throw new InvalidArgumentException('No user: ' . $username . 'found in configured table');

        $databasePassword = $result[0]['password'];

        /**
         * Get configured encrytion salt
         */
        $salt = $container->getParameter('mittax.wsse.salt');

        /** @var  $header \Mittax\WsseBundle\Security\Header\WsseHeader*/
        $header = new WsseHeader($username, $databasePassword, $salt, $encoderClass);

        $headerOutput = $header->toString();

        $output->writeln([
            '====================================================================================================',
            'Your WSSE Header. You can copy an paste it to your header input. The name of the header is : X-WSSE',
            '====================================================================================================',
        ]);

        $output->write($headerOutput);

        $output->writeln([
            '',
        ]);
        $output->writeln([
            '----------------------------------------------------------------------------------------------------',
        ]);
        $output->writeln([
            '',
        ]);

    }
}
