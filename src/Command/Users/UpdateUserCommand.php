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
    name: 'app:update-user',
    description: 'Updates a User',
)]
class UpdateUserCommand extends Command
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
            ->addOption('userId', null, InputOption::VALUE_REQUIRED, 'Id(User) to update')
            ->addOption('userName', null, InputOption::VALUE_REQUIRED, 'User Name to update')
            ->addOption('userEmail', null, InputOption::VALUE_REQUIRED, 'User Email to update')
            ->addOption('userGroup', null, InputOption::VALUE_OPTIONAL, 'User Group to update')
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

        $userId = $input->getOption('userId');
        $userName = $input->getOption('userName');
        $userEmail = $input->getOption('userEmail');
        $userGroup = $input->getOption('userGroup');

        if (!is_numeric($userId)) {
            $inOut->text('Error: Provided User Id is Invalid.');

            return Command::FAILURE;
        }

        $jsonData = [
            'name'     => $userName,
            'email'    => $userEmail,
            'group_id' => $userGroup,
        ];

        try {
            $response = $this->httpClient->request('PUT', $this->url . '/api/users/' . $userId, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => $jsonData,
            ]);

            $statusCode = $response->getStatusCode();

            if (Response::HTTP_OK != $statusCode) {
                $inOut->text('Error: Something went wrong.');

                return Command::FAILURE;
            }

            $inOut->text('User was updated.');

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $inOut->text('Error: Something went wrong.');

            return Command::FAILURE;
        }
    }
}
