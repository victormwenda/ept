<?php

	namespace database\crud;
 
	use database\core\mysql\DatabaseUtils;
    use database\core\mysql\InvalidColumnValueMatchException;
    use database\core\mysql\NullabilityException;

	/**
	* THIS SOURCE CODE WAS AUTOMATICALLY GENERATED ON Fri 05:26:39  02/12/2016
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
* Publications
*  
* Low level class for manipulating the data in the table publications
*
* This source code is auto-generated
*
* @author Victor Mwenda
* Email : vmwenda.vm@gmail.com
* Phone : +254(0)718034449
*/
class Publications {

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
	* private class variable $_publicationId
	*/
	private $_publicationId;
	
	/**
	* returns the value of $publicationId
	*
	* @return object(int|string) publicationId
	*/
	public function _getPublicationId() {
		return $this->_publicationId;
	}
	
	/**
	* sets the value of $_publicationId
	*
	* @param publicationId
	*/
	public function _setPublicationId($publicationId) {
		$this->_publicationId = $publicationId;
	}
	/**
	* sets the value of $_publicationId
	*
	* @param publicationId
	* @return object ( this class)
	*/
	public function setPublicationId($publicationId) {
		$this->_setPublicationId($publicationId);
		return $this;
	}
	
	
	/**
	* private class variable $_content
	*/
	private $_content;
	
	/**
	* returns the value of $content
	*
	* @return object(int|string) content
	*/
	public function _getContent() {
		return $this->_content;
	}
	
	/**
	* sets the value of $_content
	*
	* @param content
	*/
	public function _setContent($content) {
		$this->_content = $content;
	}
	/**
	* sets the value of $_content
	*
	* @param content
	* @return object ( this class)
	*/
	public function setContent($content) {
		$this->_setContent($content);
		return $this;
	}
	
	
	/**
	* private class variable $_fileName
	*/
	private $_fileName;
	
	/**
	* returns the value of $fileName
	*
	* @return object(int|string) fileName
	*/
	public function _getFileName() {
		return $this->_fileName;
	}
	
	/**
	* sets the value of $_fileName
	*
	* @param fileName
	*/
	public function _setFileName($fileName) {
		$this->_fileName = $fileName;
	}
	/**
	* sets the value of $_fileName
	*
	* @param fileName
	* @return object ( this class)
	*/
	public function setFileName($fileName) {
		$this->_setFileName($fileName);
		return $this;
	}
	
	
	/**
	* private class variable $_sortOrder
	*/
	private $_sortOrder;
	
	/**
	* returns the value of $sortOrder
	*
	* @return object(int|string) sortOrder
	*/
	public function _getSortOrder() {
		return $this->_sortOrder;
	}
	
	/**
	* sets the value of $_sortOrder
	*
	* @param sortOrder
	*/
	public function _setSortOrder($sortOrder) {
		$this->_sortOrder = $sortOrder;
	}
	/**
	* sets the value of $_sortOrder
	*
	* @param sortOrder
	* @return object ( this class)
	*/
	public function setSortOrder($sortOrder) {
		$this->_setSortOrder($sortOrder);
		return $this;
	}
	
	
	/**
	* private class variable $_addedBy
	*/
	private $_addedBy;
	
	/**
	* returns the value of $addedBy
	*
	* @return object(int|string) addedBy
	*/
	public function _getAddedBy() {
		return $this->_addedBy;
	}
	
	/**
	* sets the value of $_addedBy
	*
	* @param addedBy
	*/
	public function _setAddedBy($addedBy) {
		$this->_addedBy = $addedBy;
	}
	/**
	* sets the value of $_addedBy
	*
	* @param addedBy
	* @return object ( this class)
	*/
	public function setAddedBy($addedBy) {
		$this->_setAddedBy($addedBy);
		return $this;
	}
	
	
	/**
	* private class variable $_addedOn
	*/
	private $_addedOn;
	
	/**
	* returns the value of $addedOn
	*
	* @return object(int|string) addedOn
	*/
	public function _getAddedOn() {
		return $this->_addedOn;
	}
	
	/**
	* sets the value of $_addedOn
	*
	* @param addedOn
	*/
	public function _setAddedOn($addedOn) {
		$this->_addedOn = $addedOn;
	}
	/**
	* sets the value of $_addedOn
	*
	* @param addedOn
	* @return object ( this class)
	*/
	public function setAddedOn($addedOn) {
		$this->_setAddedOn($addedOn);
		return $this;
	}
	
	
	/**
	* private class variable $_status
	*/
	private $_status;
	
	/**
	* returns the value of $status
	*
	* @return object(int|string) status
	*/
	public function _getStatus() {
		return $this->_status;
	}
	
	/**
	* sets the value of $_status
	*
	* @param status
	*/
	public function _setStatus($status) {
		$this->_status = $status;
	}
	/**
	* sets the value of $_status
	*
	* @param status
	* @return object ( this class)
	*/
	public function setStatus($status) {
		$this->_setStatus($status);
		return $this;
	}
	

		
		
	/**
     * Performs a database query and returns the value of publication_id 
     * based on the value of $publication_id,$content,$file_name,$sort_order,$added_by,$added_on,$status passed to the function
     *
     * @param $publication_id,$content,$file_name,$sort_order,$added_by,$added_on,$status
     * @return object (publication_id)| null
     */
	public function getPublicationId($publication_id,$content,$file_name,$sort_order,$added_by,$added_on,$status) {
		$columns = array ('publication_id','content','file_name','sort_order','added_by','added_on','status');
		$records = array ($publication_id,$content,$file_name,$sort_order,$added_by,$added_on,$status);
		$publication_id_ = $this->query_from_publications ( $columns, $records );
		return sizeof($publication_id_)>0 ? $publication_id_ [0] ['publication_id'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of content 
     * based on the value of $publication_id passed to the function
     *
     * @param $publication_id
     * @return object (content)| null
     */
	public function getContent($publication_id) {
		$columns = array ('publication_id');
		$records = array ($publication_id);
		$content_ = $this->query_from_publications ( $columns, $records );
		return sizeof($content_)>0 ? $content_ [0] ['content'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of file_name 
     * based on the value of $publication_id passed to the function
     *
     * @param $publication_id
     * @return object (file_name)| null
     */
	public function getFileName($publication_id) {
		$columns = array ('publication_id');
		$records = array ($publication_id);
		$file_name_ = $this->query_from_publications ( $columns, $records );
		return sizeof($file_name_)>0 ? $file_name_ [0] ['file_name'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of sort_order 
     * based on the value of $publication_id passed to the function
     *
     * @param $publication_id
     * @return object (sort_order)| null
     */
	public function getSortOrder($publication_id) {
		$columns = array ('publication_id');
		$records = array ($publication_id);
		$sort_order_ = $this->query_from_publications ( $columns, $records );
		return sizeof($sort_order_)>0 ? $sort_order_ [0] ['sort_order'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of added_by 
     * based on the value of $publication_id passed to the function
     *
     * @param $publication_id
     * @return object (added_by)| null
     */
	public function getAddedBy($publication_id) {
		$columns = array ('publication_id');
		$records = array ($publication_id);
		$added_by_ = $this->query_from_publications ( $columns, $records );
		return sizeof($added_by_)>0 ? $added_by_ [0] ['added_by'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of added_on 
     * based on the value of $publication_id passed to the function
     *
     * @param $publication_id
     * @return object (added_on)| null
     */
	public function getAddedOn($publication_id) {
		$columns = array ('publication_id');
		$records = array ($publication_id);
		$added_on_ = $this->query_from_publications ( $columns, $records );
		return sizeof($added_on_)>0 ? $added_on_ [0] ['added_on'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of status 
     * based on the value of $publication_id passed to the function
     *
     * @param $publication_id
     * @return object (status)| null
     */
	public function getStatus($publication_id) {
		$columns = array ('publication_id');
		$records = array ($publication_id);
		$status_ = $this->query_from_publications ( $columns, $records );
		return sizeof($status_)>0 ? $status_ [0] ['status'] : null;
	}
	

	
	/**
	* Inserts data into the table[publications] in the order below
	* array ('publication_id','content','file_name','sort_order','added_by','added_on','status')
	* is mappped into 
	* array ($publication_id,$content,$file_name,$sort_order,$added_by,$added_on,$status)
	* @return int 1 if data was inserted,0 otherwise
	* if redundancy check is true, it inserts if the record if it never existed else.
	* if the record exists, it returns the number of times the record exists on the relation
	*/
	public function insert_prepared_records($publication_id,$content,$file_name,$sort_order,$added_by,$added_on,$status,$redundancy_check= false, $printSQL = false) {
		$columns = array('publication_id','content','file_name','sort_order','added_by','added_on','status');
		$records = array($publication_id,$content,$file_name,$sort_order,$added_by,$added_on,$status);
		return $this->insert_records_to_publications ( $columns, $records,$redundancy_check, $printSQL );
	}

	
	/**
	* Returns the table name. This is the owner of these crud functions.
	* The various crud functions directly affect this table
	* @return string table name -> 'publications' 
	*/
	public static function get_table() {
		return 'publications';
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
     * Used  to calculate the number of times a record exists in the table publications
     * It returns the number of times a record exists exists in the table publications
     * @param array $columns
     * @param array $records
     * @param bool $printSQL
     * @return mixed
     */
	public function is_exists(Array $columns, Array $records, $printSQL = false) {
		return $this->get_database_utils ()->is_exists ( $this->get_table (), $columns, $records, $printSQL );
	}
	
	/**
     * Inserts data into the table publications
     * if redundancy check is true, it inserts if the record if it never existed else.
     * if the record exists, it returns the number of times the record exists on the relation
     *
     * @param array $columns
     * @param array $records
     * @param bool $redundancy_check
     * @param bool $printSQL
     * @return mixed
     */
	public function insert_records_to_publications(Array $columns, Array $records,$redundancy_check = false, $printSQL = false) {
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
	 * Deletes all the records that meets the passed criteria from the table publications
	 * @param array $columns
	 * @param array $records
	 * @param bool $printSQL
	 * @return number of deleted rows
	 */
	public function delete_record_from_publications(Array $columns, Array $records, $printSQL = false) {
		return $this->delete_record ( $this->get_table (), $columns, $records, $printSQL );
	}
	
	/**
	 * Updates all the records that meets the passed criteria from the table publications
	 * 
	 * @param array $columns
	 * @param array $records
	 * @param array $where_columns
	 * @param array $where_records
	 * @param bool $printSQL
	 * @return number of updated rows
	 */
	public function update_record_in_publications(Array $columns, Array $records, Array $where_columns, Array $where_records, $printSQL = false) {
		return $this->update_record ( $this->get_table (), $columns, $records, $where_columns, $where_records, $printSQL );
	}
	
	/**
	 * Gets an Associative array of the records in the table 'publications' that meets the passed criteria
	 *
	 * @param $distinct
	 * @param array $columns
	 * @param array $records
	 * @param string $extraSQL
	 * @param bool $printSQL
	 * @return array|mixed associative
	 */
	public function fetch_assoc_in_publications($distinct, Array $columns, Array $records, $extraSQL="", $printSQL = false) {
		return $this->fetch_assoc ( $distinct, $this->get_table (),$columns, $records, $extraSQL , $printSQL );
	}
	
	/**
	 * Gets an Associative array of the records in the table publications that meets the passed criteria
	 *
	 * @param array $columns
	 * @param array $records
	 * @param string $extraSQL
	 * @param bool $printSQL
	 * @return array|mixed associative
	 */
	public function query_from_publications(Array $columns, Array $records,$extraSQL="",  $printSQL = false) {
		return $this->query ( $this->get_table (), $columns, $records,$extraSQL,$printSQL );
	}
	
	/**
	 * Gets an Associative array of the records in the table publications that meets the passed distinct criteria
	 * 
	 * @param array $columns
	 * @param array $records
	 * @param string $extraSQL
	 * @param bool $printSQL
	 * @return array|mixed associative
	 */
	public function query_distinct_from_publications(Array $columns, Array $records,$extraSQL="",  $printSQL = false) {
		return $this->query_distinct ( $this->get_table (), $columns, $records,$extraSQL,$printSQL );
	}
	
	/**
	 * Performs a search in the table publications that meets the passed criteria
	 * 
	 * @param array $columns
	 * @param array $records
	 * @param string $extraSQL
	 * @param bool $printSQL
	 * @return array|mixed associative
	 */
	public function search_in_publications(Array $columns, Array $records,$extraSQL="", $printSQL = false) {
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
	 * Deletes all the records that meets the passed criteria from the table [publications]
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
     * Inserts data into the table publications
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
     * Updates all the records that meets the passed criteria from the table publications
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
     * Gets an Associative array of the records in the table publications that meets the passed criteria
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
     * Gets an Associative array of the records in the table publications  that meets the passed criteria
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
     * Gets an Associative array of the records in the table publications that meets the distinct passed criteria
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
     * Performs a search and returns an associative array of the records in the table publications  that meets the passed criteria
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
