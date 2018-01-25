<?php

namespace App\Commands;

use ErrorException;
use App\Transformer\Transform;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TransformCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('transform')
            ->setDescription('Transform a sql-file according to the rules.')
            ->addArgument('files', InputArgument::OPTIONAL|InputArgument::IS_ARRAY)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $transform = new Transform($this->loadRules(getcwd() . DIRECTORY_SEPARATOR . 'myform.json'));

        foreach ($input->getArgument('files') as $file) {
            $output->writeln(sprintf('transforming: %s...', basename($file, '.sql')));
            $this->processFile($transform, $file);
        }
    }

    protected function loadRules($file)
    {
        return json_decode(file_get_contents($file), true);
    }

    protected function processFile(Transform $transform, $in_file)
    {
        $path = pathinfo($in_file);

        $out_file = $path['dirname'] . DIRECTORY_SEPARATOR . $path['filename'] . '+transformed.sql';

        $fp_read = fopen($in_file, 'r');
        if (!$fp_read) {
            throw new ErrorException(sprintf('unable to read: %s', $in_file));
        }

        $fp_write = fopen($out_file, 'w');
        if (!$fp_write) {
            throw new ErrorException(sprintf('unable to write: %s', $out_file));
        }

        while (!feof($fp_read)) {
            $row = rtrim(fgets($fp_read));

            // skip empty lines
            if (!isset($row[0])) {
                fputs($fp_write, $row . PHP_EOL);
                continue;
            }

            // skip comments
            if ($row[0] === '-' && isset($row[1]) && $row[1] === '-') {
                fputs($fp_write, $row . PHP_EOL);
                continue;
            }
            if (preg_match('~^/\*.*\*/;$~', $row)) {
                fputs($fp_write, $row . PHP_EOL);
                continue;
            }

            fputs($fp_write, $transform->transform($row) . PHP_EOL);
        }

        fclose($fp_read);
        fclose($fp_write);
    }
}
