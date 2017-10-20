<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
$obj = new main;
class Manage {
    public static function autoload($class) {
        include $class . '.php';
    }
}
spl_autoload_register(array('Manage', 'autoload'));
class main
{
    public function __construct()
    {   //set default page as when there is no page requested
        $pageRequest = 'uploadPage';
        if(isset($_REQUEST['page']))
        {
            //load the type of page the request wants into page request
            $pageRequest = $_REQUEST['page'];
        }
        //creates an object of whichever page is requested for
        $page = new $pageRequest;

        //calls the get or post function depending on the request method
        if($_SERVER['REQUEST_METHOD'] == 'GET') {
            $page->get();
        } else {
            $page->post();
        }
    }
}
abstract class page
{
    protected $html;
    function __construct()
    {
        $this->html .= '<html><head>';
        $this->html .= '<link rel="stylesheet" href="styles.css" type="text/css">';
        $this->html .= '</head><body>';
        $this->html .= '<body>';
    }
    function __destruct()
    {
        $this->html .= '</body></html>';
        stringFunctions::printOut($this->html);

    }
}
class htmlTable extends page
{
    function get()
    {
        $name = $_REQUEST['filename'];
        $f = fopen("$name", "r");

            $this->html.='<table>';
//dislplay header
        if (true) {
            $csvcontents = fgetcsv($f);
            $this->html.= '<tr>';
            foreach ($csvcontents as $headercolumn) {
                $this->html.= "<th>$headercolumn</th>";
            }
            $this->html.= '</tr>';
        }
// display contents
        while ($csvcontents = fgetcsv($f)) {
            $this->html.= '<tr>';
            foreach ($csvcontents as $column) {
                $this->html.= "<td>$column</td>";
            }
            $this->html.= '</tr>';
        }
        $this->html.='</table>';
        fclose($f);

    }
}
class uploadPage extends page
{
    //gets the file from the use
    public function get()
    {
        $form = "<form action='index.php?page=uploadPage' method='POST' enctype='multipart/form-data'>";
        $form .= '<h3>Select file to upload</h3>';
        $form .= '<br>';
        $form .= "<input type='file' name='fileT' id='file'>";
        $form .= "<input type='submit' name='submit' value='Upload' >";
        $form .= '</form>';
        $this->html .= '<h1>Upload Page</h1>';
        $this->html .= $form;
    }

    //uploads the file
    public function post()
    {
        $target_dir = "uploads/";
        $file = basename($_FILES["fileT"]["name"]);
        $target_file = $target_dir . $file;

        //checks the file type
        if( fileHandling::checkType($file)=='csv')
        {       $homepage = '<br>';
                $homepage .= '<a href="index.php?page=uploadPage">Go back</a>';
            //check if file exists, then uploads the file and displays the file in html table..
          echo file_exists($target_file)?'File already exists'. $this->html.=$homepage:(move_uploaded_file($_FILES["fileT"]["tmp_name"],$target_file)?header("Location:index.php?page=htmlTable&filename=$target_file"):'File Not Uploaded');

        }
        else
            {
                echo ' Please upload a CSV file';
        }
    }
}
class homepage extends page
{
    //gets user inputs
    public function get()
    {
        $form = '<form action="index.php?page=homepage" method="post">';
        $form .= 'First name:<br>';
        $form .= '<input type="text" name="firstname" value="" required>';
        $form .= '<br>';
        $form .= 'Last name:<br>';
        $form .= '<input type="text" name="lastname" value="" required>';
        $form .= '<input type="submit" value="Submit">';
        $form .= '</form> ';
        $this->html .= 'homepage';
        $this->html .= $form;
    }

    //displays user input
  public function post()
  {
      $firstName = $_REQUEST['firstname'];
      $lastName = $_REQUEST['lastname'];

      $this->html.= '<h3>'.$firstName.'<h3>';
      $this->html.='<br>';
      $this->html.= '<h3>'.$lastName.'<h3>';

  }
}
class fileHandling
{
    //check and return the file type
    static function checkType($file)
    {
        $fileType = pathinfo($file,PATHINFO_EXTENSION);
        return $fileType;
    }
}
class stringFunctions
{
    //prints out whatever string is passed into it
    static function printOut($string)
    {
        echo $string;
    }
}