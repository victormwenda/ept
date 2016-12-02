<?php 

	namespace database\modules;

	use database\crud\Partners;

	/**
	*  
	*	PartnersInfo
	*  
	* Provides High level features for interacting with the Partners;
	*
	* This source code is auto-generated
    *
    * @author Victor Mwenda
    * Email : vmwenda.vm@gmail.com
    * Phone : +254(0)718034449
	*/
	class PartnersInfo{

	private $build;
	private $client;
	private $action;
	private $partners;
	private $table = 'partners';
	/**
	 * PartnersInfo
	 * 
	 * Class to get all the partners Information from the partners table 
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
		
		$this->partners = new Partners( $action, $client );

	}

	

		/**
	* Inserts data into the table[partners] in the order below
	* array ('partner_id','partner_name','link','sort_order','added_by','added_on','status')
	* is mappped into 
	* array ($partner_id,$partner_name,$link,$sort_order,$added_by,$added_on,$status)
	* @return 1 if data was inserted,0 otherwise
	* if redundancy check is true, it inserts if the record if it never existed else.
	* if the record exists, it returns the number of times the record exists on the relation
	*/
	public function insert($partner_id,$partner_name,$link,$sort_order,$added_by,$added_on,$status,$redundancy_check= false, $printSQL = false) {
		$columns = array('partner_id','partner_name','link','sort_order','added_by','added_on','status');
		$records = array($partner_id,$partner_name,$link,$sort_order,$added_by,$added_on,$status);
		return $this->partners->insert_prepared_records($partner_id,$partner_name,$link,$sort_order,$added_by,$added_on,$status,$redundancy_check,$printSQL );
	}


 	/**
     * @param $distinct
     * @param string $extraSQL
     * @return string
     */
	public function query($distinct,$extraSQL=""){

		$columns = $records = array ();
		$queried_partners = $this->partners->fetch_assoc_in_partners ($distinct, $columns, $records,$extraSQL );

		if($this->build == "eng-build"){
			return $this->query_eng_build($queried_partners);
		}
		if($this->build == "user-build"){
			return $this->query_user_build($queried_partners);
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
        return $this->partners->insert_raw($records, $printSQL);
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
        public function insert_records_to_partners(Array $columns, Array $records,$redundancy_check = false, $printSQL = false){
            return $this->partners->insert_records_to_partners($columns, $records,$redundancy_check,$printSQL);
        }

     /**
        * Performs a raw Query
        * @param $sql string sql to execute
        * @return string sql results
        * @throws \app\libs\marvik\libs\database\core\mysql\NullabilityException
        */
	public function rawQuery($sql){

		$queried_partners = $this->partners->get_database_utils()->rawQuery($sql);

		if($this->build == "eng-build"){
			return $this->query_eng_build($queried_partners);
		}
		if($this->build == "user-build"){
			return $this->query_user_build($queried_partners);
		}
	}

	public function query_eng_build($queried_partners){
		if($this->client == "web-desktop"){
			return $this->export_query_html($queried_partners);
		}
		if($this->client == "mobile-android"){
			return $this->export_query_json($queried_partners);
		}
	}
	public function query_user_build($queried_partners){
		if($this->client == "web-desktop"){
			return $this->export_query_html($queried_partners);
		}
		if($this->client == "mobile-android"){
			return $this->export_query_json($queried_partners);
		}
	}
	public function export_query_json($queried_partners){
		$query_json = json_encode($queried_partners);
		return $query_json;
	}
	public function export_query_html($queried_partners){
		$query_html = "";
		foreach ( $queried_partners as $partners_row_items ) {
			$query_html .= $this->process_query_for_html_export ( $partners_row_items );
		}
		return $query_html;
	}

	private function process_query_for_html_export ( $partners_row_items ){
		$html_export ='<div style="padding:10px;margin:10px;border:2px solid black;"><h3>'  .$this->table.  '</h3>';
		
		$partner_id = $partners_row_items ['partner_id'];
	if ($partner_id  != null) {
	$html_export .= $this->parseHtmlExport ( 'partner_id', $partner_id  );
}
$partner_name = $partners_row_items ['partner_name'];
	if ($partner_name  != null) {
	$html_export .= $this->parseHtmlExport ( 'partner_name', $partner_name  );
}
$link = $partners_row_items ['link'];
	if ($link  != null) {
	$html_export .= $this->parseHtmlExport ( 'link', $link  );
}
$sort_order = $partners_row_items ['sort_order'];
	if ($sort_order  != null) {
	$html_export .= $this->parseHtmlExport ( 'sort_order', $sort_order  );
}
$added_by = $partners_row_items ['added_by'];
	if ($added_by  != null) {
	$html_export .= $this->parseHtmlExport ( 'added_by', $added_by  );
}
$added_on = $partners_row_items ['added_on'];
	if ($added_on  != null) {
	$html_export .= $this->parseHtmlExport ( 'added_on', $added_on  );
}
$status = $partners_row_items ['status'];
	if ($status  != null) {
	$html_export .= $this->parseHtmlExport ( 'status', $status  );
}

		
		return $html_export .='</div>';
	}

	private function parseHtmlExport($title,$message){
		return '<div style="width:400px;"><h4>' . $title . '</h4><hr /><p>' . $message . '</p></div>';
	}
} ?>
