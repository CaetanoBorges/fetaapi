<?php

namespace Ferramentas;

class Funcoes
{
    public static $conexao = null;

    static function Tokeniza($str)
    {
        $res = self::valid($str);
        if (gettype($res) == "array") {
            return true;
        } else {
            return false;
        }
    }
    static function valid($token)
    {
        $token = explode(".", $token);

        $sms = $token[0];
        $chave = $token[1];
        $cript = new Criptografia();
        $chave = $cript->decriptChave($chave);
        $res = $cript->decrypt($sms, $chave);

        $r = (array) json_decode($res);

        if (count($r) > 1) {
            return $r;
        } else {
            return "Erro, token inválido!";
        }
    }

    static function seisDigitos()
    {
        return mt_rand(100000, 999999);
    }

    static function HTTPpost($url, $opt)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($opt));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }

    static function enviaSMS($numero, $mensagem)
    {
        // get token
        $enviar = self::HTTPpost("https://app.smshub.ao/api/authentication", ["authId" => "692533754324503635", "secretKey" => 'pd4x53HDSTRkqbEznKHzThkBA9behOHnNiPDX8QnKtHX8Rr07vtAXr8DPaWPRvZKo6uEg4SDSF7Gr6C3LcHThLDKl1AWsyH8tTVe']);
        $res = (array) json_decode($enviar);
        $data = (array) $res['data'];

        // set post fields
        $post['contactNo'] = [$numero];
        $post['message'] = $mensagem;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://app.smshub.ao/api/sendsms");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = [
            'accessToken:' . $data['authToken']
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $res = curl_exec($ch);
        curl_close($ch);


        return $res;
    }
    static function setRemetente($nome)
    {
        // get token
        $enviar = self::HTTPpost("https://app.smshub.ao/api/authentication", ["authId" => "692533754324503635", "secretKey" => 'pd4x53HDSTRkqbEznKHzThkBA9behOHnNiPDX8QnKtHX8Rr07vtAXr8DPaWPRvZKo6uEg4SDSF7Gr6C3LcHThLDKl1AWsyH8tTVe']);
        $res = (array) json_decode($enviar);
        $data = (array) $res['data'];

        // set post fields
        $post['name'] = $nome;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://app.smshub.ao/api/sender");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            'accessToken:' . $data['authToken']
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }

    static function enviaEmail($mail, $email, $titulo, $corpo, $confPath = "email.conf.json")
    {
        $conf = file_get_contents($confPath);

        $configuracao = (array) json_decode($conf);

        // Passing `true` enables exceptions
        //Server settings
        $mail->SMTPDebug = 0;           // Enable verbose debug output
        $mail->isSMTP();  // Set mailer to use SMTP
        $mail->Host = $configuracao['servidor'];  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;   // Enable SMTP authentication
        $mail->Username = $configuracao['usuario'];         // SMTP username
        $mail->Password = $configuracao['palavra_passe'];           // SMTP password
        $mail->SMTPSecure = $configuracao['seguranca']; // Enable TLS encryption, `ssl` also accepted
        $mail->Port = $configuracao['porta']; // TCP port to connect to

        //Recipients
        $mail->setFrom($configuracao['usuario'], $configuracao['nome']);
        //$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
        $mail->addAddress($email);   // Name is optional
        //$mail->addReplyTo('info@example.com', 'Information');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');

        //Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

        //Content
        $mail->isHTML(true);          // Set email format to HTML
        $mail->Subject = mb_convert_encoding($titulo, 'ISO-8859-1');
        $mail->Body    = $corpo;
        $mail->AltBody = $corpo;


        if (!$mail->send()) {
            return false;
        } else {
            return true;
        }
    }

    static function chaveDB()
    {
        return uniqid();
    }

    static function data()
    {
        $data['dia'] = date('d');
        $data['mes'] = date('m');
        $data['ano'] = date('y');
        return $data;
    }

    static function conexao()
    {

        if (isset(self::$conexao)) {
            return self::$conexao;
        }

        self::$conexao = pg_connect("host=localhost port=5432 dbname=fetafacil user=postgres password=root");
        return self::$conexao;
    }
    static function queryDB($sql, $retorna = true)
    {
        $conexao = self::conexao();
        $query = <<<EOF
            $sql
        EOF;

        $result = pg_query($conexao, $query);
        if($retorna){
            return pg_fetch_all($result);
        }
        return true;
    }
    static function substituiEspacoPorMais($variavel)
    {
        return str_replace(" ", "+", $variavel);
    }
    static function fazHash($valor)
    {
        return hash("sha512", $valor);
    }
    static function quando($quando)
    {
        date_default_timezone_set("Africa/Luanda");
        return date("d-m-Y H:i:s A", $quando);
    }

}
