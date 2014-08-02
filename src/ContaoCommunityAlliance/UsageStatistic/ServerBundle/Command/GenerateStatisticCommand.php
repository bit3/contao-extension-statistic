<?php

namespace ContaoCommunityAlliance\UsageStatistic\ServerBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateStatisticCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('uss:generate-statistic')
			->setDescription('Generate the weekly, monthly, quarterly and yearly statistics.')
			->addOption('weekly', 'w', InputOption::VALUE_NONE, 'Generate weekly statistics.')
			->addOption('monthly', 'm', InputOption::VALUE_NONE, 'Generate monthly statistics.')
			->addOption('quarterly', 'Q', InputOption::VALUE_NONE, 'Generate quarterly statistics.')
			->addOption('yearly', 'y', InputOption::VALUE_NONE, 'Generate yearly statistics.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$generator = $this->getContainer()->get('usage_statistic_server.service.statistic_generator');

		if ($input->getOption('weekly')) {
			$generator->generateWeekly();
		}

		if ($input->getOption('monthly')) {
			$generator->generateMonthly();
		}

		if ($input->getOption('quarterly')) {
			$generator->generateQuarterly();
		}

		if ($input->getOption('yearly')) {
			$generator->generateYearly();
		}
	}
}
