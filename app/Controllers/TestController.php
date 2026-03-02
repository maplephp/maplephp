<?php

namespace App\Controllers;

use MaplePHP\Core\Routing\DefaultShellController;

class TestController extends DefaultShellController
{
    public function index(): void
    {
        $this->command->message("HELLO WORLD!!");
    }
}