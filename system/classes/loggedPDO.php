<?php
/**
* Extends PDO and logs all queries that are executed and how long
* they take, including queries issued via prepared statements
*/
class LoggedPDO extends PDO
{
    public static $log = array();
    
    public function __construct($dsn, $username = null, $password = null) {
        parent::__construct($dsn, $username, $password);
    }
    
    public function query($query) {
        $start = microtime(true);
        $result = parent::query($query);
        $time = microtime(true) - $start;
        LoggedPDO::$log[] = array('query' => $query,
                                  'time' => round($time * 1000, 3),
                                  'errors'=>$result->errorInfo(),
                                  'vars'=>''
                                  );
        return $result;
    }

    /**
     * @return LoggedPDOStatement
     */
    public function prepare($query) {
        return new LoggedPDOStatement(parent::prepare($query));
    }
    
    public static function printLog() {
        $totalTime = 0;
        $final = array();
        //echo '<table border=1><tr><th>Query</th><th>Time (ms)</th></tr>';
        foreach(self::$log as $entry) {
            $totalTime += $entry['time'];
            $final[] = array('entry'=>$entry['query'], 'time'=>$entry['time'], 'vars'=>$entry['vars'], 'errors'=>$entry['errors']);
        }
        $final['meta'] = array(
        	'count'=>count(self::$log),
        	'time'=>$totalTime
        );
        return $final;
    }
}

class LoggedPDOStatement {
    /**
     * The PDOStatement we decorate
     */
    private $statement;

    public function __construct(PDOStatement $statement) {
        $this->statement = $statement;
    }

    /**
    * When execute is called record the time it takes and
    * then log the query
    * @return PDO result set
    */
    public function execute($vars = null) {
        $start = microtime(true);
        $result = $this->statement->execute($vars);
        $time = microtime(true) - $start;
        LoggedPDO::$log[] = array('query' => '[PS] ' . $this->statement->queryString,
                                  'time' => round($time * 1000, 3),
                                  'vars'=>$vars,
                                  'errors'=>$this->statement->errorInfo());
        return $result;
    }
    /**
    * Other than execute pass all other calls to the PDOStatement object
    * @param string $function_name
    * @param array $parameters arguments
    */
    public function __call($function_name, $parameters) {
        return call_user_func_array(array($this->statement, $function_name), $parameters);
    }
}