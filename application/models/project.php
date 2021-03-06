<?php

class Project extends CI_Model {

    var $title   = '';
    var $content = '';
    var $date    = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_documents($num=20,$start=0)
    {
		$this->db->select();
		$this->db->from('DocUploaded');  
		$this->db->order_by('doc_id');  
		$this->db->limit($num, $start);
		$query = $this->db->get();
        
        return $query->result_array();
    }
    
    function get_document_entry($id)
    {  
		$this->db->where('ID', $id);
		$this->db->from('DocUploaded');
        	$query = $this->db->get();
        return $query->result_array();
    }
    
    function get_doc_count(){
    	$this->db->select('doc_id');
		$this->db->from('DocUploaded');
		$query = $this->db->get();
     
     return $query->num_rows();
   }
    
    function checkDoc($val){
	$this->db->where('doc_id',$val);    
      return $this->db->count_all_results('DocUploaded');
    }
    
    function insert_document($data)
    {
        $this->db->insert('DocUploaded', $data);
        return $this->db->insert_id();
    }
    
    function update_document($data)
    {
	  $this->db->where('ID', $data['ID']);        
        $this->db->update('DocUploaded', $data);
    }
	
    function get_entityType()
    {
		$this->db->select();
		$this->db->from('EntityType'); 
		//$this->db->limit(300);
		$query = $this->db->get();
        return $query->result_array();
    }
	
    function get_entities()
    {
		$this->db->select();
		$this->db->from('Entity'); 
		$this->db->limit(300);
		$query = $this->db->get();
        return $query->result_array();
    }
    
    function update_entity($data)
    {
	  $this->db->where('ID', $data['ID']);        
        $this->db->update('Entity', $data);
    }
   
    function insert_entity($data)
    {
    	$org = str_replace(',', '', $data['entity_organisation'][0]);
		$org_type_id = $data['entity_organisation_type'];
    	//echo ($org);
    	$context = $data['entity_context'];
    	$DocID = $data['entity_DocID'];
		$persons = $data['entity_persons'];
		$person_type_id = $data['entity_persons_type'];
		$effectDate = $data['effect_date'];
		$effectDate = str_replace('the','',$effectDate);	
		$effectDate = (empty($effectDate)) ? 'NOT AVAILABLE': $effectDate;	
		$gazDate = (empty($data['gazette_date'])) ? 'NOT AVAILABLE': $data['gazette_date'];
		$gazAppointer = (empty($data['gazette_appointer'])) ? 'NOT AVAILABLE': $data['gazette_appointer'];
		$gazOffice = (empty($data['gazette_office'])) ? 'NOT AVAILABLE': $data['gazette_office'];
				
//insert organisations  	
    $this->db->select();
	$this->db->from('Entity');
	$this->db->where('Name', $org);
	$query = $this->db->get();
	// echo $this->db->last_query();
	 if ($query->num_rows() > 0){ 
	 	$row = $query->row(); 
		$OrgID =$row->ID;
	 } else {
	 	$this->db->insert('Entity', array('EntityTypeID'=>$org_type_id,'Name'=>$org,'EntityContext'=>$context,'DocID'=>$DocID ));
        $OrgID = $this->db->insert_id();
	 }
	    
//insert persons
//var_dump($data['entity_persons']);
    	if (is_array($data['entity_persons'])){
    		$toOrgID ='';
    		foreach ($data['entity_persons'] as $name){
    		//echo $name .'tuko';
    		$name = str_replace(',', '', $name);
    		$this->db->select();
			$this->db->from('Entity');
	    	$this->db->where('Name', $name);
			$query = $this->db->get();
		//echo $this->db->last_query();
			 if ($query->num_rows() > 0){ 
			 	$row = $query->row(); 
				$NameID =$row->ID;
			 	$this->db->set('EntityMap', "CONCAT(EntityMap,'||','".$OrgID."')", FALSE);
			 	$this->db->set('EntityContext', "CONCAT(EntityContext,'||','".$context."')", FALSE);
			 	$this->db->set('DocID', "CONCAT(DocID,'||','".$DocID."')", FALSE);
			 	$this->db->where('ID', $NameID);        
       			$this->db->update('Entity',array('EffectiveDate'=> $effectDate, 'GazetteDate' => $gazDate, 'GazetteAppointer' => $gazAppointer,'GazetteOffice' =>$gazOffice));
			 	
			 } else {
			 	$this->db->set('EntityMap', "CONCAT(EntityMap,'||','".$OrgID."')", FALSE);
			 	$this->db->set('EntityContext', "CONCAT(EntityContext,'||','".$context."')", FALSE);
			 	$this->db->set('DocID', "CONCAT(DocID,'||','".$DocID."')", FALSE);
			 	$this->db->insert('Entity', array('Name'=>$name,'EntityTypeID'=>$person_type_id,'EffectiveDate'=> $effectDate, 'GazetteDate' => $gazDate, 'GazetteAppointer' => $gazAppointer,'GazetteOffice' =>$gazOffice));
		        	$NameID = $this->db->insert_id();
			 }
		//	 echo $this->db->last_query();
			 $toOrgID .=  $NameID .'||';
			 //echo $toOrgID;
		}
			if(strlen($toOrgID)>3){
			  	$this->db->where('ID', $OrgID);        
		        $this->db->update('Entity', array('EntityMap' => $toOrgID));
		        //echo $this->db->last_query();
	      	}
    	}
    	
    }

    function get_entries($tag,$var)
    {
		$this->db->select();
		$this->db->from($tag);
		$this->db->like($tag,$var);  
		$this->db->limit(30);   
		//if($this->db->count_all_results()>0){  
	        $query = $this->db->get();
	        return $query->result_array();
	      //} else {return '';}
    }
    
    
    function get_entry2($tag,$docid)
    {
		$this->db->select();
		$this->db->from($tag);
		$this->db->where('DocID',$docid);  
		$this->db->limit(6);     
        $query = $this->db->get();
        return $query->result_array();
    }
    
    

}