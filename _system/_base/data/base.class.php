<?php
/*====================================================*
*	DoFound PHP Framework.	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
abstract class base_Dbase
{
	private $_rdb;		//read db
    private $_wdb;		//write db
	protected $_table_prefix = '';
	public $_table;

	/**
     * __construct 
     * @param $db_read_key string -> read_key
	 * @param $db_write_key string -> write_key
     * @return array or false
     */
	public function __construct( $db_read_key,$db_write_key=null ) {
        $this->_rdb = DF::getDb( $db_read_key );
        $this->_wdb = empty($db_write_key) ? $this->_rdb : DF::getDb( $db_write_key );
	}
	/**
     * 根据ID获取记录
     * @param int $id
     * @return array or false
     */
	public function fetch_by_id( $id ) {
        if( is_numeric( $id ) ) $id = (int)$id;
        else $id = addslashes($id);	   
		if ( empty($id)) return false;
		$sql = "SELECT * FROM `{$this->_table}` WHERE `id` = '{$id}' LIMIT 1";
		return $this->_rdb->fetch_one( $sql );
	}

	/**
     * 根据条件获取一条记录
     * @param string $condition
     * @return array or false
     */
	public function fetch_one( $condition , $order = '' ) {
		if( empty( $condition ) ) return false;
		$sql = "SELECT * FROM `{$this->_table}` WHERE {$condition}";
		$sql .= empty($order) ? '' : " ORDER BY {$order}";
		return $this->_rdb->fetch_one( $sql );
	}

    /**
     * 根据条件获取所有记录
     * @param string $condition $order $limit
     * @return array or false
     */
	public function fetch_more( $condition, $order = '', $limit = '' ) {
        if( empty( $condition ) ) return false;
        $sql = "SELECT * FROM `{$this->_table}` WHERE {$condition}";
		$sql .= empty($order) ? " ORDER BY id DESC" : " ORDER BY {$order}";
		if( $limit != '' ) {
			$sql .= " LIMIT {$limit}";
		}
        return $this->_rdb->fetch_more( $sql );
    }

    /**
     * 根据条件获取一条记录
     * @param string $condition, $field
     * @return array or false
     */
    public function fetch_more_field( $condition, $field, $order = '', $limit = '' ) {
        if( empty( $condition ) || empty( $field ) ) return false;
        $sql = "SELECT {$field} FROM `{$this->_table}` WHERE {$condition}";
		$sql .= empty($order) ? " ORDER BY id DESC" : " ORDER BY {$order}";
		if( $limit != '' ) {
			$sql .= " LIMIT {$limit}";
		}
        return $this->_rdb->fetch_more( $sql );
    }

    /**
     * 根据ID更新记录
     * @param int $id
     * @param array $data
     * @return int or false
     */
    public function update_by_id( $id, $data ) {
        if( !is_numeric( $id ) || !is_array( $data ) ) return false;
        $condition = "`id` = '{$id}'";
        return $this->_wdb->update( $this->_table, $data, $condition );
    }

    /**
     * 根据ID更新记录
     * @param string $condition
     * @param array $data
     * @return int or false
     */
	public function update_by_condition( $data, $condition ) {
        if( empty( $condition ) || !is_array( $data ) ) return false;
        return $this->_wdb->update( $this->_table, $data, $condition );
    }

    /**
     * 根据ID删除记录
     * @param int $id
     * @return true or false
     */
    public function delete_by_id( $id ) {
        if( !is_numeric( $id ) ) $id = (int)$id;
        else $id = addslashes($id);
        $condition = "`id` = '{$id}'";
        return $this->_wdb->del( $this->_table, $condition );
	}

    /**
     * 根据条件删除记录
     * @param string $condition
     * @return true or false
     */
    public function delete_by_condition( $condition ) {
        if( empty( $condition ) ) return false;
        return $this->_wdb->del( $this->_table, $condition );
    }

    /**
     * 插入记录
     * @param array $data
     * @return int 插入行的ID or false
     */
    public function insert_row( $data ) {
        if( !is_array( $data ) ) return false;
        return $this->_wdb->insert( $this->_table, $data );
    }

    /**
     * 替换记录
     * @param array $data
     * @return int 插入行的ID or false
     */
    public function replace_row( $data ) {
        if( !is_array( $data ) ) return false;
        return $this->_wdb->replace( $this->_table, $data );
    }

    /**
     * 根据条件获取记录数
     * @param string $condition
     * @return count
     */
    public function fetch_count( $condition ) {
        if( empty( $condition ) ) return false;
        $sql = "SELECT COUNT(*) as `count` FROM {$this->_table} WHERE {$condition}";
        return $this->_rdb->fetch_one( $sql );
    }

    /**
     * 更新数值。
     * @param array $field
     * @param string $condition
     * @param boolen $model
     * @return true or false
     */
    public function update_nums( $field,$condition ) {
        if (empty( $condition ) || !is_array($field)) return false;
        $tmp = array();
        foreach ($field as $key=>$pv) {
            if (is_numeric($pv)) {
                $tmp[] = $pv>0 ? "`$key`=`$key`+{$pv}":"`$key`=`$key`{$pv}";
            }    
        }
        $tmp = join(',',$tmp);
        $sql = "UPDATE `{$this->_table}` SET {$tmp} WHERE {$condition}";
        return $this->_wdb->exec( $sql );
    }

    /**
     * 优化数据
     * @author 
     */
    public function optimize( $table ) {
        return $this->_rdb->optimize( $table );
    }

}