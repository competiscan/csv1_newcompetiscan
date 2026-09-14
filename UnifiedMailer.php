<?php
require 'vendor/autoload.php';
require 'includes/unified_mailer_defs.php';

class UnifiedMailer
{
    /** @var PHPMailer $mailer */
    private $mailer;

    function __construct($env = false)
    {
        global $unified_mailer_defs, $unified_mailer_env;

        if (empty($unified_mailer_env)) {
            $unified_mailer_env ='DEV';
        }

        $the_env = ($env) ? $env : $unified_mailer_env;
        $conf = $unified_mailer_defs[$the_env];
        $this->mailer = new PHPMailer(true);
        $this->mailer->Mailer = 'smtp';
        $this->mailer->Host = $conf['host'];
        $this->mailer->Port = $conf['port'];

        if (!empty($conf['protocol'])) {
            $this->mailer->SMTPSecure = $conf['protocol'];
        }

        if (!empty($conf['debug'])) {
            $this->mailer->SMTPDebug = 3;
        }

        if (!empty($conf['user'])) {
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $conf['user'];
            $this->mailer->Password = $conf['password'];
        }
    }

    function addAttachment($type, $name, $content)
    {
        $this->mailer->addStringAttachment($content, $name);
    }

    function send($from, $to, $subject, $body, $fromName = 'Richard Goldman')
    {
        try {
            if (strpos($from, '@competiscan.com') === false) {
                $this->mailer->addReplyTo($from, $fromName);
                $from = 'share@competiscan.com';
            }

            $this->mailer->SetFrom($from, $fromName);

            foreach ($to as $send_type => $emails) {
                if (is_numeric($send_type)) {
                    $this->handleMandrillEmail($emails);
                    continue;
                } else {
                    if ($send_type == 'to') {
                        if (is_array($emails)) {
                            foreach ($emails as $email_addr) {
                                $this->mailer->addAddress($email_addr);
                            }
                        } else {
                            $this->mailer->addAddress($emails);
                        }
                    } elseif ($send_type == 'cc') {
                        if (is_array($emails)) {
                            foreach ($emails as $email_addr) {
                                $this->mailer->AddCC($email_addr);
                            }
                        }
                    } elseif ($send_type == 'bcc') {
                        if (is_array($emails)) {
                            foreach ($emails as $email_addr) {
                                $this->mailer->AddBCC($email_addr);
                            }
                        }
                    }
                }
            }

            $this->mailer->isHTML();
            $this->mailer->Subject = $subject;
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->Body = $body;
            $this->mailer->WordWrap = 50;
            $this->mailer->AltBody = $this->mailer->html2text(nl2br($body));
            $this->mailer->send();
            $this->mailer->clearAllRecipients();

            return true;
        } catch (Exception $e) {
           // print_r($e);
            error_log($e->getMessage());

            return false;
        }
    }

    /**
     * @param $email
     * @return mixed
     */
    private function handleMandrillEmail($email)
    {
        $this->mailer->addAddress($email['email'], $email['name']);
    }
}
