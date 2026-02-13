<?php

namespace App\Controllers;

use MaplePHP\Unitary\Console\Controllers\DefaultController;

class TestController extends DefaultController
{

    public function index(): void
    {
        $this->command->message("HELLO WORLD!!");
    }

}
