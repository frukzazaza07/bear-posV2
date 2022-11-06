<?php
class Route
{
    /**
    * @var array $_listUri List of URI's to match against
    */
    private static $_listUri = array();

    /**
    * @var array $_listCall List of closures to call 
    */
    private static $_listCall = array();

    /**
    * @var string $_trim Class-wide items to clean
    */
    private static $_trim = '/\^$';

    /**
    * add - Adds a URI and Function to the two lists
    *
    * @param string $uri A path such as about/system
    * @param object $function An anonymous function
    */
    static public function add($uri, $function)
    {
        $uri = trim($uri, self::$_trim);
        self::$_listUri[] = $uri;
        self::$_listCall[] = $function;
    }

    /**
    * submit - Looks for a match for the URI and runs the related function
    */
    static public function submit()
    {   
        $uri = isset($_REQUEST['uri']) ? $_REQUEST['uri'] : '/';
        $uri = trim($uri, self::$_trim);

        $replacementValues = array();

        /**
        * List through the stored URI's
        */
        foreach (self::$_listUri as $listKey => $listUri)
        {
            /**
            * See if there is a match
            */
            if (preg_match("#^$listUri$#", $uri))
            {
                /**
                * Replace the values
                */
                $realUri = explode('/', $uri);
                $fakeUri = explode('/', $listUri);

                /**
                * Gather the .+ values with the real values in the URI
                */
                foreach ($fakeUri as $key => $value) 
                {
                    if ($value == '.+') 
                    {
                        $replacementValues[] = $realUri[$key];
                    }
                }

                /**
                * Pass an array for arguments
                */
                call_user_func_array(self::$_listCall[$listKey], $replacementValues);
            }

        }

    }

}