<?php
require_once('../UnifiedMailer.php');
require_once('MailTemplateEngine.php');
class TemplateMailer
{
    private $template;
    private $to_address_array;
    private $um;
    private $mte;

    function __construct($mailer = false, $templateEngine = false)
    {



        $this->um = ($mailer) ? $mailer :  new UnifiedMailer();
        $this->mte = ($templateEngine) ? $templateEngine:   new MailTemplateEngine();
        $this->to_address_array = array();
    }


    public function addToAddress($to_address)
    { //eg: array("email" => "mhollingshead@highlandsolutions.com", "name" => "Max")
        $this->to_address_array[] = $to_address;
    }

    public function addAttachment($type, $name, $content)
    {

        return $this->um->addAttachment($type, $name, $content);

    }

    public function setTemplate($template_name)
    {
        $this->template = $template_name;
    }

    public function send($placeholder_vars = array())
    {

        $message = $this->mte->fromFile(dirname(__FILE__) . '/mail-templates/' . $this->template . '.tmp.html', $placeholder_vars);
        $meta = $this->mte->meta($message);

        $subject = $meta['SUBJECT'];
        return $this->um->send('share@competiscan.com', $this->to_address_array, $subject, $message);

    }
}
