<?php
// http://www.atkinson.kiwi/programming/creating-phar-cli-programs.html

error_reporting(E_ALL);
ini_set('display_errors', true);

$pharFile = $_SERVER['argv'][1];

// clean up
if (file_exists($pharFile)) {
    unlink($pharFile);
}
if (file_exists($pharFile . '.gz')) {
    unlink($pharFile . '.gz');
}

// create phar
$p = new Phar($pharFile);

// creating our library using whole directory
$p->buildFromDirectory(dirname(__FILE__), '~^(?!(.*(\.git|\.bash)))(.*)$~i');

$stub = <<<"EOT"
#!/usr/bin/env php
<?php
Phar::mapPhar('$pharFile');
set_include_path('phar://$pharFile' . PATH_SEPARATOR . get_include_path());
require('cli.php');
__HALT_COMPILER();
EOT;

$p->setStub($stub);

// plus - compressing it into gzip
//$p->compress(Phar::GZ);
unset($p);
