<?php
        $m = new MongoDB\Driver\Manager("mongodb://localhost:27017");
        echo "connection finally successful";

        #$db = $m->test;
        #var_dump($m);

        #$stats = new MongoDB\Driver\Command(["dbstats" => 1]);
        #$res = $m->executeCommand("graphql_servers", $stats);
    
    #$stats = current($res->toArray());

    #print_r($stats);

    /*
    $listdatabases = new MongoDB\Driver\Command(["listDatabases" => 1]);
    $res = $m->executeCommand("admin", $listdatabases);

    $databases = current($res->toArray());

    foreach ($databases->databases as $el) {
    
        echo $el->name . "\n";
    }

    $query = new MongoDB\Driver\Query([]); 
     
    $rows = $m->executeQuery("graphql_servers.test1", $query);
    
    foreach ($rows as $row) {
    
        echo "$row->name : $row->price\n";
    }*/


    $bulk = new MongoDB\Driver\BulkWrite;

    $redditSchema = file_get_contents('./redditSchema.txt');
    $githubSchema = file_get_contents('./githubSchema.txt');

    $redditServer = ['server_name' => 'Reddit', 'URL' => 'http://localhost:51880/', 'slug' => 'reddit', 'description' => 'Reddit GraphQL Server', 'schema' => $redditSchema, 'requires_authorization' => 1, 'requires_authentication' => 0];

    $githubServer = ['server_name' => 'Github', 'URL' => 'http://localhost:53339/', 'slug' => 'github', 'description' => 'Github GraphQL Server', 'schema' => $githubSchema, 'requires_authorization' => 1, 'requires_authentication' => 0];

    $bulk->insert($redditServer);
    //$m->executeBulkWrite("graphql_servers.test1", $bulk);

    $bulk->insert($githubServer);
    $m->executeBulkWrite("graphql_servers.test1", $bulk);



?>