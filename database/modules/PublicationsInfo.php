<?php 

	namespace database\modules;

	use database\crud\Publications;

	/**
	*  
	*	PublicationsInfo
	*  
	* Provides High level features for interacting with the Publications;
	*
	* This source code is auto-generated
    *
    * @author Victor Mwenda
    * Email : vmwenda.vm@gmail.com
    * Phone : +254(0)718034449
	*/
	class PublicationsInfo{

	private $build;
	private $client;
	private $action;
	private $publications;
	private $table = 'publications';
	/**
	 * PublicationsInfo
	 * 
	 * Class to get all the publications Information from the publications table 
	 * @param String $action
	 * @param String $client
	 * @param String $build
	 * 
	 * @author Victor Mwenda
	 * Email : vmwenda.vm@gmail.com
	 * Phone : +254718034449
	 */
	public function __construct($action = "query", $client = "mobile-android",$build="user-build") {

		$this->client = $client;
		$this->action = $action;
		$this->build = $build;
		
		$this->publications = new Publications( $action, $client );

	}

	

		/**
	* Inserts data into the table[publications] in the order below
	* array ('publication_id','content','file_name','sort_order','added_by','added_on','status')
	* is mappped into 
	* array ($publication_id,$content,$file_name,$sort_order,$added_by,$added_on,$status)
	* @return 1 if data was inserted,0 otherwise
	* if redundancy check is true, it inserts if the record if it never existed else.
	* if the record exists, it returns the number of times the record exists on the relation
	*/
	public function insert($publication_id,$content,$file_name,$sort_order,$added_by,$added_on,$status,$redundancy_check= false, $printSQL = false) {
		$columns = array('publication_id','content','file_name','sort_order','added_by','added_on','status');
		$records = array($publication_id,$content,$file_name,$sort_order,$added_by,$added_on,$status);
		return $this->publications->insert_prepared_records($publication_id,$content,$file_name,$sort_order,$added_by,$added_on,$status,$redundancy_check,$printSQL );
	}


 	/**
     * @param $distinct
     * @param string $extraSQL
     * @return string
     */
	public function query($distinct,$extraSQL=""){

		$columns = $records = array ();
		$queried_publications = $this->publications->fetch_assoc_in_publications ($distinct, $columns, $records,$extraSQL );

		if($this->build == "eng-build"){
			return $this->query_eng_build($queried_publications);
		}
		if($this->build == "user-build"){
			return $this->query_user_build($queried_publications);
		}
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
        return $this->publications->insert_raw($records, $printSQL);
    }

    /**
     * Inserts records in a relation
     * The records are matched alongside the columns in the relation
         * @param array $columns
         * @param array $records
         * @param bool $redundancy_check
         * @param bool $printSQL
         * @return mixed
         */
        public function insert_records_to_publications(Array $columns, Array $records,$redundancy_check = false, $printSQL = false){
            return $this->publications->insert_records_to_publications($columns, $records,$redundancy_check,$printSQL);
        }

     /**
        * Performs a raw Query
        * @param $sql string sql to execute
        * @return string sql results
        * @throws \app\libs\marvik\libs\database\core\mysql\NullabilityException
        */
	public function rawQuery($sql){

		$queried_publications = $this->publications->get_database_utils()->rawQuery($sql);

		if($this->build == "eng-build"){
			return $this->query_eng_build($queried_publications);
		}
		if($this->build == "user-build"){
			return $this->query_user_build($queried_publications);
		}
	}

	public function query_eng_build($queried_publications){
		if($this->client == "web-desktop"){
			return $this->export_query_html($queried_publications);
		}
		if($this->client == "mobile-android"){
			return $this->export_query_json($queried_publications);
		}
	}
	public function query_user_build($queried_publications){
		if($this->client == "web-desktop"){
			return $this->export_query_html($queried_publications);
		}
		if($this->client == "mobile-android"){
			return $this->export_query_json($queried_publications);
		}
	}
	public function export_query_json($queried_publications){
		$query_json = json_encode($queried_publications);
		return $query_json;
	}
	public function export_query_html($queried_publications){
		$query_html = "";
		foreach ( $queried_publications as $publications_row_items ) {
			$query_html .= $this->process_query_for_html_export ( $publications_row_items );
		}
		return $query_html;
	}

	private function process_query_for_html_export ( $publications_row_items ){
		$html_export ='<div style="padding:10px;margin:10px;border:2px solid black;"><h3>'  .$this->table.  '</h3>';
		
		$publication_id = $publications_row_items ['publication_id'];
	if ($publication_id  != null) {
	$html_export .= $this->parseHtmlExport ( 'publication_id', $publication_id  );
}
$content = $publications_row_items ['content'];
	if ($content  != null) {
	$html_export .= $this->parseHtmlExport ( 'content', $content  );
}
$file_name = $publications_row_items ['file_name'];
	if ($file_name  != null) {
	$html_export .= $this->parseHtmlExport ( 'file_name', $file_name  );
}
$sort_order = $publications_row_items ['sort_order'];
	if ($sort_order  != null) {
	$html_export .= $this->parseHtmlExport ( 'sort_order', $sort_order  );
}
$added_by = $publications_row_items ['added_by'];
	if ($added_by  != null) {
	$html_export .= $this->parseHtmlExport ( 'added_by', $added_by  );
}
$added_on = $publications_row_items ['added_on'];
	if ($added_on  != null) {
	$html_export .= $this->parseHtmlExport ( 'added_on', $added_on  );
}
$status = $publications_row_items ['status'];
	if ($status  != null) {
	$html_export .= $this->parseHtmlExport ( 'status', $status  );
}

		
		return $html_export .='</div>';
	}

	private function parseHtmlExport($title,$message){
		return '<div style="width:400px;"><h4>' . $title . '</h4><hr /><p>' . $message . '</p></div>';
	}
} ?>
