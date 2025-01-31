<?php

declare(strict_types = 1);

namespace Acquia\Cli\Command\App;

use Acquia\Cli\Command\CommandBase;
use Acquia\Cli\Exception\AcquiaCliException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:unlink')]
class UnlinkCommand extends CommandBase {

  protected function configure(): void {
    $this->setDescription('Remove local association between your project and a Cloud Platform application')
      ->setAliases(['unlink']);
  }

  protected function commandRequiresAuthentication(): bool {
    return FALSE;
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $this->validateCwdIsValidDrupalProject();

    $projectDir = $this->projectDir;
    if (!$this->getCloudUuidFromDatastore()) {
      throw new AcquiaCliException('There is no Cloud Platform application linked to {projectDir}', ['projectDir' => $projectDir]);
    }

    $application = $this->getCloudApplication($this->datastoreAcli->get('cloud_app_uuid'));
    $this->datastoreAcli->set('cloud_app_uuid', NULL);
    $output->writeln("<info>Unlinked <options=bold>$projectDir</> from Cloud application <options=bold>{$application->name}</></info>");

    return Command::SUCCESS;
  }

}
