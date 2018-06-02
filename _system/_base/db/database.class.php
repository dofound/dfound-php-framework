<?php
/*====================================================*
*	DoFound PHP Framework.	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
class base_Ldatabase
{
	private static $rkey = array();
	private $_wdb;
	private $_rdb;
	protected $_debug = 'xiaojh';
	private static $_oper = array();
	private static $queryDate = array('+ Database Debug +'=>'Function:time(sec msec) \t execute sql record:');

	public function __construct( $config ) {
		if ( !in_array($config['uniqid'],self::$rkey) ) {
			try {
				$temp = new PDO( $config['dns'], $config['user'],$config['pass'] );
			} catch (PDOException $e) {
				throw new myThrow( 'Could not connect to the database...' );
			}
			self::$rkey[] = $config['uniqid'];
			/**
			* Here you can set a time, used to write operation
			* @value = DF::config($pkey);
			* @author xiaojh@
			*
			*/
			$wconfig=null;
			
			if (null!=$wconfig) {
				try {
					$wtemp = new PDO( $wconfig['dns'], $wconfig['user'],$wconfig['pass'] );
				} catch (PDOException $e) {
					throw new myThrow( 'Could not connect to the writable database...' );
				}
				self::$rkey[] = $wconfig['uniqid'];
				$this->setDb($config['uniqid'],$temp);
				$this->setDb($wconfig['uniqid'],$wtemp);
			} else {
				self::$rkey[] = $config['uniqid'];
				$this->setDb($config['uniqid'],$temp);
			}
		}
		$this->getDb($config['uniqid'],$wconfig['uniqid']);
	}
	private function setDb($key,&$obj) {
		if (empty(self::$_oper[$key])) {
			self::$_oper[$key] = $obj; 
		}
		return;
	}
	private function getDb($rkey,$wkey) {
		$wkey = empty($wkey) ? $rkey : $wkey;
		$this->_wdb = self::$_oper[$wkey];
		$this->_rdb = self::$_oper[$rkey];
	}
	/**
	 * update datas
	 *
	 * @param String $table
	 * @param Array $data
	 * @param String $condition
	 * @return Intger 
	 */
	public function update( $table, $data, $condition ) {
		$set_info = array();
		foreach( $data as $key => $value ) {
			$set_info[] = '`' . $key . '` = ' . "'$value'";
		}
		$set_str = join(',', $set_info);
		$condit = !empty($condition) ? ' WHERE '.$condition : '';
		$sql = "UPDATE `{$table}` SET {$set_str} {$condit}";
		$tmp = $this->_wdb->exec( $sql );
		$this->_debug( 'update',$sql );
		return $tmp;
	}

	/**
	 * del data
	 *
	 * @param String $table
	 * @param String $condition
	 * @return Intger 
	 */
	public function del( $table, $condition ,$limit=1) {
        if ($limit) {
            $sql = "DELETE FROM `{$table}` WHERE {$condition} limit 1";
        } else {
            $sql = "DELETE FROM `{$table}` WHERE {$condition}";
        }
		if (!$tmp = $this->_wdb->exec( $sql )) {
            $this->showError();
		}
		$this->_debug( 'del',$sql );
		return $tmp;
	}

	/**
	 * optimize
	 *
	 * @param String $table
	 * @return Intger 
	 */
	public function optimize( $table ) {
		$sql = "OPTIMIZE TABLE `{$table}`";
		$tmp = $this->_wdb->exec( $sql );
		$this->_debug( 'optimize',$sql );
		return $tmp;
	}

	/**
	 * truncate
	 *
	 * @param String $table
	 * @return Intger 
	 */
	public function truncate( $table ) {
        /*$sql = "TRUNCATE TABLE `{$table}`";
		$tmp = $this->_wdb->exec( $sql );
		$this->_debug( 'truncate',$sql );
		return $tmp;*/
	}

	/**
	 * add datas
	 *
	 * @param String $table
	 * @param Array $fields
	 * @return Intger 
	 */
	public function insert( $table, $fields ) {
  		foreach( $fields as $key => $value ) {
   	  		$field_names[] = $key;
  	  		$field_values[] = addslashes( $value );	//qoute magic
  		}	  	
		$sql = "INSERT INTO `{$table}` (`".implode('`,`', $field_names)."`) VALUES "."('".implode("','", $field_values)."')";
		if(!$this->_wdb->exec( $sql ) ) {
            $this->showError();
        }
		$this->_debug( 'insert',$sql );
		return $this->_wdb->lastInsertId();
	}
    
	/**
	 * replace datas
	 *
	 * @param String $table
	 * @param Array $fields
	 * @return Intger 
	 */
	public function replace( $table, $fields ) {
  		foreach( $fields as $key => $value ) {
   	  		$field_names[] = $key;
  	  		$field_values[] = addslashes( $value );	//qoute magic
  		}	  	
		$sql = "REPLACE INTO `{$table}`  (`".implode('`,`', $field_names)."`) VALUES "."('".implode("','", $field_values)."')";
		if (!$tmp = $this->_wdb->exec( $sql ) ) {
            $this->showError();
		}
		$this->_debug( 'replace',$sql );
		return $tmp;
	}

	/**
     * bug
     */
	private function _debug( $function,$sql ) {
        if (_DEBUG) {
            $ctime = explode(' ', microtime());
            self::$queryDate[]=$function.":(".date('H:i:s',$ctime[1])." {$ctime[0]})\t".$sql;
        }
	}
	/**
     * get bug
     */
	public static function getBug($other='') {
		if (_DEBUG) {
			echo '<div style="background:#ffffff;padding:15px;"><pre>';
            echo '<h3>Variable value :</h3>';
            is_array($other) ? print_r($other) : @array_push(self::$queryDate,$other);
            echo '<hr /><h3>Database value :</h3>';
			print_r(self::$queryDate);			
			echo "</pre></div>";
            self::$queryDate = null;
		}
	}
	/**
	 * get one row
	 *
	 * @param String $sql
	 * @param boolen $is_num 
	 * @return array
	 */
	public function fetch_one( $sql ,$is_num = false ) {
        $stmt = $this->_rdb->query( $sql );
		if (empty($stmt)) {
            $this->showError();
			return false;
		} else {
			$tmp = $stmt->fetch( $is_num ? PDO::FETCH_NUM : PDO::FETCH_ASSOC );
			$this->_debug( 'fetch_one',$sql );
			return $tmp;
		}
	}

	/**
	 * get all
	 *
	 * @param String $sql
	 * @param boolen $is_num 
	 * @return array
	 */
	public function fetch_more( $sql ,$is_num = false ) {
        $stmt = $this->_rdb->query( $sql );
		if (empty($stmt)) {
            $this->showError();
			return false;
		} else {
			$tmp = $stmt->fetchAll( $is_num ? PDO::FETCH_NUM : PDO::FETCH_ASSOC );
			$this->_debug( 'fetch_more',$sql );
			return $tmp;
		}
	}
    /**
     * show errors
     * */
    protected function showError() {
        throw new myThrow(join(',',$this->_rdb->errorInfo()));
    }      	

}
