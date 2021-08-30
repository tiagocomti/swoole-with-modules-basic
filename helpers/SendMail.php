<?php


namespace app\helpers;

/**
 * Class responsible for formatting
 *
 * @author Yan Max
 */
class SendMail {

    public static function send($mail_to, $subject, $corpo, $cc_to = []){
        $validar_email = self::validarEmails(array_merge([$mail_to], $cc_to));
        if($validar_email !== true){
            throw new Exception("Email "._("is not valid: ") .$validar_email);
        }

        $bound = "Privatus -" . date("dmYis") . "- FreeBSD Brasil";
        if(trim(ini_get("sendmail_path")) !== "/usr/sbin/sendmail -t -i"){
            ini_set("sendmail_path","/usr/sbin/sendmail -t -i");
        }

        $mensagem = $corpo;

        $headers[] = 'from: Privatum <staffproapps@server.privatusdev.com.br>';
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = "Content-type: text/plain; boundary=\"$bound\"";

        try{
            $result_mail = mail($mail_to,$subject,$mensagem,implode("\r\n", $headers), "-f staffproapps@server.privatusdev.com.br");
            \Yii::info("E-mail enviado com sucesso para: '".$mail_to."' com o assunto: ". $subject,"api");
            \Yii::info("resultado '".$result_mail,"api");
            return $result_mail;
        }
        catch (Exception $e){
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
