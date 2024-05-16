<?php

namespace App\Command\Users;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:get-user',
    description: 'Retrieves a User',
)]
class GetUserCommand extends Command
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
            ->addOption('userId', null, InputOption::VALUE_REQUIRED, 'User Id')
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

        if (!is_numeric($userId)) {
            $inOut->text('Error: Provided User Id is Invalid.');

            return Command::FAILURE;
        }

        try {
            $response = $this->httpClient->request('GET', $this->url . '/api/users/' . $userId);
            $user = $response->toArray();

            $inOut->text(sprintf("ID: %d, Name: %s, Email: %s", $user['id'], $user['name'], $user['email']));

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $inOut->text('Error: Something went wrong.');

            return Command::FAILURE;
        }
    }
}
