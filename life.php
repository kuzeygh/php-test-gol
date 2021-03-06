<?php

require "vendor/autoload.php";
use MidoriKocak\GameOfLife\GameOfLife;

$climate = new League\CLImate\CLImate;

$climate->arguments->add([
    'random' => [
        'prefix' => 'r',
        'longPrefix' => 'random',
        'description' => 'Create Game of life from random matrix',
        'noValue' => true,
    ],
    'iterations' => [
        'prefix' => 'i',
        'longPrefix' => 'iterations',
        'description' => 'Number of iterations',
        'castTo' => 'int',
    ],
    'size' => [
        'prefix' => 's',
        'longPrefix' => 'size',
        'description' => 'Square size of the world',
        'castTo' => 'int',
    ],
    'species' => [
        'prefix' => 'sp',
        'longPrefix' => 'species',
        'description' => 'Amount of species',
        'castTo' => 'int',
    ],
    'filename' => [
        'prefix' => 'f',
        'longPrefix' => 'filename',
        'description' => 'File Name of XML input',
        'castTo' => 'string',
    ],
    'outputFilename' => [
        'prefix' => 'o',
        'longPrefix' => 'outputFilename',
        'description' => 'File Name of XML output',
        'castTo' => 'string',
        'defaultValue' => 'out.xml',
    ],
    'verbose' => [
        'prefix' => 'v',
        'longPrefix' => 'verbose',
        'description' => 'Verbose output, shows GameOfLife in CLI',
        'noValue' => true,
    ],
    'help' => [
        'prefix' => 'h',
        'longPrefix' => 'help',
        'description' => 'Prints a usage statement',
        'noValue' => true,
    ],
]);

$climate->description("Connway's Game of Life by Midori Kocak");
$climate->arguments->parse();

try {
    if ($climate->arguments->defined('help')) {
        $climate->usage();
        exit();
    }

    $verbose = false;

    if ($climate->arguments->defined('verbose')) {
        $verbose = true;
    }

    if ($climate->arguments->defined('filename')) {
        $filename = $climate->arguments->get('filename');
    } elseif ($climate->arguments->defined('random')) {
        $exit = false;
        if (!$climate->arguments->defined('size')) {
            $climate->to('error')->red('Needs world --size or -s as a parameter');
            $exit = true;
        } else {
            $size = $climate->arguments->get('size');
        }

        if (!$climate->arguments->defined('iterations')) {
            $climate->to('error')->red('Needs number of --iterations or -i as a parameter');
            $exit = true;
        } else {
            $iterations = $climate->arguments->get('iterations');
        }

        if (!$climate->arguments->defined('species')) {
            $climate->to('error')->red('Needs number of --species or -sp as a parameter');
            $exit = true;
        } else {
            $species = $climate->arguments->get('species');
        }
        if ($exit === true) {
            $climate->usage();
            exit();
        }

        $randomArray = GameOfLife::createRandomMatrix($size, $size, $species);

        if ($climate->arguments->defined('filename')) {
            $filename = $climate->arguments->get('filename');
        } else {
            $filename = 'data/random.xml';
        }

        GameOfLife::createXMLfromCells($filename, $randomArray, $species, $iterations);
    }

    if ($climate->arguments->defined('outputFilename')) {
        $outputFilename = $filename = $climate->arguments->get('outputFilename');
        if (empty($outputFilename)) {
            $outputFilename = "out.xml";
        }
        $gameOfLife = new GameOfLife($filename, $outputFilename);
    } elseif (isset($filename) || $climate->arguments->defined('filename')) {
        $gameOfLife = new GameOfLife($filename);
    }
    if (isset($gameOfLife)) {
        $gameOfLife->start($verbose);
    } else {
        $climate->usage();
        exit();
    }
} catch (Exception $e) {
    $climate->to('error')->red($e->getMessage());
    $climate->usage();
    exit();
}

