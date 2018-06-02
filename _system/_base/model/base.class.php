<?php
/*====================================================*
*	DoFound PHP Framework.	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
abstract class base_Mbase
{
    protected $_succ	= 1;
    protected $_fail	= 0;
	protected $_db;
	protected $_wdb;

	public function __construct( &$read_obj ) {
		/*@eval("\$obj = $obj;");
		if( !is_object($obj) ) throw new myThrow( __FILE__.' object is no '.$obj ); */
		$this->_db =& $read_obj;
		$this->set_table( $read_obj->_table );
	}

    /**
     * 设置TABLE
     * @author 
     */
	protected function set_table( $table ) {
		$this->_db->_table = $table;
	}

    /**
     * 优化数据表
     * @author 
     */
    public function set_optimize() {
        return $this->_db->optimize( $this->_db->_table );
    }

    /**
     * 插入数据
     * @author 
     */
    public function add_record( $data ) {
        return $this->_db->insert_row( $data );
    }
    
    /**
     * 编辑数据 来自ID
     * @author 
     */
    public function edit_record_id( $id,$data )	{
        return $this->_db->update_by_id( $id,$data );
 	}
    
    /**
     * 编辑数据 来自条件
     * @author 
     */
    public function edit_record_condition( $data,$condition ) {
        return $this->_db->update_by_condition( $data,$condition );
    }

    /**
     * 重置数据 replace
     * @author 
     */    
	public function replace_record( $data )	{
		return $this->_db->replace_row( $data );
	}

    /**
     * 删除数据 id
     * @author 
     */
    public function del_record_id( $id ) {
        return $this->_db->delete_by_id ( $id );
	}

    /**
     * 删除数据 condition
     * @author 
     */
    public function del_record_condition( $condition ) {
        return $this->_db->delete_by_condition( $condition );
	}

    /**
     * 显示 ID 数据。
     * @author 
     */
    public function get_record_id( $id ) {
        $temp = $this->_db->fetch_by_id( $id );
        return $this->_return_data( empty($temp) ? false : true,$temp );
    }

    /**
     * 显示某条数据。
     * @author 
     */
    public function get_record_one( $condition,$order = '' ) {
        $temp = $this->_db->fetch_one( $condition,$order = '' );
         return $this->_return_data( empty($temp) ? false : true,$temp );
    }

    /**
     * 显示些字段的数据。
     * @author 
     */
    public function get_record_field( $condition, $field='*', $order = '', $limit = '' ) {
		$temp = $this->_db->fetch_more_field( $condition, $field, $order, $limit );
         return $this->_return_data( empty($temp) ? false : true,$temp );
	}

    /**
     * 显示更多数据。
     * @author 
     */
    public function get_record_all( $condition,$order = '',$limit = '' ) {
        $temp = $this->_db->fetch_more( $condition,$order,$limit );
         return $this->_return_data( empty($temp) ? false : true,$temp );
    }


    /**
     * 根据条件获取记录数
     * @param string $condition
     * @return array
     */
    public function get_count( $condition ) {
        $temp = $this->_db->fetch_count( $condition );
         return $this->_return_data( empty($temp) ? false : true,$temp );
    }

    /**
     * 更新数值。
     * @param array $field
     * @param string $condition
     * @param boolen $model
     * @return true or false
     */	
	public function update_nums( $field,$condition ) {
		return $this->_db->update_nums( $field,$condition,$model );
	}
	
	/**
     * 将要输出的数据格式成需要的形式返回
     * @param boolean $is_succ
     * @param mixed   $info
     * @return array
     */
    protected function _return_data( $is_succ, $info = null ) {
        $res = array();
        $res['status']	= $is_succ ? $this->_succ : $this->_fail;
        $res['info']	= $info;
        return $res;
    }

}