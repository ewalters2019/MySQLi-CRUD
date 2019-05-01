<?php

// 	This class has been successfully used with:
//
//      Apache 2.4.10
//	PHP 7.0.27
//      Mysqlnd 5.0.12-dev
//      MariaDB  10.0.32


class Connection{
		

      public function __construct($host, $user, $password, $database){
            
	    $this->host = $host;
            $this->user = $user;
	    $this->password = $password;
	    $this->database = $database;
      }
		
		
      protected function connect(){
			
	return new mysqli($this->host, $this->user, $this->password, $this->database);
      }
		
      
      //////////////////////////////////////////////////////////////////////////////////////
		
		                     //CRUD FUNCTIONS
		
      
      public function query($stmt, $return_type){
                    
            if(empty($stmt) || empty($return_type)) return false;

            if(!($db = $this->connect())) return false;
			 
	    if(!$result = $db->query($stmt)) return false;
			 
            while($row = mysqli_fetch_array($result, $return_type)){

		  $data[] = $row;
	    }     
        
	return $data;
      }
		
		
      public function insert($table, $data, $format){
			
	    if(empty($table) || empty($data) || empty($format)) return false;
			
	    if(!($db = $this->connect())) return false;
			
	    $data = (array)$data;
	    $format = (array)$format;
			
	    $format = implode('', $format); 
	    $format = str_replace('%', '', $format);
			
	    list($fields, $placeholders, $values) = $this->prep_query($data, 'insert');
			
	    array_unshift($values, $format); 

	    if(!($stmt = $db->prepare("INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})"))) return false;

	    if(!(call_user_func_array(array( $stmt, 'bind_param'), $this->ref_values($values)))) return false;
			
	    if(!($stmt->execute())) return false;
			
	    if($stmt->affected_rows) return $stmt->insert_id;
        
	
	return false;
      }
      
      
      public function select($query, $data, $format, $return_type){
	
	    if(empty($query) || empty($data) || empty($format) || empty($return_type)) return false;

	    if(!($db = $this->connect())) return false;
			
	    if(!($stmt = $db->prepare($query))) return false;
			
	    $format = implode('', $format); 
	    $format = str_replace('%', '', $format);
            
            array_unshift($data, $format);
		
	    if(!(call_user_func_array(array($stmt, 'bind_param'), $this->ref_values($data)))) return false;
			
	    if(!$stmt->execute()) return false;
			
	    $data = $this->get_result($stmt, $return_type);
			
			
	return $data;
      }
		
      
      public function update($table, $data, $format, $where, $where_format){
			
		
	    if(empty($table) || empty($data) || empty($format) || empty($where) || empty($where_format)) return false;
			
	    if(!($db = $this->connect())) return false;
			
	    $data = (array)$data;
	    $format = (array)$format;
			
	    $format = implode('', $format); 
	    $format = str_replace('%', '', $format);
	    $where_format = implode('', $where_format); 
	    $where_format = str_replace('%', '', $where_format);
	    $format .= $where_format;
						
	    list($fields, $placeholders, $values ) = $this->prep_query($data, 'update');
			
	    $where_clause = '';
	    $where_values = '';
	    $count = 0;
			
	    foreach($where as $field => $value){
				
		  if($count > 0) $where_clause .= ' AND ';

		  $where_clause .= $field . '= ?';
		  $where_values[] = $value;
				
		  $count++;
	    }

	    array_unshift($values, $format);
			
	    $values = array_merge($values, $where_values);

	    if(!($stmt = $db->prepare("UPDATE {$table} SET {$placeholders} WHERE {$where_clause}"))) return false;
			
	    if(!(call_user_func_array(array($stmt, 'bind_param'), $this->ref_values($values)))) return false;
			
	    if(!($stmt->execute())) return false;
			
	    if($stmt->affected_rows) return true;
			
			
	return false;
      }
			
      
      public function delete($table, $id){
			
	    if(empty($table) || empty($id)) return false;

	    if(!($db = $this->connect())) return false;
			
	    if(!($stmt = $db->prepare("DELETE FROM {$table} WHERE ID = ?"))) return false;
			
	    if(!($stmt->bind_param('d', $id))) return false;
			
	    if(!($stmt->execute())) return false;
			
	    if($stmt->affected_rows) return true;
       
	
	return false;
      }	    
		
      
     
      //////////////////////////////////////////////////////////////////////////////////////
		
		                     //HELPER FUNCTIONS
		
      
      private function prep_query($data, $type){
			
	    $fields = '';
	    $placeholders = '';
	    $values = array();
			
	    foreach ($data as $field => $value){
				
		  $fields .= "{$field},";
		  $values[] = $value;
				
		  if($type == 'update'){
					
			$placeholders .= $field . '= ?,';
				
		  }else{
					
			$placeholders .= '?,';
		  }
		   
	    }
			
	    $fields = substr($fields, 0, -1);
	    $placeholders = substr($placeholders, 0, -1);
			
	
	return array($fields, $placeholders, $values);
      }
		
		
      private function ref_values($array){
			
	    $refs = array();

	    foreach($array as $key => $value){
				
		  $refs[$key] = &$array[$key]; 
	    }

	
	return $refs; 
      }
	   
	    
      public function get_result($stmt, $return_type){
	    
            // This is here because it is only available when using MySQL Native Driver
            // which not everybody is using, so this can elimate alot of headache
            // for those unaware 
            
            $meta = $stmt->result_metadata();
            $row = array();
            $idx = 0;
            
            while ($field = $meta->fetch_field()){
        
                if($return_type == MYSQLI_ASSOC){
                
                    $row[$field->name] = NULL;
                    $params[] = &$row[$field->name];
                   
                }else if($return_type == MYSQLI_NUM){
                
                    $row[$idx] = NULL;
                    $params[] = &$row[$idx];
                            
                    $idx++;
                }
            }

            call_user_func_array(array($stmt, 'bind_result'), $params);
                
	    while($stmt->fetch()){

		  $data[] = array_map(create_function('$a', 'return $a;'), $row);
	    }
	        
          
	return $data;
      }

      
}?>