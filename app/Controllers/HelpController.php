<?php

namespace App\Controllers;

use MaplePHP\Core\Routing\DefaultController;
use MaplePHP\Prompts\Themes\Blocks;

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
