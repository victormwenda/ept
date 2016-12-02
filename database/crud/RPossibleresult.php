<?php

	namespace database\crud;
 
	use database\core\mysql\DatabaseUtils;
    use database\core\mysql\InvalidColumnValueMatchException;
    use database\core\mysql\NullabilityException;

	/**
	* THIS SOURCE CODE WAS AUTOMATICALLY GENERATED ON Fri 05:26:40  02/12/2016
	* 
	*
	* DATABASE CRUD GENERATOR IS AN OPEN SOURCE PROJECT. TO IMPROVE ON THIS PROJECT BY
	* ADDING MODULES, FIXING BUGS e.t.c GET THE SOURCE CODE FROM GIT (https://github.com/marviktintor/dbcrudgen/)
	* 
	* DATABASE CRUD GENERATOR INFO:
	* 
	* DEVELOPER : VICTOR MWENDA
	* VERSION : DEVELOPER PREVIEW 0.1
	* SUPPORTED LANGUAGES : PHP
	* DEVELOPER EMAIL : vmwenda.vm@gmail.com
	* 
	*/


/**
*  
* RPossibleresult
*  
* Low level class for manipulating the data in the table r_possibleresult
*
* This source code is auto-generated
*
* @author Victor Mwenda
* Email : vmwenda.vm@gmail.com
* Phone : +254(0)718034449
*/
class RPossibleresult {

	private $databaseUtils;
	private $action;
	private $client;
	
	public function __construct($databaseUtils, $action = "", $client = "") {
		$this->init($databaseUtils);
	}
	
	//Initializes
	public function init($databaseUtils) {
		
		//Init
		$this->databaseUtils = $databaseUtils;
		
	}
	
		
	/**
	* private class variable $_id
	*/
	private $_id;
	
	/**
	* returns the value of $id
	*
	* @return object(int|string) id
	*/
	public function _getId() {
		return $this->_id;
	}
	
	/**
	* sets the value of $_id
	*
	* @param id
	*/
	public function _setId($id) {
		$this->_id = $id;
	}
	/**
	* sets the value of $_id
	*
	* @param id
	* @return object ( this class)
	*/
	public function setId($id) {
		$this->_setId($id);
		return $this;
	}
	
	
	/**
	* private class variable $_schemeId
	*/
	private $_schemeId;
	
	/**
	* returns the value of $schemeId
	*
	* @return object(int|string) schemeId
	*/
	public function _getSchemeId() {
		return $this->_schemeId;
	}
	
	/**
	* sets the value of $_schemeId
	*
	* @param schemeId
	*/
	public function _setSchemeId($schemeId) {
		$this->_schemeId = $schemeId;
	}
	/**
	* sets the value of $_schemeId
	*
	* @param schemeId
	* @return object ( this class)
	*/
	public function setSchemeId($schemeId) {
		$this->_setSchemeId($schemeId);
		return $this;
	}
	
	
	/**
	* private class variable $_schemeSubGroup
	*/
	private $_schemeSubGroup;
	
	/**
	* returns the value of $schemeSubGroup
	*
	* @return object(int|string) schemeSubGroup
	*/
	public function _getSchemeSubGroup() {
		return $this->_schemeSubGroup;
	}
	
	/**
	* sets the value of $_schemeSubGroup
	*
	* @param schemeSubGroup
	*/
	public function _setSchemeSubGroup($schemeSubGroup) {
		$this->_schemeSubGroup = $schemeSubGroup;
	}
	/**
	* sets the value of $_schemeSubGroup
	*
	* @param schemeSubGroup
	* @return object ( this class)
	*/
	public function setSchemeSubGroup($schemeSubGroup) {
		$this->_setSchemeSubGroup($schemeSubGroup);
		return $this;
	}
	
	
	/**
	* private class variable $_response
	*/
	private $_response;
	
	/**
	* returns the value of $response
	*
	* @return object(int|string) response
	*/
	public function _getResponse() {
		return $this->_response;
	}
	
	/**
	* sets the value of $_response
	*
	* @param response
	*/
	public function _setResponse($response) {
		$this->_response = $response;
	}
	/**
	* sets the value of $_response
	*
	* @param response
	* @return object ( this class)
	*/
	public function setResponse($response) {
		$this->_setResponse($response);
		return $this;
	}
	

		
		
	/**
     * Performs a database query and returns the value of id 
     * based on the value of $id,$scheme_id,$scheme_sub_group,$response passed to the function
     *
     * @param $id,$scheme_id,$scheme_sub_group,$response
     * @return object (id)| null
     */
	public function getId($id,$scheme_id,$scheme_sub_group,$response) {
		$columns = array ('id','scheme_id','scheme_sub_group','response');
		$records = array ($id,$scheme_id,$scheme_sub_group,$response);
		$id_ = $this->query_from_r_possibleresult ( $columns, $records );
		return sizeof($id_)>0 ? $id_ [0] ['id'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of scheme_id 
     * based on the value of $id passed to the function
     *
     * @param $id
     * @return object (scheme_id)| null
     */
	public function getSchemeId($id) {
		$columns = array ('id');
		$records = array ($id);
		$scheme_id_ = $this->query_from_r_possibleresult ( $columns, $records );
		return sizeof($scheme_id_)>0 ? $scheme_id_ [0] ['scheme_id'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of scheme_sub_group 
     * based on the value of $id passed to the function
     *
     * @param $id
     * @return object (scheme_sub_group)| null
     */
	public function getSchemeSubGroup($id) {
		$columns = array ('id');
		$records = array ($id);
		$scheme_sub_group_ = $this->query_from_r_possibleresult ( $columns, $records );
		return sizeof($scheme_sub_group_)>0 ? $scheme_sub_group_ [0] ['scheme_sub_group'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of response 
     * based on the value of $id passed to the function
     *
     * @param $id
     * @return object (response)| null
     */
	public function getResponse($id) {
		$columns = array ('id');
		$records = array ($id);
		$response_ = $this->query_from_r_possibleresult ( $columns, $records );
		return sizeof($response_)>0 ? $response_ [0] ['response'] : null;
	}
	

	
	/**
	* Inserts data into the table[r_possibleresult] in the order below
	* array ('id','scheme_id','scheme_sub_group','response')
	* is mappped into 
	* array ($id,$scheme_id,$scheme_sub_group,$response)
	* @return int 1 if data was inserted,0 otherwise
	* if redundancy check is true, it inserts if the record if it never existed else.
	* if the record exists, it returns the number of times the record exists on the relation
	*/
	public function insert_prepared_records($id,$scheme_id,$scheme_sub_group,$response,$redundancy_check= false, $printSQL = false) {
		$columns = array('id','scheme_id','scheme_sub_group','response');
		$records = array($id,$scheme_id,$scheme_sub_group,$response);
		return $this->insert_records_to_r_possibleresult ( $columns, $records,$redundancy_check, $printSQL );
	}

	
	/**
	* Returns the table name. This is the owner of these crud functions.
	* The various crud functions directly affect this table
	* @return string table name -> 'r_possibleresult' 
	*/
	public static function get_table() {
		return 'r_possibleresult';
	}
	
	/**
	* This action represents the intended database transaction
	*
	* @return string the set action.
	*/
	private function get_action() {
		return $this->action;
	}
	
	/**
	* Returns the client doing transactions
	*
	* @return string the client
	*/
	private function get_client() {
		return $this->client;
	}
	
	/**
     * Used  to calculate the number of times a record exists in the table r_possibleresult
     * It returns the number of times a record exists exists in the table r_possibleresult
     * @param array $columns
     * @param array $records
     * @param bool $printSQL
     * @return mixed
     */
	public function is_exists(Array $columns, Array $records, $printSQL = false) {
		return $this->get_database_utils ()->is_exists ( $this->get_table (), $columns, $records, $printSQL );
	}
	
	/**
     * Inserts data into the table r_possibleresult
     * if redundancy check is true, it inserts if the record if it never existed else.
     * if the record exists, it returns the number of times the record exists on the relation
     *
     * @param array $columns
     * @param array $records
     * @param bool $redundancy_check
     * @param bool $printSQL
     * @return mixed
     */
	public function insert_records_to_r_possibleresult(Array $columns, Array $records,$redundancy_check = false, $printSQL = false) {
		return $this->insert_records ( $this->get_table (), $columns, $records,$redundancy_check, $printSQL );
	}
	/**
         * Inserts records in a relation
         * The records are inserted in the relation columns in the order they are arranged in the array
         *
         * @param $records
         * @param bool $printSQL
         * @return bool|mysqli_result
         * @throws NullabilityException
         */
        public function insert_raw($records, $printSQL = false)
        {
            return $this->get_database_utils()->insert_raw_records($this->get_table(), $records, $printSQL);
        }
	/**
	 * Deletes all the records that meets the passed criteria from the table r_possibleresult
	 * @param array $columns
	 * @param array $records
	 * @param bool $printSQL
	 * @return number of deleted rows
	 */
	public function delete_record_from_r_possibleresult(Array $columns, Array $records, $printSQL = false) {
		return $this->delete_record ( $this->get_table (), $columns, $records, $printSQL );
	}
	
	/**
	 * Updates all the records that meets the passed criteria from the table r_possibleresult
	 * 
	 * @param array $columns
	 * @param array $records
	 * @param array $where_columns
	 * @param array $where_records
	 * @param bool $printSQL
	 * @return number of updated rows
	 */
	public function update_record_in_r_possibleresult(Array $columns, Array $records, Array $where_columns, Array $where_records, $printSQL = false) {
		return $this->update_record ( $this->get_table (), $columns, $records, $where_columns, $where_records, $printSQL );
	}
	
	/**
	 * Gets an Associative array of the records in the table 'r_possibleresult' that meets the passed criteria
	 *
	 * @param $distinct
	 * @param array $columns
	 * @param array $records
	 * @param string $extraSQL
	 * @param bool $printSQL
	 * @return array|mixed associative
	 */
	public function fetch_assoc_in_r_possibleresult($distinct, Array $columns, Array $records, $extraSQL="", $printSQL = false) {
		return $this->fetch_assoc ( $distinct, $this->get_table (),$columns, $records, $extraSQL , $printSQL );
	}
	
	/**
	 * Gets an Associative array of the records in the table r_possibleresult that meets the passed criteria
	 *
	 * @param array $columns
	 * @param array $records
	 * @param string $extraSQL
	 * @param bool $printSQL
	 * @return array|mixed associative
	 */
	public function query_from_r_possibleresult(Array $columns, Array $records,$extraSQL="",  $printSQL = false) {
		return $this->query ( $this->get_table (), $columns, $records,$extraSQL,$printSQL );
	}
	
	/**
	 * Gets an Associative array of the records in the table r_possibleresult that meets the passed distinct criteria
	 * 
	 * @param array $columns
	 * @param array $records
	 * @param string $extraSQL
	 * @param bool $printSQL
	 * @return array|mixed associative
	 */
	public function query_distinct_from_r_possibleresult(Array $columns, Array $records,$extraSQL="",  $printSQL = false) {
		return $this->query_distinct ( $this->get_table (), $columns, $records,$extraSQL,$printSQL );
	}
	
	/**
	 * Performs a search in the table r_possibleresult that meets the passed criteria
	 * 
	 * @param array $columns
	 * @param array $records
	 * @param string $extraSQL
	 * @param bool $printSQL
	 * @return array|mixed associative
	 */
	public function search_in_r_possibleresult(Array $columns, Array $records,$extraSQL="", $printSQL = false) {
		return $this->search ( $this->get_table (), $columns, $records,$extraSQL, $printSQL );
	}
	
	/**
	* Get Database Utils
	*  
	* @return DatabaseUtils $this->databaseUtils
	*/
	public function get_database_utils() {
		return $this->databaseUtils;
	}
	
	
	/**
	 * Deletes all the records that meets the passed criteria from the table [r_possibleresult]
	 *
	 * @param $table
	 * @param array $columns
	 * @param array $records
	 * @param bool $printSQL
	 * @return bool|int|\mysqli_result number of deleted rows
	* @throws InvalidColumnValueMatchException
    * @throws NullabilityException
	 */
	private function delete_record($table, Array $columns, Array $records, $printSQL = false) {
		return $this->get_database_utils ()->delete_record ( $table, $columns, $records, $printSQL );
	}
	
	
	/**
     * Inserts data into the table r_possibleresult
     *
     * if redundancy check is true, it inserts if the record if it never existed else.
     * if the record exists, it returns the number of times the record exists on the relation
     * @param $table
     * @param array $columns
     * @param array $records
     * @param bool $redundancy_check
     * @param bool $printSQL
     * @return bool|mixed|\mysqli_result the number of times the record exists
   * @throws NullabilityException
     */
	private function insert_records($table, Array $columns, Array $records,$redundancy_check = false, $printSQL = false) {
		if($redundancy_check){
			if($this->is_exists($columns, $records) == 0){
				return $this->get_database_utils ()->insert_records ( $table, $columns, $records, $printSQL );
			} else return $this->is_exists($columns, $records);
		}else{
			return $this->get_database_utils ()->insert_records ( $table, $columns, $records, $printSQL );
		}
		
	}
	
	/**
     * Updates all the records that meets the passed criteria from the table r_possibleresult
     * @param $table
     * @param array $columns
     * @param array $records
     * @param array $where_columns
     * @param array $where_records
     * @param bool $printSQL
     * @return bool|\mysqli_result number of updated rows
   * @throws NullabilityException
     */
	private function update_record($table, Array $columns, Array $records, Array $where_columns, Array $where_records, $printSQL = false) {
		return $this->get_database_utils ()->update_record ( $table, $columns, $records, $where_columns, $where_records, $printSQL );
	}
	
	/**
     * Gets an Associative array of the records in the table r_possibleresult that meets the passed criteria
     * associative array of the records that are found after performing the query
     * @param $distinct
     * @param $table
     * @param array $columns
     * @param array $records
     * @param string $extraSQL
     * @param bool $printSQL
     * @return array|null
    * @throws InvalidColumnValueMatchException
   * @throws NullabilityException
     */
	private function fetch_assoc($distinct, $table, Array $columns, Array $records, $extraSQL="", $printSQL = false) {
		return $this->get_database_utils ()->fetch_assoc ( $distinct, $table, $columns, $records,$extraSQL, $printSQL );
	}
	
	 /**
     * Gets an Associative array of the records in the table r_possibleresult  that meets the passed criteria
     *
     * @param $table
     * @param array $columns
     * @param array $records
     * @param string $extraSQL
     * @param bool $printSQL
     * @return array
     */
	private function query($table, Array $columns, Array $records,$extraSQL="",$printSQL = false) {
		return $this->get_database_utils ()->query ( $table, $columns, $records,$extraSQL, $printSQL );
	}
	/**
     * Gets an Associative array of the records in the table r_possibleresult that meets the distinct passed criteria
     * @param $table
     * @param array $columns
     * @param array $records
     * @param string $extraSQL
     * @param bool $printSQL
     * @return array
     */
	private function query_distinct($table, Array $columns, Array $records,$extraSQL="",$printSQL = false) {
		return $this->get_database_utils ()->query_distinct ( $table, $columns, $records,$extraSQL, $printSQL );
	}
	
	 /**
     * Performs a search and returns an associative array of the records in the table r_possibleresult  that meets the passed criteria
     * 
     * @param $table
     * @param array $columns
     * @param array $records
     * @param string $extraSQL
     * @param bool $printSQL
     * @return array|null
    * @throws InvalidColumnValueMatchException
   * @throws NullabilityException
     */
	private function search($table, Array $columns, Array $records,$extraSQL="", $printSQL = false) {
		return $this->get_database_utils ()->search ( $table, $columns, $records, $extraSQL, $printSQL );
	}
}
?>
