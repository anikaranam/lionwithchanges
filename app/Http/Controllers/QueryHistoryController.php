<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class QueryHistoryController extends Controller
{
	public function sayHello() {
        echo 'hellooo ANIIIII';


        $servername = "localhost";
        $username = "root";
        $password = "anrMar#18";
        $dbname = "hello_world";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM queries";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            echo "success!!:))";
        } else {
            echo "0 results";
        }
        $conn->close();
    }
}





