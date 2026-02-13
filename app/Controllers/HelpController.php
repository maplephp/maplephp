<?php

namespace App\Controllers;

use MaplePHP\Prompts\Themes\Blocks;
use MaplePHP\Unitary\Console\Controllers\DefaultController;

class HelpController extends DefaultController
{
    public function index(): void
    {
        $blocks = new Blocks($this->command);
        $blocks->addHeadline("\n--- Unitary Help ---");
        $blocks->addSection("Usage", "php unitary [type] [options]");
        $blocks->addSection("Options", function (Blocks $inst) {
            return $inst
                ->addOption("--help", "Display this help message.");
        });
    }

}
