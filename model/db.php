<?php  

$username ="root";
$password = "Annu#2005";
$host ="localhost";
$dbname = "lost_found";

$conn= mysqli_connect($host,$username, $password,$dbname);
 
if($conn){
  //  Echo"Connected Succefully";
} 
else{
    Echo"Connection Failed";
   
}
?>