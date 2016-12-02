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
* ReferenceResultTb
*  
* Low level class for manipulating the data in the table reference_result_tb
*
* This source code is auto-generated
*
* @author Victor Mwenda
* Email : vmwenda.vm@gmail.com
* Phone : +254(0)718034449
*/
class ReferenceResultTb {

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
	* private class variable $_shipmentId
	*/
	private $_shipmentId;
	
	/**
	* returns the value of $shipmentId
	*
	* @return object(int|string) shipmentId
	*/
	public function _getShipmentId() {
		return $this->_shipmentId;
	}
	
	/**
	* sets the value of $_shipmentId
	*
	* @param shipmentId
	*/
	public function _setShipmentId($shipmentId) {
		$this->_shipmentId = $shipmentId;
	}
	/**
	* sets the value of $_shipmentId
	*
	* @param shipmentId
	* @return object ( this class)
	*/
	public function setShipmentId($shipmentId) {
		$this->_setShipmentId($shipmentId);
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
	* private class variable $_sampleLabel
	*/
	private $_sampleLabel;
	
	/**
	* returns the value of $sampleLabel
	*
	* @return object(int|string) sampleLabel
	*/
	public function _getSampleLabel() {
		return $this->_sampleLabel;
	}
	
	/**
	* sets the value of $_sampleLabel
	*
	* @param sampleLabel
	*/
	public function _setSampleLabel($sampleLabel) {
		$this->_sampleLabel = $sampleLabel;
	}
	/**
	* sets the value of $_sampleLabel
	*
	* @param sampleLabel
	* @return object ( this class)
	*/
	public function setSampleLabel($sampleLabel) {
		$this->_setSampleLabel($sampleLabel);
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
	* private class variable $_control
	*/
	private $_control;
	
	/**
	* returns the value of $control
	*
	* @return object(int|string) control
	*/
	public function _getControl() {
		return $this->_control;
	}
	
	/**
	* sets the value of $_control
	*
	* @param control
	*/
	public function _setControl($control) {
		$this->_control = $control;
	}
	/**
	* sets the value of $_control
	*
	* @param control
	* @return object ( this class)
	*/
	public function setControl($control) {
		$this->_setControl($control);
		return $this;
	}
	
	
	/**
	* private class variable $_mandatory
	*/
	private $_mandatory;
	
	/**
	* returns the value of $mandatory
	*
	* @return object(int|string) mandatory
	*/
	public function _getMandatory() {
		return $this->_mandatory;
	}
	
	/**
	* sets the value of $_mandatory
	*
	* @param mandatory
	*/
	public function _setMandatory($mandatory) {
		$this->_mandatory = $mandatory;
	}
	/**
	* sets the value of $_mandatory
	*
	* @param mandatory
	* @return object ( this class)
	*/
	public function setMandatory($mandatory) {
		$this->_setMandatory($mandatory);
		return $this;
	}
	
	
	/**
	* private class variable $_sampleScore
	*/
	private $_sampleScore;
	
	/**
	* returns the value of $sampleScore
	*
	* @return object(int|string) sampleScore
	*/
	public function _getSampleScore() {
		return $this->_sampleScore;
	}
	
	/**
	* sets the value of $_sampleScore
	*
	* @param sampleScore
	*/
	public function _setSampleScore($sampleScore) {
		$this->_sampleScore = $sampleScore;
	}
	/**
	* sets the value of $_sampleScore
	*
	* @param sampleScore
	* @return object ( this class)
	*/
	public function setSampleScore($sampleScore) {
		$this->_setSampleScore($sampleScore);
		return $this;
	}
	

		
		
	/**
     * Performs a database query and returns the value of shipment_id 
     * based on the value of $sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score, passed to the function
     *
     * @param $sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,
     * @return object (shipment_id)| null
     */
	public function getShipmentId($sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,) {
		$columns = array ('sample_id','sample_label','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','probe_a','control','mandatory','sample_score',);
		$records = array ($sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,);
		$shipment_id_ = $this->query_from_reference_result_tb ( $columns, $records );
		return sizeof($shipment_id_)>0 ? $shipment_id_ [0] ['shipment_id'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of sample_id 
     * based on the value of $shipment_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score, passed to the function
     *
     * @param $shipment_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,
     * @return object (sample_id)| null
     */
	public function getSampleId($shipment_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,) {
		$columns = array ('shipment_id','sample_label','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','probe_a','control','mandatory','sample_score',);
		$records = array ($shipment_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,);
		$sample_id_ = $this->query_from_reference_result_tb ( $columns, $records );
		return sizeof($sample_id_)>0 ? $sample_id_ [0] ['sample_id'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of sample_label 
     * based on the value of $shipment_id,$sample_id,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score, passed to the function
     *
     * @param $shipment_id,$sample_id,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,
     * @return object (sample_label)| null
     */
	public function getSampleLabel($shipment_id,$sample_id,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,) {
		$columns = array ('shipment_id','sample_id','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','probe_a','control','mandatory','sample_score',);
		$records = array ($shipment_id,$sample_id,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,);
		$sample_label_ = $this->query_from_reference_result_tb ( $columns, $records );
		return sizeof($sample_label_)>0 ? $sample_label_ [0] ['sample_label'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of mtb_detected 
     * based on the value of $shipment_id,$sample_id,$sample_label,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score, passed to the function
     *
     * @param $shipment_id,$sample_id,$sample_label,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,
     * @return object (mtb_detected)| null
     */
	public function getMtbDetected($shipment_id,$sample_id,$sample_label,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,) {
		$columns = array ('shipment_id','sample_id','sample_label','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','probe_a','control','mandatory','sample_score',);
		$records = array ($shipment_id,$sample_id,$sample_label,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,);
		$mtb_detected_ = $this->query_from_reference_result_tb ( $columns, $records );
		return sizeof($mtb_detected_)>0 ? $mtb_detected_ [0] ['mtb_detected'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of rif_resistance 
     * based on the value of $shipment_id,$sample_id,$sample_label,$mtb_detected,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score, passed to the function
     *
     * @param $shipment_id,$sample_id,$sample_label,$mtb_detected,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,
     * @return object (rif_resistance)| null
     */
	public function getRifResistance($shipment_id,$sample_id,$sample_label,$mtb_detected,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,) {
		$columns = array ('shipment_id','sample_id','sample_label','mtb_detected','probe_d','probe_c','probe_e','probe_b','spc','probe_a','control','mandatory','sample_score',);
		$records = array ($shipment_id,$sample_id,$sample_label,$mtb_detected,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,);
		$rif_resistance_ = $this->query_from_reference_result_tb ( $columns, $records );
		return sizeof($rif_resistance_)>0 ? $rif_resistance_ [0] ['rif_resistance'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of probe_d 
     * based on the value of $shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score, passed to the function
     *
     * @param $shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,
     * @return object (probe_d)| null
     */
	public function getProbeD($shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,) {
		$columns = array ('shipment_id','sample_id','sample_label','mtb_detected','rif_resistance','probe_c','probe_e','probe_b','spc','probe_a','control','mandatory','sample_score',);
		$records = array ($shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,);
		$probe_d_ = $this->query_from_reference_result_tb ( $columns, $records );
		return sizeof($probe_d_)>0 ? $probe_d_ [0] ['probe_d'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of probe_c 
     * based on the value of $shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score, passed to the function
     *
     * @param $shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,
     * @return object (probe_c)| null
     */
	public function getProbeC($shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,) {
		$columns = array ('shipment_id','sample_id','sample_label','mtb_detected','rif_resistance','probe_d','probe_e','probe_b','spc','probe_a','control','mandatory','sample_score',);
		$records = array ($shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,);
		$probe_c_ = $this->query_from_reference_result_tb ( $columns, $records );
		return sizeof($probe_c_)>0 ? $probe_c_ [0] ['probe_c'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of probe_e 
     * based on the value of $shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score, passed to the function
     *
     * @param $shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,
     * @return object (probe_e)| null
     */
	public function getProbeE($shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,) {
		$columns = array ('shipment_id','sample_id','sample_label','mtb_detected','rif_resistance','probe_d','probe_c','probe_b','spc','probe_a','control','mandatory','sample_score',);
		$records = array ($shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,);
		$probe_e_ = $this->query_from_reference_result_tb ( $columns, $records );
		return sizeof($probe_e_)>0 ? $probe_e_ [0] ['probe_e'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of probe_b 
     * based on the value of $shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$spc,$probe_a,$control,$mandatory,$sample_score, passed to the function
     *
     * @param $shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$spc,$probe_a,$control,$mandatory,$sample_score,
     * @return object (probe_b)| null
     */
	public function getProbeB($shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$spc,$probe_a,$control,$mandatory,$sample_score,) {
		$columns = array ('shipment_id','sample_id','sample_label','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','spc','probe_a','control','mandatory','sample_score',);
		$records = array ($shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$spc,$probe_a,$control,$mandatory,$sample_score,);
		$probe_b_ = $this->query_from_reference_result_tb ( $columns, $records );
		return sizeof($probe_b_)>0 ? $probe_b_ [0] ['probe_b'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of spc 
     * based on the value of $shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$probe_a,$control,$mandatory,$sample_score, passed to the function
     *
     * @param $shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$probe_a,$control,$mandatory,$sample_score,
     * @return object (spc)| null
     */
	public function getSpc($shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$probe_a,$control,$mandatory,$sample_score,) {
		$columns = array ('shipment_id','sample_id','sample_label','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','probe_a','control','mandatory','sample_score',);
		$records = array ($shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$probe_a,$control,$mandatory,$sample_score,);
		$spc_ = $this->query_from_reference_result_tb ( $columns, $records );
		return sizeof($spc_)>0 ? $spc_ [0] ['spc'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of probe_a 
     * based on the value of $shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$control,$mandatory,$sample_score, passed to the function
     *
     * @param $shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$control,$mandatory,$sample_score,
     * @return object (probe_a)| null
     */
	public function getProbeA($shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$control,$mandatory,$sample_score,) {
		$columns = array ('shipment_id','sample_id','sample_label','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','control','mandatory','sample_score',);
		$records = array ($shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$control,$mandatory,$sample_score,);
		$probe_a_ = $this->query_from_reference_result_tb ( $columns, $records );
		return sizeof($probe_a_)>0 ? $probe_a_ [0] ['probe_a'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of control 
     * based on the value of $shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$mandatory,$sample_score, passed to the function
     *
     * @param $shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$mandatory,$sample_score,
     * @return object (control)| null
     */
	public function getControl($shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$mandatory,$sample_score,) {
		$columns = array ('shipment_id','sample_id','sample_label','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','probe_a','mandatory','sample_score',);
		$records = array ($shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$mandatory,$sample_score,);
		$control_ = $this->query_from_reference_result_tb ( $columns, $records );
		return sizeof($control_)>0 ? $control_ [0] ['control'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of mandatory 
     * based on the value of $shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$sample_score, passed to the function
     *
     * @param $shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$sample_score,
     * @return object (mandatory)| null
     */
	public function getMandatory($shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$sample_score,) {
		$columns = array ('shipment_id','sample_id','sample_label','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','probe_a','control','sample_score',);
		$records = array ($shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$sample_score,);
		$mandatory_ = $this->query_from_reference_result_tb ( $columns, $records );
		return sizeof($mandatory_)>0 ? $mandatory_ [0] ['mandatory'] : null;
	}
	
	
	/**
     * Performs a database query and returns the value of sample_score 
     * based on the value of $shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory, passed to the function
     *
     * @param $shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,
     * @return object (sample_score)| null
     */
	public function getSampleScore($shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,) {
		$columns = array ('shipment_id','sample_id','sample_label','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','probe_a','control','mandatory',);
		$records = array ($shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,);
		$sample_score_ = $this->query_from_reference_result_tb ( $columns, $records );
		return sizeof($sample_score_)>0 ? $sample_score_ [0] ['sample_score'] : null;
	}
	

	
	/**
	* Inserts data into the table[reference_result_tb] in the order below
	* array ('shipment_id','sample_id','sample_label','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','probe_a','control','mandatory','sample_score')
	* is mappped into 
	* array ($shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score)
	* @return int 1 if data was inserted,0 otherwise
	* if redundancy check is true, it inserts if the record if it never existed else.
	* if the record exists, it returns the number of times the record exists on the relation
	*/
	public function insert_prepared_records($shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score,$redundancy_check= false, $printSQL = false) {
		$columns = array('shipment_id','sample_id','sample_label','mtb_detected','rif_resistance','probe_d','probe_c','probe_e','probe_b','spc','probe_a','control','mandatory','sample_score');
		$records = array($shipment_id,$sample_id,$sample_label,$mtb_detected,$rif_resistance,$probe_d,$probe_c,$probe_e,$probe_b,$spc,$probe_a,$control,$mandatory,$sample_score);
		return $this->insert_records_to_reference_result_tb ( $columns, $records,$redundancy_check, $printSQL );
	}

	
	/**
	* Returns the table name. This is the owner of these crud functions.
	* The various crud functions directly affect this table
	* @return string table name -> 'reference_result_tb' 
	*/
	public static function get_table() {
		return 'reference_result_tb';
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
     * Used  to calculate the number of times a record exists in the table reference_result_tb
     * It returns the number of times a record exists exists in the table reference_result_tb
     * @param array $columns
     * @param array $records
     * @param bool $printSQL
     * @return mixed
     */
	public function is_exists(Array $columns, Array $records, $printSQL = false) {
		return $this->get_database_utils ()->is_exists ( $this->get_table (), $columns, $records, $printSQL );
	}
	
	/**
     * Inserts data into the table reference_result_tb
     * if redundancy check is true, it inserts if the record if it never existed else.
     * if the record exists, it returns the number of times the record exists on the relation
     *
     * @param array $columns
     * @param array $records
     * @param bool $redundancy_check
     * @param bool $printSQL
     * @return mixed
     */
	public function insert_records_to_reference_result_tb(Array $columns, Array $records,$redundancy_check = false, $printSQL = false) {
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
	 * Deletes all the records that meets the passed criteria from the table reference_result_tb
	 * @param array $columns
	 * @param array $records
	 * @param bool $printSQL
	 * @return number of deleted rows
	 */
	public function delete_record_from_reference_result_tb(Array $columns, Array $records, $printSQL = false) {
		return $this->delete_record ( $this->get_table (), $columns, $records, $printSQL );
	}
	
	/**
	 * Updates all the records that meets the passed criteria from the table reference_result_tb
	 * 
	 * @param array $columns
	 * @param array $records
	 * @param array $where_columns
	 * @param array $where_records
	 * @param bool $printSQL
	 * @return number of updated rows
	 */
	public function update_record_in_reference_result_tb(Array $columns, Array $records, Array $where_columns, Array $where_records, $printSQL = false) {
		return $this->update_record ( $this->get_table (), $columns, $records, $where_columns, $where_records, $printSQL );
	}
	
	/**
	 * Gets an Associative array of the records in the table 'reference_result_tb' that meets the passed criteria
	 *
	 * @param $distinct
	 * @param array $columns
	 * @param array $records
	 * @param string $extraSQL
	 * @param bool $printSQL
	 * @return array|mixed associative
	 */
	public function fetch_assoc_in_reference_result_tb($distinct, Array $columns, Array $records, $extraSQL="", $printSQL = false) {
		return $this->fetch_assoc ( $distinct, $this->get_table (),$columns, $records, $extraSQL , $printSQL );
	}
	
	/**
	 * Gets an Associative array of the records in the table reference_result_tb that meets the passed criteria
	 *
	 * @param array $columns
	 * @param array $records
	 * @param string $extraSQL
	 * @param bool $printSQL
	 * @return array|mixed associative
	 */
	public function query_from_reference_result_tb(Array $columns, Array $records,$extraSQL="",  $printSQL = false) {
		return $this->query ( $this->get_table (), $columns, $records,$extraSQL,$printSQL );
	}
	
	/**
	 * Gets an Associative array of the records in the table reference_result_tb that meets the passed distinct criteria
	 * 
	 * @param array $columns
	 * @param array $records
	 * @param string $extraSQL
	 * @param bool $printSQL
	 * @return array|mixed associative
	 */
	public function query_distinct_from_reference_result_tb(Array $columns, Array $records,$extraSQL="",  $printSQL = false) {
		return $this->query_distinct ( $this->get_table (), $columns, $records,$extraSQL,$printSQL );
	}
	
	/**
	 * Performs a search in the table reference_result_tb that meets the passed criteria
	 * 
	 * @param array $columns
	 * @param array $records
	 * @param string $extraSQL
	 * @param bool $printSQL
	 * @return array|mixed associative
	 */
	public function search_in_reference_result_tb(Array $columns, Array $records,$extraSQL="", $printSQL = false) {
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
	 * Deletes all the records that meets the passed criteria from the table [reference_result_tb]
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
     * Inserts data into the table reference_result_tb
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
     * Updates all the records that meets the passed criteria from the table reference_result_tb
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
     * Gets an Associative array of the records in the table reference_result_tb that meets the passed criteria
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
     * Gets an Associative array of the records in the table reference_result_tb  that meets the passed criteria
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
     * Gets an Associative array of the records in the table reference_result_tb that meets the distinct passed criteria
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
     * Performs a search and returns an associative array of the records in the table reference_result_tb  that meets the passed criteria
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
