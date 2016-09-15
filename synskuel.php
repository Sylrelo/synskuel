<?php
/*
    Sylvain LOPEZ
    2016
*/
class Synskuel {

    private static $db;
    private static $errors;

    public static function connect($host, $database, $username, $password, $options = null)
    {
        try
        {
            self::$db = new PDO('mysql:host='.$host.';dbname='.$database.';charset=utf8mb4', $username, $password, $options);
        }
        catch(PDOException $e)
        {
          die("Error! : " . $e->getMessage());
        }
    }

    public static function errors()
    {
        if(sizeof(self::$errors) >= 1)
        {
            echo "<br> Errors : <br>";
            print_r(self::$errors);
        }
    }

    public static function errorCode($code)
    {
        switch($code)
        {
            case "HY093";
            return "execute() : no parameters were bound";
            break;
        }
    }

    public static function query($query, $data = null, $success = null, $error = null)
    {
        $action = explode(" ", $query)[0];
        $db = self::$db;

        if($action == "INSERT")
        {
            $prepared = $db->prepare($query);
            foreach($data as $key => $value)
            {
               $prepared->bindParam($key+1, $data[$key]);
            }
            if($prepared->execute())
            {
                self::Callback($success);
            }
            else
            {
               self::Error($error, [$query, $prepared->errorInfo()]);
            }
        }
        else if(in_array($action, ['DELETE', 'UPDATE']))
        {
            $prepared = $db->prepare($query);
            if($prepared->execute($data) && $prepared->rowCount() >= 1)
            {
                self::Callback($success);
            }
            else
            {
                $_err = array('Row affected : ' . $prepared->rowCount(), $prepared->errorInfo());
                self::Error($error, [$query, $_err]);
            }
        }
        else if($action == "SELECT")
        {
            $prepared = $db->prepare($query);
            if($prepared->execute($data))
            {
                self::Callback($success);
                if(strpos($query, 'count(') !== false)
                {
                    $count = $prepared->fetch()[0];
                    self::Success($success, $count);
                    return $count;
                }
            }
            else
            {
                self::Error($error, [$query, $prepared->errorInfo()]);
            }
        }
        else
        {
            die("Invalid statement");
        }
    }

    public static function exists($query, $data = null, $success = null, $error = null)
    {
        if(self::query($query, $data) >= 1)
        {
            self::Success($success);
        }
        else
        {
            self::Error($error);
        }
    }

    public static function Error($callback, $data = null)
    {
        self::$errors[] = $data;
        self::writelog($data);
        self::Callback($callback, $data);
    }

    public static function Success($callback, $data = null)
    {
        self::Callback($callback, $data);
    }

    public static function Callback($callback, $data = null)
    {
        if(is_callable($callback) && ($callback instanceof Closure))
        {
            call_user_func($callback, $data);
        }
    }

////    < LOG TO FILE #######################################################################
    public static function implode_r($sp, $array)
    {
        $output = "";
        foreach ($array as $values)
        {
            if (is_array ($values))
            {
                $output .= self::implode_r ($sp, $values);
            }
            else
            {
                $output .= $sp . $values;
            }
        }

        return $output;
    }

    private static $log_file = 'synskuel.log';
    private static $logs = false;
    public static function logs($file = null)
    {
        if($file != null)
        {
            self::$log_file = $file;
        }
        self::$logs = true;
    }
    public static function writelog($data)
    {
        if(self::$logs && $data)
        {
            $fp = '';
            if(!is_resource($fp))
            {
                $lfile = self::$log_file;
                $fp = fopen($lfile, 'a');
            }
            $time = @date('[d/M/y:H:i:s]');
            fwrite($fp, "$time " . self::implode_r(" | ",$data) . PHP_EOL);
        }
    }
////    /> LOG TO FILE #######################################################################
}

?>
