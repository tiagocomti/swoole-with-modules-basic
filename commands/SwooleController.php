<?php

namespace app\commands;
use \Swoole\Runtime;
use app\commands\DefaultController as Controller;
use yii\console\ExitCode;
use yii\helpers\BaseConsole;

defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w'));
defined('STDERR') or define('STDERR', fopen('php://stderr', 'w'));

class SwooleController extends Controller
{
    public function actionStart(){
        BaseConsole::output('Starting swoole...');
        BaseConsole::output(shell_exec("/usr/local/bin/php ".__DIR__."/../bootstrap.php"));
    }

    public function actionStop(){
        if($this->kill()){
            BaseConsole::output('Stopping swoole.');
        }
        return ExitCode::OK;
    }

    public function actionRestart(){
        if($this->kill()){
            BaseConsole::output('Stopping swoole.');
        }
        BaseConsole::output('Starting swoole...');
        BaseConsole::output(shell_exec("/usr/local/bin/php ".__DIR__."/../bootstrap.php"));
        return ExitCode::OK;
    }

    private function kill(){
        $pid = @file_get_contents( __DIR__ .'/../runtime/swoole.pid');
        if (empty($pid)) {
            BaseConsole::output('swoole is not running.');
            return ExitCode::UNSPECIFIED_ERROR;
        }
        if (!posix_kill($pid, SIGTERM)) {
            BaseConsole::output('swoole is not running.');
            return ExitCode::UNSPECIFIED_ERROR;
        }
        return true;
    }
}