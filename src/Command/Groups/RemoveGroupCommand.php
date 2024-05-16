<?php

namespace App\Command\Groups;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;

#[AsCommand(
    name: 'app:remove-group',
    description: 'Removes a Group (and relate Users)',
)]
class RemoveGroupCommand extends Command
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
            ->addOption('groupId', null, InputOption::VALUE_REQUIRED, 'Group Id to remove')
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

        $groupId = $input->getOption('groupId');

        if (!is_numeric($groupId)) {
            $inOut->text('Error: Provided Group Id is Invalid.');

            return Command::FAILURE;
        }

        try {
            $response   = $this->httpClient->request('DELETE', $this->url . '/api/groups/' . $groupId);
            $statusCoed = $response->getStatusCode();

            if (Response::HTTP_NOT_FOUND == $statusCoed) {
                $inOut->text('Error: Group with the Id provided was not found');

                return Command::FAILURE;
            }

            $inOut->text('Group (with relate users if any) was removed.');

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $inOut->text('Error: Something went wrong.');

            return Command::FAILURE;
        }
    }
}
