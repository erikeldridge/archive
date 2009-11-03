//See if there's a record for this key.
$sql = sprintf(
    "SELECT COUNT(*) FROM `table1` 
    WHERE `key` = '%s';", 
    $input['key']
);
$result = $mysqli->query($sql)->fetch_row();
$input['value'] = $mysqli->escape_string($input['value']);

//If there isn't a record.
if (0 == $result[0]) {
    $sql = sprintf(
        
        //we use INSERT and not REPLACE because we don't use key as the primary key
        "INSERT INTO 
        `table1` (`primary`, `key`, `value`, `created`, `updated`) 
        VALUES (NULL, '%s', '%s', NOW(), NOW());", 
        $input['key'], $input['value']
    );
    
//If there is a record.
}else{
    $sql = sprintf(
        "UPDATE `table1`
        SET `value` = '%s'
        WHERE `key` = '%s';",
        $input['value'], $input['key']
    );
}

//Either way, return the record.
$sql .= sprintf(
    "SELECT `value` FROM `table1` WHERE `key` = '%s';", 
    $input['key']
);
$result = runMultiQuery($mysqli, $sql);
$response = array(
    'status' => 'success',
    'value' => html_entity_decode(stripslashes($result[1][0]['value']))
);