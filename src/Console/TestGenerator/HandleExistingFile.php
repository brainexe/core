<?php

namespace BrainExe\Core\Console\TestGenerator;

use Exception;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * @codeCoverageIgnore
 */
class HandleExistingFile
{

    /**
     * @param string $originalTest
     * @param string $newTest
     * @throws Exception
     * @return string
     */
    private function replaceHeaderOnly($originalTest, $newTest)
    {
        if (!preg_match('/^.*?}/s', $newTest, $matches)) {
            throw new Exception('No header found in new test');
        }

        $header = $matches[0];

        return preg_replace('/^.*?}/s', $header, $originalTest);
    }


    /**
     * @param string $originalTest
     * @param string $newTest
     * @param OutputInterface $output
     * @todo finish
     */
    private function displayPatch($originalTest, $newTest, OutputInterface $output)
    {
        $output->writeln('<info>Diff: Not implemented yet</info>');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $helper
     * @param string $serviceId
     * @param string $testFileName
     * @param string $template
     * @return string
     * @throws Exception
     */
    public function handleExistingFile(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $helper,
        $serviceId,
        $testFileName,
        $template
    ) {
        if ($input->getOption('no-interaction')) {
            $output->writeln(sprintf("Test for '<info>%s</info>' already exist", $serviceId));
            return false;
        }

        $choices = [
            'stop' => 'Stop',
            'replace' => 'Replace full test file',
            'diff' => 'Display the diff',
            'header' => 'full setup only',
        ];
        $question = new ChoiceQuestion(
            '<error>The test file already exist. What should i do now?</error>',
            $choices
        );

        $answer       = $helper->ask($input, $output, $question);
        $originalTest = file_get_contents($testFileName);

        $answerId = array_flip($choices)[$answer];
        switch ($answerId) {
            case 'replace':
                break;
            case 'header':
                $template = $this->replaceHeaderOnly($originalTest, $template);
                break;
            case 'diff':
                $this->displayPatch($originalTest, $template, $output);
                return $template;
            case 'stop':
            default:
                return false;
        }

        return $template;
    }

}
