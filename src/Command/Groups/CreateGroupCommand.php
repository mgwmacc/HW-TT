<?php

namespace App\Command\Groups;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:create-group',
    description: 'Creates a Group',
)]
class CreateGroupCommand extends Command
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
            ->addOption('groupName', null, InputOption::VALUE_REQUIRED, 'Group Name to add')
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

        $groupName = $input->getOption('groupName');

        if (!$groupName) {
            $inOut->text('Error: Provided Group Name is Invalid.');

            return Command::FAILURE;
        }

        $jsonData = [
            'name'     => $groupName,
        ];

        try {
            $response = $this->httpClient->request('POST', $this->url . '/api/groups', [
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

            $inOut->text('Group was created.');

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $inOut->text('Error: Something went wrong.');

            return Command::FAILURE;
        }
    }
}