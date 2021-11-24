<?php


namespace app\helpers;

/**
 * Class responsible for formatting
 *
 * @author Yan Max
 */

class SendMail {

    public static function send($mail_to, $subject, $corpo, $name = false, $link= false, $cc_to = []){
        $validar_email = self::validarEmails(array_merge([$mail_to], $cc_to));
        if($validar_email !== true){
            \Yii::error("Email "._("is not valid: ").$mail_to,"command");
        }

        $cc_to = implode(",", $cc_to);
        $cc_to = str_replace(" ", "", $cc_to);
        $bound = "AGF+ -" . date("dmYis") . "";
        if(trim(ini_get("sendmail_path")) !== "/usr/sbin/sendmail -t -i"){
            ini_set("sendmail_path","/usr/sbin/sendmail -t -i");
        }

        ob_start();
        include(__DIR__."/../mail/layouts/body.php");
        $var = ob_get_contents();
        ob_end_clean();
        $var = str_replace('[$nome]', $name, $var);
        $var = str_replace('[$mensagem]', $corpo, $var);
        $var = str_replace('[$URL]', (!$link)?"https://agfmais.com.br/":$link, $var);

        $mensagem = $var;

        $headers[] = 'from: AGF+ <naoresponda@agfmais.com.br>';
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = "Content-type: text/html; charset=UTF-8; boundary=\"$bound\"";
        $headers[] = 'Cc:'.$cc_to.'';

        try{
            $result_mail = mail($mail_to,$subject,$mensagem,implode("\r\n", $headers), "-f naoresponda@agfmais.com.br");
            \Yii::info("E-mail enviado com sucesso para: '".$mail_to."' com o assunto: ". $subject,"web");
            \Yii::info("E-mail enviado com sucesso para: '".$mail_to."' com o assunto: ". $subject,"command");
            return $result_mail;
        }
        catch (Exception $e){
            \Yii::error("error: '".$e->getMessage(),"command");
            \Yii::error("error: '".$e->getMessage(),"web");
            throw new Exception($e);
        }
    }

    private static function validarEmails($emails){
        foreach($emails as $chave => $valor){
            if(!filter_var(trim($valor), FILTER_VALIDATE_EMAIL)){
                return $valor;
            }
        }
        return true;
    }
}

