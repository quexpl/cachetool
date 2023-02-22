<?php

/*
 * This file is part of CacheTool.
 *
 * (c) Samuel Gordalina <samuel.gordalina@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CacheTool\Command;

use CacheTool\Util\Formatter;
use CacheTool\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OpcacheStatusScriptsCommand extends AbstractOpcacheCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('opcache:status:scripts')
            ->setDescription('Show scripts in the opcode cache')
            ->setHelp('');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->ensureExtensionLoaded('Zend OPcache');
        $this->json = $input->hasParameterOption('--json');
        $info = $this->getCacheTool()->opcache_get_status(true);
        $this->ensureSuccessfulOpcacheCall($info);

        $table = new Table($output);
        $table
            ->setHeaders([
                'Hits',
                'Memory',
                'Filename'
            ])
            ->setRows($this->processFilelist($info['scripts']))
        ;

        $table->setJson($this->json);
        $table->render();

        return 0;
    }

    protected function processFileList(array $cacheList)
    {
        $list = [];

        foreach ($cacheList as $item) {
            $list[] = [
                number_format($item['hits']),
                Formatter::bytes($item['memory_consumption']),
                $item['full_path'],
            ];
        }

        return $list;
    }
}
