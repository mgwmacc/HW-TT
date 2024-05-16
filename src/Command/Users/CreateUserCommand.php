<?php

namespace App\Command\Users;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a User',
)]
class CreateUserCommand extends Command
{
    //TODO: put the path to some settings
    private string $url = 'http://127.0.0.1:8000';

    /**
     * @var HttpClientInterface
     */
    private HttpClientInterface $httpClient;

    /**
     * @param HttpClientInterface $httpClient
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addOption('userName', null, InputOption::VALUE_REQUIRED, 'User Name to add')
            ->addOption('userEmail', null, InputOption::VALUE_REQUIRED, 'User Email to add')
            ->addOption('userGroup', null, InputOption::VALUE_REQUIRED, 'User Group to add')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inOut = new SymfonyStyle($input, $output);

        $userName = $input->getOption('userName');
        $userEmail = $input->getOption('userEmail');
        $userGroup = $input->getOption('userGroup');

        if (!$userName) {
            $inOut->text('Error: Provided User Name is Invalid.');

            return Command::FAILURE;
        }

        if (!$userEmail) {
            $inOut->text('Error: Provided User Email is Invalid.');

            return Command::FAILURE;
        }

        if (!$userGroup) {
            $inOut->text('Error: Provided User Group is Invalid.');

            return Command::FAILURE;
        }

        $jsonData = [
            'name'     => $userName,
            'email'    => $userEmail,
            'group_id' => $userGroup,
        ];

        try {
            $response = $this->httpClient->request('POST', $this->url . '/api/users', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => $jsonData,
            ]);

            $statusCode = $response->getStatusCode();

            if (Response::HTTP_CREATED != $statusCode) {
                $inOut->text('Error: Something went wrong.');

                return Command::FAILURE;
            }

            $inOut->text('User was created.');

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $inOut->text('Error: Something went wrong.');

            return Command::FAILURE;
        }
    }
}
