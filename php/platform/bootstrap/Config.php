<?php
namespace NGS;

// Autoload NGS + generated modules
require_once __DIR__.'/Dirs.php';

// Recompiles DSL, generates sources
require_once Dirs::$bootstrap.'Compiler.php';
Compiler::checkRebuild();

// Loads static platform components
require_once Dirs::$bootstrap.'Requirements.php';
Requirements::init();

// Loads dynamic platform components
require_once Dirs::$modules.'Modules.php';

// Initializes the RestHttp connection
use NGS\Client\RestHttp;
RestHttp::instance(new RestHttp(
    Project::$apiUrl,
    Project::$username,
    Project::$ID
));
