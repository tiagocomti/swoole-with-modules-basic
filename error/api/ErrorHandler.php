<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ErrorHandler
 *
 * @author tiago
 */

namespace app\error\api;

use app\Exception\NotificationException;
use app\Exception\ActionsException;
use app\helpers\Crypt;
use Yii;
use yii\base\InvalidCallException;
use yii\base\InvalidParamException;
use yii\base\UserException;
use yii\web\HeaderCollection;
use yii\web\Response;
use Error;
use Exception;
use yii\base\ErrorException;
use yii\base\InvalidRouteException;
use yii\console\Controller;
use yii\console\UnknownCommandException;
use yii\helpers\Console;
use yii\helpers\VarDumper;

class ErrorHandler extends \yii\web\ErrorHandler{
    
    const EXCEPTION_UNAUTHORIZED = "Unauthorized";
    const EXCEPTION_BADREQUEST = "BadRequest";
    const EXCEPTION_UNKNOWCLASS = "Unknown Class";
    /**
     * Returns human-readable exception name.
     * @param \Exception $exception
     * @return string human-readable exception name or null if it cannot be determined
     */
    public function getExceptionName($exception)
    {
        if ($exception instanceof \Exception ||
            $exception instanceof InvalidCallException ||
            $exception instanceof InvalidParamException ||
            $exception instanceof \yii\base\UnknownMethodException ||
            $exception instanceof NotificationException ||
            $exception instanceof ActionsException) {
            if(is_callable($exception->getName)) {
                return $exception->getName();
            }else{
                return get_class($exception);
            }
        }

        return null;
    }

    
    public function handleException($exception) {
        $type_excepetion = $this->getExceptionName($exception);
        Yii::error("Fail to executed api. Return: ". \GuzzleHttp\json_encode($exception->getMessage())." Code: ". $exception->getCode().", FileLine".$exception->getFile().":".$exception->getLine()." exception_type: ".$type_excepetion,"api");
        if($type_excepetion === self::EXCEPTION_UNAUTHORIZED || 
            $type_excepetion === self::EXCEPTION_BADREQUEST ||
            $type_excepetion === self::EXCEPTION_UNKNOWCLASS){
            $this->renderExceptionByArray(['return' => false, 'error' => $exception->getMessage(), 'status' => $exception->getCode()]);
        }else if ($type_excepetion === "Not Found"){
            $this->renderExceptionByArray(['return' => false, 'error' => 'ja sabe, neh?', 'status' => 404, 'type' => $type_excepetion]);
        }
        else{
            $this->renderExceptionByArray(['return' => false, 'error' => 'Exception occurred, check in API log files', 'status' => $exception->getCode(), 'type' => $type_excepetion]);
        }

        $this->exception = null;
    }

    public function renderExceptionByArray($array)
    {
        $status_code = 500;
        if (Yii::$app->has('response')) {
            $response = Yii::$app->getResponse();
            $response->isSent = false;
            $response->stream = null;
            $response->data = null;
            $response->content = null;
        } else {
            $response = new Response();
        }

        if((int)$array["status"]  > 100 && (int)$array["status"]  <= 600){
            $status_code = (int)$array["status"];
        }
        $response->setStatusCode($status_code);
        /**
         * if you want this encrypt enable this follow line
         */
//        $response->data = ["encryptedSecretBoxHex" => Crypt::easyEncrypt(json_encode($array), Crypt::getOurSecret())];
        $response->data =$array;
        if(YII_ENV_DEV){
            Yii::info("----Payload Entregue descriptografado---", "api");
            Yii::info("Retorno:".json_encode($array), "api");
            Yii::info("----Payload Entregue Criptografado---", "api");
            Yii::info("Retorno:".json_encode($response->data ), "api");
        }
        $response->send();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function handleFallbackExceptionMessage($exception, $previousException)
    {
        $msg = "An Error occurred while handling another error:\n";
        $msg .= (string)$exception;
        $msg .= "\nPrevious exception:\n";
        $msg .= (string)$previousException;
        if (YII_DEBUG) {
            if (PHP_SAPI === 'cli') {
                echo $msg . "\n";
            } else {
                echo '<pre>' . htmlspecialchars($msg, ENT_QUOTES, Yii::$app->charset) . '</pre>';
            }
        } else {
            echo 'An internal server error occurred.';
        }
        $msg .= "\n\$_SERVER = " . VarDumper::export($_SERVER);
        throw new Exception($msg);
    }

    /**
     * @param int $code
     * @param string $message
     * @param string $file
     * @param int $line
     * @return bool
     * @throws ErrorException
     * @throws Exception
     */
    public function handleError($code, $message, $file, $line)
    {
        if (error_reporting() & $code) {
            if (!class_exists('yii\\base\\ErrorException', false)) {
                require_once Yii::getAlias('@yii/base/ErrorException.php');
            }
            $exception = new ErrorException($message, $code, $code, $file, $line);
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            array_shift($trace);
            foreach ($trace as $frame) {
                if ($frame['function'] === '__toString') {
                    $this->handleException($exception);
                }
            }

            throw $exception;
        }

        return false;
    }

    /**
     * @throws InvalidRouteException
     * @throws \yii\console\Exception
     */
    public function handleFatalError()
    {
        if (!class_exists('yii\\base\\ErrorException', false)) {
            require_once Yii::getAlias('@yii/base/ErrorException.php');
        }

        $error = error_get_last();

        if (ErrorException::isFatalError($error)) {
            $exception = new ErrorException($error['message'], $error['type'], $error['type'], $error['file'],
                $error['line']);
            $this->exception = $exception;

            $this->logException($exception);

            if ($this->discardExistingOutput) {
                $this->clearOutput();
            }
            $this->renderException($exception);

            // need to explicitly flush logs because exit() next will terminate the app immediately
            Yii::getLogger()->flush(true);
        }
    }

    /**
     * @param Error|Exception $exception
     * @throws InvalidRouteException
     * @throws \yii\console\Exception
     */
    protected function renderException($exception)
    {
        if (!Yii::$app->has('response') || Yii::$app->response->getResponse() == null) {
            $this->renderConsoleException($exception);
        } else {
            $this->renderWebException($exception);
        }
    }

    /**
     * Render exception on web environment
     * @param Exception $exception
     * @throws InvalidRouteException
     * @throws \yii\console\Exception
     */
    protected function renderWebException($exception)
    {
        $response = Yii::$app->getResponse();
        // reset parameters of response to avoid interference with partially created response data
        // in case the error occurred while sending the response.
        $response->isSent = false;
        $response->stream = null;
        $response->data = null;
        $response->content = null;

        $response->setStatusCodeByException($exception);

        $useErrorView = $response->format === Response::FORMAT_HTML && (!YII_DEBUG || $exception instanceof UserException);

        if ($useErrorView && $this->errorAction !== null) {
            $result = Yii::$app->runAction($this->errorAction);
            if ($result instanceof Response) {
                $response = $result;
            } else {
                $response->data = $result;
            }
        } elseif ($response->format === Response::FORMAT_HTML) {
            if ($this->shouldRenderSimpleHtml()) {
                // AJAX request
                $response->data = '<pre>' . $this->htmlEncode(static::convertExceptionToString($exception)) . '</pre>';
            } else {
                // if there is an error during error rendering it's useful to
                // display PHP error in debug mode instead of a blank screen
                if (YII_DEBUG) {
                    ini_set('display_errors', 1);
                }
                $file = $useErrorView ? $this->errorView : $this->exceptionView;
                $response->data = $this->renderFile($file, [
                    'exception' => $exception,
                ]);
            }
        } elseif ($response->format === Response::FORMAT_RAW) {
            $response->data = static::convertExceptionToString($exception);
        } else {
            $response->data = $this->convertExceptionToArray($exception);
        }

        $response->send();
    }

    /**
     * Render exception on cli environment
     * @param Exception $exception
     */
    protected function renderConsoleException($exception)
    {
        if ($exception instanceof UnknownCommandException) {
            // display message and suggest alternatives in case of unknown command
            $message = $this->formatMessage($exception->getName() . ': ') . $exception->command;
            $alternatives = $exception->getSuggestedAlternatives();
            if (count($alternatives) === 1) {
                $message .= "\n\nDid you mean \"" . reset($alternatives) . '"?';
            } elseif (count($alternatives) > 1) {
                $message .= "\n\nDid you mean one of these?\n    - " . implode("\n    - ", $alternatives);
            }
        } elseif ($exception instanceof \yii\console\Exception && ($exception instanceof UserException || !YII_DEBUG)) {
            $message = $this->formatMessage($exception->getName() . ': ') . $exception->getMessage();
        } elseif (YII_DEBUG) {
            if ($exception instanceof Exception) {
                $message = $this->formatMessage("Exception ({$exception->getName()})");
            } elseif ($exception instanceof ErrorException) {
                $message = $this->formatMessage($exception->getName());
            } else {
                $message = $this->formatMessage('Exception');
            }
            $message .= $this->formatMessage(" '" . get_class($exception) . "'", [Console::BOLD, Console::FG_BLUE])
                . ' with message ' . $this->formatMessage("'{$exception->getMessage()}'", [Console::BOLD]) //. "\n"
                . "\n\nin " . dirname($exception->getFile()) . DIRECTORY_SEPARATOR . $this->formatMessage(basename($exception->getFile()),
                    [Console::BOLD])
                . ':' . $this->formatMessage($exception->getLine(), [Console::BOLD, Console::FG_YELLOW]) . "\n";
            if ($exception instanceof \yii\db\Exception && !empty($exception->errorInfo)) {
                $message .= "\n" . $this->formatMessage("Error Info:\n", [Console::BOLD]) . print_r($exception->errorInfo, true);
            }
            $message .= "\n" . $this->formatMessage("Stack trace:\n", [Console::BOLD]) . $exception->getTraceAsString();
        } else {
            $message = $this->formatMessage('Error: ') . $exception->getMessage();
        }

        if (PHP_SAPI === 'cli') {
            Console::stderr($message . "\n");
        } else {
            echo $message . "\n";
        }
    }

    /**
     * Colorizes a message for console output.
     * @param string $message the message to colorize.
     * @param array $format the message format.
     * @return string the colorized message.
     * @see Console::ansiFormat() for details on how to specify the message format.
     */
    protected function formatMessage($message, $format = [Console::FG_RED, Console::BOLD])
    {
        $stream = (PHP_SAPI === 'cli') ? STDERR : STDOUT;
        // try controller first to allow check for --color switch
        if (Yii::$app->controller instanceof Controller && Yii::$app->controller->isColorEnabled($stream)
            || Yii::$app instanceof \yii\console\Application && Console::streamSupportsAnsiColors($stream)) {
            $message = Console::ansiFormat($message, $format);
        }

        return $message;
    }
}