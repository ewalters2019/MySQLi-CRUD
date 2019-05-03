<?php 

      
      // this is not meant to be a logical script as it is just example of 
      // how to use the methods 
      
      
      require_once('Connection.php');    


      $db = new Connection("localhost", "test", "test", "test");
      
      
      //////////////////////////////////////////////////////////////////////
      
				  //CREATE
      
      ///*
      
      
      $table = "users";
      
      $data = array(
		     'username'   => "ewalters2019", 
		     'password'   => "None0fYaBiZ", 
		     'city'       => "port angeles",
		     'state'      => "wa",
		     'gender'     => "male",
		     'age'        => "39",
		     'status'     => "active"
		   );
      
      $format = array("%s","%s","%s","%s","%s","%s","%s"); 
      
      
      
      $id = $db->insert($table, $data, $format);    

      
      if($id == false){ echo "Record creation failed"; }else{ echo "Record id ".$id." created"; } 
      
      
      //*/
      
      //////////////////////////////////////////////////////////////////////
      
				  //READ
     
      
      /*
      
      
      $data = array("male", "21");
     
      $format = array("%s","%s"); 

      
      
      $stmt = "SELECT * FROM `users` WHERE `gender` = ? OR `age` > ?";
      
      $results = $db->select($stmt, $data, $format, MYSQLI_ASSOC);
      
      
      
      
      if($results == false){ 
      
	  echo "Read failed";
	  
      }else{	  
      
	  echo "RESULTS:"."</br></br>";
      
	  print_r($results);
      }
      
      
      */
      
      //////////////////////////////////////////////////////////////////////
      
				  //UPDATE
     
      /*
      
      
      $table = "users";
      
      $data = array('status' => "inactive");
     
      $format = array("%s"); 
      
      $where = array('id' => "1");
     
      $where_format = array("%d"); 
      
      
      
      $updated = $db->update($table, $data, $format, $where, $where_format);  

      
      if($updated == false){ echo "Record update failed"; }else{ echo "Record update success"; } 

      
      */
      
      //////////////////////////////////////////////////////////////////////
      
				  //DELETE
     
      /*
      
      
      $table = "users";

      $id = "1";
      
      
      
      $deleted = $db->delete($table, $id);  

      
      if($deleted == false){ echo "Record delete failed"; }else{ echo "Record delete successful"; } 

      
      
      */
      
      
      /* NOTE */
      
      // Deleting records from a table that uses auto-increment
      // is not exactly a good thing to do as it screws things up!
      
      // It can be overcome, but still, it just creates a mess 
      // to deal with. 
      
      
     //////////////////////////////////////////////////////////////////////
				  
				  //QUERY
      
      /*
      
      
      $stmt = "SELECT * FROM `users` ORDER BY `id` ASC";
      
      $results = $db->query($stmt, MYSQLI_ASSOC); 

            
      
      if($results == false){ 
      
	  echo "Query failed";
	  
      }else{	  
      
	  echo "RESULTS:"."</br></br>";
      
	  print_r($results);
      }
      
      
      
      */
      
      //////////////////////////////////////////////////////////////////////

?>
