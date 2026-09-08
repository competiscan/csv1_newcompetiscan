<?php
/**
 * Created by PhpStorm.
 * User: pkaluza
 * Date: 5/4/16
 * Time: 3:52 PM
 */
class MailTemplateEngine
{


    public function meta( $template_markup, $tags = array('SUBJECT')){

        $matches = array();
        $meta = array();

        foreach ($tags as $t) {

            $pattern = "~<!--$t (.*) -->~";
            preg_match_all($pattern, $template_markup, $matches);
            $meta[$t] = array_shift($matches[1]);
        }


        return  $meta;


    }
    public function run($template_markup, $vars)
    {

        $vars = $this->normalizeVars($vars);

        $pattern = '~\[(\w+)\]~';
        $parsed = preg_replace_callback($pattern, function ($matches) use ($vars) {
            return $vars[$matches[1]];
        }, $template_markup);

        $p2 = '~\$(\w+)~';
       // $parsed = $matches;
//$parsed = preg_replace_callback($p2, function ($matches) use ($vars) {
            //return $vars[$matches[1]];
        //}, $parsed);


        return $parsed;

    }


    public function fromFile($file, $vars){

        $template = file_get_contents($file);

        return $this->run($template, $vars);

    }

    /** converts mandrill
     * template variables in form [0] => array('name' => 'KEY', 'content' => 'VALUE')
     * to array('KEY' => 'VALUE)  */
    private function convertFromMandrill($vars){

        $res = array();

        foreach ($vars as $v) {
            $res[$v['name']]  =  $v['content'];
        }

        return $res;
    }

    /** Normalizes variable formats handles conversion from mandrill if detected
     * @param $vars
     * @return array
     */
    private function normalizeVars($vars)
    {
        $testKey = array_shift(array_keys($vars));

        return (is_numeric($testKey) && isset($vars[0]['name'])) ? $this->convertFromMandrill($vars) : $vars;
    }




}