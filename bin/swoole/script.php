#!/usr/local/bin/php
<?php
if (!isset($_SERVER['argv'])) {
    echo "ERROR ==> This script must be run from the command line.\n";
    exit(1);
}
$arg = $_SERVER['argv'];

if(!is_array($arg) || count($arg) != 2){
    echo 'Usage: /usr/local/etc/rc.d/proapps-api-daemon [fast|force|one|quiet](start|stop|restart|status|reload)'.PHP_EOL;
    exit(1);
}


use swoole\foundation\web\Server;
use \Swoole\Runtime;

defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w'));
defined('STDERR') or define('STDERR', fopen('php://stderr', 'w'));

/**
 * Gerenciamento de servidor Swoole
 * Class ServerController
 * @package app\commands
 */
class ServerSwoole
{
    public function stderr($string)
    {
        return fwrite(\STDERR, $string);
    }

    public function stdout($string)
    {
        return fwrite(\STDOUT, $string);
    }

    /**
     * Enviar sinal
     * @param int $signal
     */
    private function kill($signal)
    {
        $pid = @file_get_contents('/usr/local/www/api/basic/runtime/swoole.pid');
        if (empty($pid)) {
            $this->stderr('swoole is not running.'.PHP_EOL);
            exit(1);
        }
        if (!posix_kill($pid, $signal)) {
            $this->stderr('swoole is not running.'.PHP_EOL);
            exit(1);
        }
        return TRUE;
    }

    /**
     * Status do servidor
     */
    public function actionStatus()
    {
        $pid = @file_get_contents('/usr/local/www/api/basic/runtime/swoole.pid');
        if (empty($pid)) {
            $this->stderr("swoole is not running.\n");
            exit(1);
        }
        $pid_status = exec('/bin/ps '.(int)$pid);
        if(!preg_match('/^'.(int)$pid.' .*/',$pid_status)){
            $this->stderr("swoole is not running.\n");
            exit(1);
        }
        $this->stdout('swoole is running as pid '.(int)$pid.'.'.PHP_EOL);
    }

    /**
     * Inicie o servidor Swoole
     */
    public function actionStart()
    {
        $this->stdout('Starting swoole...'.PHP_EOL);
        shell_exec("/usr/local/bin/php /usr/local/www/api/basic/bootstrap.php");
    }

    /**
     * Desligue o servidor
     */
    public function actionStop()
    {
        if($this->kill(SIGTERM) !== FALSE){
            $this->stdout('Stopping swoole.'.PHP_EOL);
        }
    }

    /**
     * recarregar processo de trabalho
     */
    public function actionReload()
    {
        if($this->kill(SIGUSR1) !== FALSE){
            $this->stdout('Reloading swoole.'.PHP_EOL);
        }

    }
}

$server = new ServerSwoole();

switch (trim($arg[1])) {
    case 'start':
        $server->actionStart();
        break;
    case 'reload':
        $server->actionReload();
        break;
    case 'stop':
        $server->actionStop();
        break;
    case 'status':
        $server->actionStatus();
        break;
    default:
        $server->actionStatus();
        break;
}