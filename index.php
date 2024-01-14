<?php
require_once('src/PHPMailer.php');
require_once('src/SMTP.php');
require_once('src/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Verificar se a extensão OpenSSL está carregada
if (!extension_loaded('openssl')) {
    echo 'Extension missing: openssl';
    exit;
}

function checkSitesStatus($siteUrls) {
    $statuses = [];

    foreach ($siteUrls as $siteUrl) {
        try {
            $response = @file_get_contents($siteUrl);

            // Se o site respondeu com sucesso (200 OK), consideramos online
            $statuses[$siteUrl] = $response !== false;
        } catch (Exception $e) {
            // Se ocorrer um erro, consideramos offline
            $statuses[$siteUrl] = false;
        }
    }

    return $statuses;
}

$mail = new PHPMailer(true);

$sitesToCheck = [
    'https://prorelax.com.br/',
    'https://fortebrindesme.com.br/',
    'https://sharemedia.com.br/',
    'https://henrilegis.com.br/',
    'https://arcangelcouros.com.br/',
    'https://carrascoegiraldeli.com.br/',
    'https://ajnferroeaco.com.br/',
    'https://sanclereventos.com.br/',
    'https://cwengtele.com/',
    'https://engenharengenhariaeletrica.com.br/',
    'https://monpetitpethotel.com.br/',
    'https://riberpeletizadoras.com.br/',
    'https://sessma.com.br/'
];

$statuses = checkSitesStatus($sitesToCheck);

// Variável de controle para rastrear se o email já foi enviado
$emailEnviado = false;

// Verificar status antes de enviar o e-mail
if (in_array(false, $statuses)) {
    try {
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls'; // Use 'tls' para STARTTLS
        $mail->Username = 'bernardo.sharemedia@gmail.com';
        $mail->Password = 'ldkm yrgb atkt vujl'; // Altere sua senha aqui
        $mail->Port = 587;

        $mail->setFrom('bernardo.sharemedia@gmail.com');
        $mail->addAddress('bndstocco1@gmail.com');
        $mail->addAddress('diego@sharemedia.com.br'); // Novo endereço adicionado
        $mail->addAddress('eduardo@sharemedia.com.br'); // Novo endereço adicionado

        $mail->isHTML(true);
        $mail->Subject = 'Site(s) Offline - Aviso';

        // Construa o corpo do e-mail apenas se houver sites offline e o email ainda não foi enviado
        $offlineSitesMessage = '';
        foreach ($statuses as $siteUrl => $isOnline) {
            if (!$isOnline) {
                $offlineSitesMessage .= "- {$siteUrl} está offline<br>";
            }
        }

        if (!empty($offlineSitesMessage) && !$emailEnviado) {
            $mail->Body = "Um ou mais sites estão offline. Verifique a situação:<br>{$offlineSitesMessage}";
            $mail->AltBody = 'Um ou mais sites estão offline. Verifique a situação.';

            if ($mail->send()) {
                echo 'Email enviado com sucesso';
                $emailEnviado = true; // Atualiza a variável de controle
            } else {
                echo 'Email não enviado';
            }
        } else {
            echo 'Todos os sites estão online. Nenhum e-mail enviado.';
        }
        
    } catch (Exception $e) {
        echo "Erro ao enviar mensagem: {$mail->ErrorInfo}";
    }
} else {
    echo 'Todos os sites estão online. Nenhum e-mail enviado.';
}
?>
