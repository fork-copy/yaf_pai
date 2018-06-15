<?php

xhprof_enable();


    define('APPLICATION_PATH', dirname(__FILE__));

$application = new Yaf_Application( APPLICATION_PATH . "/conf/application.ini");

$application->bootstrap()->run();


// stop profiler
$xhprof_data = xhprof_disable();
// display raw xhprof data for the profiler run

$rootPath = APPLICATION_PATH. "/application/library/ThirdParty/xhprof_lib/utils/";

    include_once  $rootPath."xhprof_runs.php";
    include_once $rootPath."xhprof_lib.php";

// save raw data for this profiler run using default
// implementation of iXHProfRuns.
$xhprof_runs = new XHProfRuns_Default();
// save the run under a namespace "xhprof_foo"
$run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_foo");

echo "---------------\n".
    "Assuming you have set up the http based UI for \n".
    "XHProf at some address, you can view run at \n".
    "http://<xhprof-ui-address>/index.php?run=$run_id&source=xhprof_foo\n".
    "---------------\n";