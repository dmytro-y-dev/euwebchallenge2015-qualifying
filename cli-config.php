<?php

// Doctrine CLI config (required for doctrine schema tools)

use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once 'src/bootstrap.php';

return ConsoleRunner::createHelperSet($entityManager);
