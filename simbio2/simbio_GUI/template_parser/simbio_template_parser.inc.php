<?php


// be sure that this file not accessed directly
if (!defined('INDEX_AUTH')) {
    die("can not access this file directly");
} elseif (INDEX_AUTH != 1) { 
    die("can not access this file directly");
}

class simbio_template_parser
{
    private $file = ''; // variable to store template name info
    private $result = ''; // variable to store template result


    /**
     * Class Constructor
     *
     * @param   string  $tpl_file_path
     * @return  void
     */
    public function __construct($tpl_file_path)
    {
        // check if file exists
        if (!file_exists($tpl_file_path)) {
            die('Template file '.$tpl_file_path.' doesnt exists!');
        }

        // check if the template file is HTML or not
        if (substr($tpl_file_path, -5) != '.html') {
            die('Template file must be HTML file!');
        } else {
            $this->file = $tpl_file_path;
        }

        // reading template file and store it into result properties
        if ($tpl_str = file_get_contents($this->file)) {
            $this->result = $tpl_str;
        } else {
           die('<div style="border: 1px solid #FF0000; color: #FF0000;">Template file '.$this->file.' cant be opened!</div>');
        }
    }


    /**
     * Method assign content to template marker
     *
     * @param   string  $str_maker
     * @param   string  $str_replacement
     * @param   boolean $bool_show_error
     * @return  void
     */
    public function assign($str_maker, $str_replacement, $bool_show_error = false)
    {
        if (!preg_match("/".preg_quote($str_maker)."/i", $this->result)) {
            if ($bool_show_error) {
                $str_maker = str_ireplace(array('<!--','-->'), '', $str_maker);
                echo "Marker : <b>".htmlentities($str_maker)."</b> doesnt exist in ".$this->file;
            }
            return;
        }
        $this->result = str_ireplace($str_maker, $str_replacement, $this->result);
        // get any gettext marker
        preg_match_all('@<!--__\(.+\)-->@i', $this->result, $_gettext);
        if (count($_gettext[0]) > 0) {
            foreach ($_gettext[0] as $_trans_mark) {
                $_trans_text = str_replace(array('<!--__(\'','\')-->'), '', $_trans_mark);
                $this->result = str_ireplace($_trans_mark, __($_trans_text), $this->result);
            }
        }
    }


    /**
     * Method to print out the template
     *
     * @return  void
     */
    public function printOut()
    {
        echo $this->result;
    }
}
