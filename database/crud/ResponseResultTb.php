<?php

	namespace database\crud;
 
	use database\core\mysql\DatabaseUtils;
    use database\core\mysql\InvalidColumnValueMatchException;
    use database\core\mysql\NullabilityException;

	/**
	* THIS SOURCE CODE WAS AUTOMATICALLY GENERATED ON Fri 05:26:41  02/12/2016
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
* ResponseResultTb
*  
* Low level class for manipulating the data in the table response_result_tb
*
* This source code is auto-generated
*
* @author Victor Mwenda
* Email : vmwenda.vm@gmail.com
* Phone : +254(0)718034449
*/
class ResponseResultTb {

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
	* private class variable $_shipmentMapId
	*/
	private $_shipmentMapId;
	
	/**
	* returns the value of $shipmentMapId
	*
	* @return object(int|string) shipmentMapId
	*/
	public function _getShipmentMapId() {
		return $this->_shipmentMapId;
	}
	
	/**
	* sets the value of $_shipmentMapId
	*
	* @param shipmentMapId
	*/
	public function _setShipmentMapId($shipmentMapId) {
		$this->_shipmentMapId = $shipmentMapId;
	}
	/**
	* sets the value of $_shipmentMapId
	*
	* @param shipmentMapId
	* @return object ( this class)
	*/
	public function setShipmentMapId($shipmentMapId) {
		$this->_setShipmentMapId($shipmentMapId);
		return $this;
	}
	
	
	/**
	* private class variable $_sampleId
	*/
	private $_sampleId;
	
	/**
	* returns the value of $sampleId
	*
	* @return object(int|string) sampleId
	*/
	public function _getSampleId() {
		return $this->_sampleId;
	}
	
	/**
	* sets the value of $_sampleId
	*
	* @param sampleId
	*/
	public function _setSampleId($sampleId) {
		$this->_sampleId = $sampleId;
	}
	/**
	* sets the value of $_sampleId
	*
	* @param sampleId
	* @return object ( this class)
	*/
	public function setSampleId($sampleId) {
		$this->_setSampleId($sampleId);
		return $this;
	}
	
	
	/**
	* private class variable $_dateTested
	*/
	private $_dateTested;
	
	/**
	* returns the value of $dateTested
	*
	* @return object(int|string) dateTested
	*/
	public function _getDateTested() {
		return $this->_dateTested;
	}
	
	/**
	* sets the value of $_dateTested
	*
	* @param dateTested
	*/
	public function _setDateTested($dateTested) {
		$this->_dateTested = $dateTested;
	}
	/**
	* sets the value of $_dateTested
	*
	* @param dateTested
	* @return object ( this class)
	*/
	public function setDateTested($dateTested) {
		$this->_setDateTested($dateTested);
		return $this;
	}
	
	
	/**
	* private class variable $_mtbDetected
	*/
	private $_mtbDetected;
	
	/**
	* returns the value of $mtbDetected
	*
	* @return object(int|string) mtbDetected
	*/
	public function _getMtbDetected() {
		return $this->_mtbDetected;
	}
	
	/**
	* sets the value of $_mtbDetected
	*
	* @param mtbDetected
	*/
	public function _setMtbDetected($mtbDetected) {
		$this->_mtbDetected = $mtbDetected;
	}
	/**
	* sets the value of $_mtbDetected
	*
	* @param mtbDetected
	* @return object ( this class)
	*/
	public function setMtbDetected($mtbDetected) {
		$this->_setMtbDetected($mtbDetected);
		return $this;
	}
	
	
	/**
	* private class variable $_rifResistance
	*/
	private $_rifResistance;
	
	/**
	* returns the value of $rifResistance
	*
	* @return object(int|string) rifResistance
	*/
	public function _getRifResistance() {
		return $this->_rifResistance;
	}
	
	/**
	* sets the value of $_rifResistance
	*
	* @param rifResistance
	*/
	public function _setRifResistance($rifResistance) {
		$this->_rifResistance = $rifResistance;
	}
	/**
	* sets the value of $_rifResistance
	*
	* @param rifResistance
	* @return object ( this class)
	*/
	public function setRifResistance($rifResistance) {
		$this->_setRifResistance($rifResistance);
		return $this;
	}
	
	
	/**
	* private class variable $_probeD
	*/
	private $_probeD;
	
	/**
	* returns the value of $probeD
	*
	* @return object(int|string) probeD
	*/
	public function _getProbeD() {
		return $this->_probeD;
	}
	
	/**
	* sets the value of $_probeD
	*
	* @param probeD
	*/
	public function _setProbeD($probeD) {
		$this->_probeD = $probeD;
	}
	/**
	* sets the value of $_probeD
	*
	* @param probeD
	* @return object ( this class)
	*/
	public function setProbeD($probeD) {
		$this->_setProbeD($probeD);
		return $this;
	}
	
	
	/**
	* private class variable $_probeC
	*/
	private $_probeC;
	
	/**
	* returns the value of $probeC
	*
	* @return object(int|string) probeC
	*/
	public function _getProbeC() {
		return $this->_probeC;
	}
	
	/**
	* sets the value of $_probeC
	*
	* @param probeC
	*/
	public function _setProbeC($probeC) {
		$this->_probeC = $probeC;
	}
	/**
	* sets the value of $_probeC
	*
	* @param probeC
	* @return object ( this class)
	*/
	public function setProbeC($probeC) {
		$this->_setProbeC($probeC);
		return $this;
	}
	
	
	/**
	* private class variable $_probeE
	*/
	private $_probeE;
	
	/**
	* returns the value of $probeE
	*
	* @return object(int|string) probeE
	*/
	public function _getProbeE() {
		return $this->_probeE;
	}
	
	/**
	* sets the value of $_probeE
	*
	* @param probeE
	*/
	public function _setProbeE($probeE) {
		$this->_probeE = $probeE;
	}
	/**
	* sets the value of $_probeE
	*
	* @param probeE
	* @return object ( this class)
	*/
	public function setProbeE($probeE) {
		$this->_setProbeE($probeE);
		return $this;
	}
	
	
	/**
	* private class variable $_probeB
	*/
	private $_probeB;
	
	/**
	* returns the value of $probeB
	*
	* @return object(int|string) probeB
	*/
	public function _getProbeB() {
		return $this->_probeB;
	}
	
	/**
	* sets the value of $_probeB
	*
	* @param probeB
	*/
	public function _setProbeB($probeB) {
		$this->_probeB = $probeB;
	}
	/**
	* sets the value of $_probeB
	*
	* @param probeB
	* @return object ( this class)
	*/
	public function setProbeB($probeB) {
		$this->_setProbeB($probeB);
		return $this;
	}
	
	
	/**
	* private class variable $_spc
	*/
	private $_spc;
	
	/**
	* returns the value of $spc
	*
	* @return object(int|string) spc
	*/
	public function _getSpc() {
		return $this->_spc;
	}
	
	/**
	* sets the value of $_spc
	*
	* @param spc
	*/
	public function _setSpc($spc) {
		$this->_spc = $spc;
	}
	/**
	* sets the value of $_spc
	*
	* @param spc
	* @return object ( this class)
	*/
	public function setSpc($spc) {
		$this->_setSpc($spc);
		return $this;
	}
	
	
	/**
	* private class variable $_probeA
	*/
	private $_probeA;
	
	/**
	* returns the value of $probeA
	*
	* @return object(int|string) probeA
	*/
	public function _getProbeA() {
		return $this->_probeA;
	}
	
	/**
	* sets the value of $_probeA
	*
	* @param probeA
	*/
	public function _setProbeA($probeA) {
		$this->_probeA = $probeA;
	}
	/**
	* sets the value of $_probeA
	*
	* @param probeA
	* @return object ( this class)
	*/
	public function setProbeA($probeA) {
		$this->_setProbeA($probeA);
		return $this;
	}
	
	
	/**
	* private class variable $_calculatedScore
	*/
	private $_calculatedScore;
	
	/**
	* returns the value of $calculatedScore
	*
	* @return object(int|string) calculatedScore
	*/
	public function _getCalculatedScore() {
		return $this->_calculatedScore;
	}
	
	/**
	* sets the value of $_calculatedScore
	*
	* @param calculatedScore
	*/
	public function _setCalculatedScore($calculatedScore) {
		$this->_calculatedScore = $calculatedScore;
	}
	/**
	* sets the value of $_calculatedScore
	*
	* @param calculatedScore
	* @return object ( this class)
	*/
	public function setCalculatedScore($calculatedScore) {
		$this->_setCalculatedScore($calculatedScore);
		return $this;
	}
	
	
	/**
	* private class variable $_createdBy
	*/
	private $_createdBy;
	
	/**
	* returns the value of $createdBy
	*
	* @return object(int|string) createdBy
	*/
	public function _getCreatedBy() {
		return $this->_createdBy;
	}
	
	/**
	* sets the value of $_createdBy
	*
	* @param createdBy
	*/
	public function _setCreatedBy($createdBy) {
		$this->_createdBy = $createdBy;
	}
	/**
	* sets the value of $_createdBy
	*
	* @param createdBy
	* @return object ( this class)
	*/
	public function setCreatedBy($createdBy) {
		$this->_setCreatedBy($createdBy);
		return $this;
	}
	
	
	/**
	* private class variable $_createdOn
	*/
	private $_createdOn;
	
	/**
	* returns the value of $createdOn
	*
	* @return object(int|string) createdOn
	*/
	public function _getCreatedOn() {
		return $this->_createdOn;
	}
	
	/**
	* sets the value of $_createdOn
	*
	* @param createdOn
	*/
	public function _setCreatedOn($createdOn) {
		$this->_createdOn = $createdOn;
	}
	/**
	* sets the value of $_createdOn
	*
	* @param createdOn
	* @return object ( this class)
	*/
	public function setCreatedOn($createdOn) {
		$this->_setCreatedOn($createdOn);
		return $this;
	}
	
	
	/**
	* private class variable $_updatedBy
	*/
	private $_updatedBy;
	
	/**
	* returns the value of $updatedBy
	*
	* @return object(int|string) updatedBy
	*/
	public function _getUpdatedBy() {
		return $this->_updatedBy;
	}
	
	/**
	* sets the value of $_updatedBy
	*
	* @param updatedBy
	*/
	public function _setUpdatedBy($updatedBy) {
		$this->_updatedBy = $updatedBy;
	}
	/**
	* sets the value of $_updatedBy
	*
	* @param updatedBy
	* @return object ( this class)
	*/
	public function setUpdatedBy($updatedBy) {
		$this->_setUpdatedBy($updatedBy);
		return $this;
	}
	
	
	/**
	* private class variable $_updatedOn
	*/
	private $_updatedOn;
	
	/**
	* returns the value of $updatedOn
	*
	* @return object(int|string) updatedOn
	*/
	public function _getUpdatedOn() {
		return $this->_updatedOn;
	}
	
	/**
	* sets the value of $_updatedOn
	*
	* @param updatedOn
	*/
	public function _setUpdatedOn($updatedOn) {
		$this->_updatedOn = $updatedOn;
	}
	/**
	* sets the value of $_updatedOn
	*
	* @param updatedOn
	* @return object ( this class)
	*/
	public function setUpdatedOn($updatedOn) {
		$this->_setUpdatedOn($updatedOn);
		return $this;
	}
	

		
		
	/**
     * Performs a database query and returns the value of shipment_map_id 
     * based on the value of $sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on, passed to the function
     *
     * @param $sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,
     * @return object (shipment_map_id)| null
     */
	public function getShipmentMapId($sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,) {
		$columns = array ('sample_id','date_tested','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','probe_a','calculated_score','created_by','created_on','updated_by','updated_on',);
		$records = array ($sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,);
		$shipment_map_id_ = $this->query_from_response_result_tb ( $columns, $records );
		return sizeof($shipment_map_id_)>0 ? $shipment_map_id_ [0] ['shipment_map_id'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of sample_id 
     * based on the value of $shipment_map_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on, passed to the function
     *
     * @param $shipment_map_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,
     * @return object (sample_id)| null
     */
	public function getSampleId($shipment_map_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,) {
		$columns = array ('shipment_map_id','date_tested','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','probe_a','calculated_score','created_by','created_on','updated_by','updated_on',);
		$records = array ($shipment_map_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,);
		$sample_id_ = $this->query_from_response_result_tb ( $columns, $records );
		return sizeof($sample_id_)>0 ? $sample_id_ [0] ['sample_id'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of date_tested 
     * based on the value of $shipment_map_id,$sample_id,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on, passed to the function
     *
     * @param $shipment_map_id,$sample_id,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,
     * @return object (date_tested)| null
     */
	public function getDateTested($shipment_map_id,$sample_id,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,) {
		$columns = array ('shipment_map_id','sample_id','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','probe_a','calculated_score','created_by','created_on','updated_by','updated_on',);
		$records = array ($shipment_map_id,$sample_id,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,);
		$date_tested_ = $this->query_from_response_result_tb ( $columns, $records );
		return sizeof($date_tested_)>0 ? $date_tested_ [0] ['date_tested'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of mtb_detected 
     * based on the value of $shipment_map_id,$sample_id,$date_tested,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on, passed to the function
     *
     * @param $shipment_map_id,$sample_id,$date_tested,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,
     * @return object (mtb_detected)| null
     */
	public function getMtbDetected($shipment_map_id,$sample_id,$date_tested,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,) {
		$columns = array ('shipment_map_id','sample_id','date_tested','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','probe_a','calculated_score','created_by','created_on','updated_by','updated_on',);
		$records = array ($shipment_map_id,$sample_id,$date_tested,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,);
		$mtb_detected_ = $this->query_from_response_result_tb ( $columns, $records );
		return sizeof($mtb_detected_)>0 ? $mtb_detected_ [0] ['mtb_detected'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of rif_resistance 
     * based on the value of $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on, passed to the function
     *
     * @param $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,
     * @return object (rif_resistance)| null
     */
	public function getRifResistance($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,) {
		$columns = array ('shipment_map_id','sample_id','date_tested','mtb_detected','probe_d','probe_c','probe_e','probe_b','spc','probe_a','calculated_score','created_by','created_on','updated_by','updated_on',);
		$records = array ($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,);
		$rif_resistance_ = $this->query_from_response_result_tb ( $columns, $records );
		return sizeof($rif_resistance_)>0 ? $rif_resistance_ [0] ['rif_resistance'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of probe_d 
     * based on the value of $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on, passed to the function
     *
     * @param $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,
     * @return object (probe_d)| null
     */
	public function getProbeD($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,) {
		$columns = array ('shipment_map_id','sample_id','date_tested','mtb_detected','rif_resistance','probe_c','probe_e','probe_b','spc','probe_a','calculated_score','created_by','created_on','updated_by','updated_on',);
		$records = array ($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,);
		$probe_d_ = $this->query_from_response_result_tb ( $columns, $records );
		return sizeof($probe_d_)>0 ? $probe_d_ [0] ['probe_d'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of probe_c 
     * based on the value of $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on, passed to the function
     *
     * @param $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,
     * @return object (probe_c)| null
     */
	public function getProbeC($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,) {
		$columns = array ('shipment_map_id','sample_id','date_tested','mtb_detected','rif_resistance','probe_d','probe_e','probe_b','spc','probe_a','calculated_score','created_by','created_on','updated_by','updated_on',);
		$records = array ($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,);
		$probe_c_ = $this->query_from_response_result_tb ( $columns, $records );
		return sizeof($probe_c_)>0 ? $probe_c_ [0] ['probe_c'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of probe_e 
     * based on the value of $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on, passed to the function
     *
     * @param $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,
     * @return object (probe_e)| null
     */
	public function getProbeE($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,) {
		$columns = array ('shipment_map_id','sample_id','date_tested','mtb_detected','rif_resistance','probe_d','probe_c','probe_b','spc','probe_a','calculated_score','created_by','created_on','updated_by','updated_on',);
		$records = array ($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,);
		$probe_e_ = $this->query_from_response_result_tb ( $columns, $records );
		return sizeof($probe_e_)>0 ? $probe_e_ [0] ['probe_e'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of probe_b 
     * based on the value of $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on, passed to the function
     *
     * @param $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,
     * @return object (probe_b)| null
     */
	public function getProbeB($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,) {
		$columns = array ('shipment_map_id','sample_id','date_tested','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','spc','probe_a','calculated_score','created_by','created_on','updated_by','updated_on',);
		$records = array ($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,);
		$probe_b_ = $this->query_from_response_result_tb ( $columns, $records );
		return sizeof($probe_b_)>0 ? $probe_b_ [0] ['probe_b'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of spc 
     * based on the value of $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on, passed to the function
     *
     * @param $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,
     * @return object (spc)| null
     */
	public function getSpc($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,) {
		$columns = array ('shipment_map_id','sample_id','date_tested','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','probe_a','calculated_score','created_by','created_on','updated_by','updated_on',);
		$records = array ($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,);
		$spc_ = $this->query_from_response_result_tb ( $columns, $records );
		return sizeof($spc_)>0 ? $spc_ [0] ['spc'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of probe_a 
     * based on the value of $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$calculated_score,$created_by,$created_on,$updated_by,$updated_on, passed to the function
     *
     * @param $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,
     * @return object (probe_a)| null
     */
	public function getProbeA($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,) {
		$columns = array ('shipment_map_id','sample_id','date_tested','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','calculated_score','created_by','created_on','updated_by','updated_on',);
		$records = array ($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,);
		$probe_a_ = $this->query_from_response_result_tb ( $columns, $records );
		return sizeof($probe_a_)>0 ? $probe_a_ [0] ['probe_a'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of calculated_score 
     * based on the value of $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$created_by,$created_on,$updated_by,$updated_on, passed to the function
     *
     * @param $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$created_by,$created_on,$updated_by,$updated_on,
     * @return object (calculated_score)| null
     */
	public function getCalculatedScore($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$created_by,$created_on,$updated_by,$updated_on,) {
		$columns = array ('shipment_map_id','sample_id','date_tested','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','probe_a','created_by','created_on','updated_by','updated_on',);
		$records = array ($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$created_by,$created_on,$updated_by,$updated_on,);
		$calculated_score_ = $this->query_from_response_result_tb ( $columns, $records );
		return sizeof($calculated_score_)>0 ? $calculated_score_ [0] ['calculated_score'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of created_by 
     * based on the value of $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_on,$updated_by,$updated_on, passed to the function
     *
     * @param $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_on,$updated_by,$updated_on,
     * @return object (created_by)| null
     */
	public function getCreatedBy($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_on,$updated_by,$updated_on,) {
		$columns = array ('shipment_map_id','sample_id','date_tested','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','probe_a','calculated_score','created_on','updated_by','updated_on',);
		$records = array ($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_on,$updated_by,$updated_on,);
		$created_by_ = $this->query_from_response_result_tb ( $columns, $records );
		return sizeof($created_by_)>0 ? $created_by_ [0] ['created_by'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of created_on 
     * based on the value of $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$updated_by,$updated_on, passed to the function
     *
     * @param $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$updated_by,$updated_on,
     * @return object (created_on)| null
     */
	public function getCreatedOn($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$updated_by,$updated_on,) {
		$columns = array ('shipment_map_id','sample_id','date_tested','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','probe_a','calculated_score','created_by','updated_by','updated_on',);
		$records = array ($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$updated_by,$updated_on,);
		$created_on_ = $this->query_from_response_result_tb ( $columns, $records );
		return sizeof($created_on_)>0 ? $created_on_ [0] ['created_on'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of updated_by 
     * based on the value of $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_on, passed to the function
     *
     * @param $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_on,
     * @return object (updated_by)| null
     */
	public function getUpdatedBy($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_on,) {
		$columns = array ('shipment_map_id','sample_id','date_tested','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','probe_a','calculated_score','created_by','created_on','updated_on',);
		$records = array ($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_on,);
		$updated_by_ = $this->query_from_response_result_tb ( $columns, $records );
		return sizeof($updated_by_)>0 ? $updated_by_ [0] ['updated_by'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of updated_on 
     * based on the value of $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by, passed to the function
     *
     * @param $shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,
     * @return object (updated_on)| null
     */
	public function getUpdatedOn($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,) {
		$columns = array ('shipment_map_id','sample_id','date_tested','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','probe_a','calculated_score','created_by','created_on','updated_by',);
		$records = array ($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,);
		$updated_on_ = $this->query_from_response_result_tb ( $columns, $records );
		return sizeof($updated_on_)>0 ? $updated_on_ [0] ['updated_on'] : null;
	}
	

	
	/**
	* Inserts data into the table[response_result_tb] in the order below
	* array ('shipment_map_id','sample_id','date_tested','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','probe_a','calculated_score','created_by','created_on','updated_by','updated_on')
	* is mappped into 
	* array ($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on)
	* @return int 1 if data was inserted,0 otherwise
	* if redundancy check is true, it inserts if the record if it never existed else.
	* if the record exists, it returns the number of times the record exists on the relation
	*/
	public function insert_prepared_records($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on,$redundancy_check= false, $printSQL = false) {
		$columns = array('shipment_map_id','sample_id','date_tested','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','probe_a','calculated_score','created_by','created_on','updated_by','updated_on');
		$records = array($shipment_map_id,$sample_id,$date_tested,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$calculated_score,$created_by,$created_on,$updated_by,$updated_on);
		return $this->insert_records_to_response_result_tb ( $columns, $records,$redundancy_check, $printSQL );
	}

	
	/**
	* Returns the table name. This is the owner of these crud functions.
	* The various crud functions directly affect this table
	* @return string table name -> 'response_result_tb' 
	*/
	public static function get_table() {
		return 'response_result_tb';
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
     * Used  to calculate the number of times a record exists in the table response_result_tb
     * It returns the number of times a record exists exists in the table response_result_tb
     * @param array $columns
     * @param array $records
     * @param bool $printSQL
     * @return mixed
     */
	public function is_exists(Array $columns, Array $records, $printSQL = false) {
		return $this->get_database_utils ()->is_exists ( $this->get_table (), $columns, $records, $printSQL );
	}
	
	/**
     * Inserts data into the table response_result_tb
     * if redundancy check is true, it inserts if the record if it never existed else.
     * if the record exists, it returns the number of times the record exists on the relation
     *
     * @param array $columns
     * @param array $records
     * @param bool $redundancy_check
     * @param bool $printSQL
     * @return mixed
     */
	public function insert_records_to_response_result_tb(Array $columns, Array $records,$redundancy_check = false, $printSQL = false) {
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
	 * Deletes all the records that meets the passed criteria from the table response_result_tb
	 * @param array $columns
	 * @param array $records
	 * @param bool $printSQL
	 * @return number of deleted rows
	 */
	public function delete_record_from_response_result_tb(Array $columns, Array $records, $printSQL = false) {
		return $this->delete_record ( $this->get_table (), $columns, $records, $printSQL );
	}
	
	/**
	 * Updates all the records that meets the passed criteria from the table response_result_tb
	 * 
	 * @param array $columns
	 * @param array $records
	 * @param array $where_columns
	 * @param array $where_records
	 * @param bool $printSQL
	 * @return number of updated rows
	 */
	public function update_record_in_response_result_tb(Array $columns, Array $records, Array $where_columns, Array $where_records, $printSQL = false) {
		return $this->update_record ( $this->get_table (), $columns, $records, $where_columns, $where_records, $printSQL );
	}
	
	/**
	 * Gets an Associative array of the records in the table 'response_result_tb' that meets the passed criteria
	 *
	 * @param $distinct
	 * @param array $columns
	 * @param array $records
	 * @param string $extraSQL
	 * @param bool $printSQL
	 * @return array|mixed associative
	 */
	public function fetch_assoc_in_response_result_tb($distinct, Array $columns, Array $records, $extraSQL="", $printSQL = false) {
		return $this->fetch_assoc ( $distinct, $this->get_table (),$columns, $records, $extraSQL , $printSQL );
	}
	
	/**
	 * Gets an Associative array of the records in the table response_result_tb that meets the passed criteria
	 *
	 * @param array $columns
	 * @param array $records
	 * @param string $extraSQL
	 * @param bool $printSQL
	 * @return array|mixed associative
	 */
	public function query_from_response_result_tb(Array $columns, Array $records,$extraSQL="",  $printSQL = false) {
		return $this->query ( $this->get_table (), $columns, $records,$extraSQL,$printSQL );
	}
	
	/**
	 * Gets an Associative array of the records in the table response_result_tb that meets the passed distinct criteria
	 * 
	 * @param array $columns
	 * @param array $records
	 * @param string $extraSQL
	 * @param bool $printSQL
	 * @return array|mixed associative
	 */
	public function query_distinct_from_response_result_tb(Array $columns, Array $records,$extraSQL="",  $printSQL = false) {
		return $this->query_distinct ( $this->get_table (), $columns, $records,$extraSQL,$printSQL );
	}
	
	/**
	 * Performs a search in the table response_result_tb that meets the passed criteria
	 * 
	 * @param array $columns
	 * @param array $records
	 * @param string $extraSQL
	 * @param bool $printSQL
	 * @return array|mixed associative
	 */
	public function search_in_response_result_tb(Array $columns, Array $records,$extraSQL="", $printSQL = false) {
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
	 * Deletes all the records that meets the passed criteria from the table [response_result_tb]
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
     * Inserts data into the table response_result_tb
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
     * Updates all the records that meets the passed criteria from the table response_result_tb
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
     * Gets an Associative array of the records in the table response_result_tb that meets the passed criteria
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
     * Gets an Associative array of the records in the table response_result_tb  that meets the passed criteria
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
     * Gets an Associative array of the records in the table response_result_tb that meets the distinct passed criteria
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
     * Performs a search and returns an associative array of the records in the table response_result_tb  that meets the passed criteria
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
