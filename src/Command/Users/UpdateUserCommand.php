<?php

namespace App\Command\Users;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;

#[AsCommand(
    name: 'app:update-user',
    description: 'Updates a User',
)]
class UpdateUserCommand extends Command
{
    /**
     * @var string
     */
    private string $url = '';

    /**
     * @var HttpClientInterface
     */
    private HttpClientInterface $httpClient;

    /**
     * @param HttpClientInterface $httpClient
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(HttpClientInterface $httpClient, ParameterBagInterface $parameterBag)
    {
        $this->httpClient = $httpClient;
        $this->url        = $parameterBag->get('API_SERVER_URL');

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
