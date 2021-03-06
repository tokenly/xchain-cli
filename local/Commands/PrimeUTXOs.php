<?php

namespace XChainCLI\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tokenly\XChainClient\Client;
use \Exception;

class PrimeUTXOs extends XChainCommand {

    protected $name        = 'x:prime';
    protected $description = 'Primes an address with UTXOs';

    protected function configure() {
        parent::configure();

        $this
            ->addArgument(
                'payment-address-id',
                InputArgument::REQUIRED,
                'Payment UUID'
            )
            ->addArgument(
                'size',
                InputArgument::REQUIRED,
                'UTXO size'
            )
            ->addArgument(
                'count',
                InputArgument::REQUIRED,
                'Number of desired UTXOs'
            )
            ->addOption(
                'fee', 'f',
                InputOption::VALUE_OPTIONAL,
                'Fee',
                0.0001
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $payment_address_id = $input->getArgument('payment-address-id');
        $size               = $input->getArgument('size');
        $count              = $input->getArgument('count');
        $fee                = $input->getOption('fee');

        // init the client
        $client = $this->getClient($input);

        // get the info
        $payment_address_details = $client->getPaymentAddress($payment_address_id);
        $address = $payment_address_details['address'];
        $output->writeln("<comment>Address is $address</comment>");

        $balances = $client->getBalances($address);
        $btc_balance = $balances['BTC'];
        $output->writeln("<comment>BTC balance is $btc_balance</comment>");

        $output->writeln("<comment>calling primeUTXOs($payment_address_id, $size, $count, $fee)</comment>");
        $result = $client->primeUTXOs($payment_address_id, $size, $count, $fee);
        $output->writeln("<info>Result\n".json_encode($result, 192)."</info>");
    }

}
