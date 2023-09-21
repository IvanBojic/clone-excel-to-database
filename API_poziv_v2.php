<?php
// postavite URL adresu API servera
$url = 'http://192.168.0.253:31000/InfoAPI.aspx';

ini_set('default_socket_timeout', 10000); // Set the timeout to 10 seconds

$jsonData = array(
    'app_option' => 'get_prohrom_pbi',
    'data' => '',
    'req_id' => 'DOTEST_4S60QRPEI',
    'instance_id' => '',
    'password' => 'demo',
    'session_id' => '',
    'username' => 'info',
    'client_id' => 'DEMO',
    'checksum' => '9beeaf5b585bb5b5b022559e1213a467'
);

// podaci koje želite da pošaljete serveru
$data = array(
    'json' => json_encode($jsonData),
    'DebugLevel' => 0,
    'DebugSection' => 'SYSTEM,CLASS',
    'APIVersion' => 1
);

// postavite opcije za curl
// $options = array(
//     CURLOPT_URL => $url,
//     CURLOPT_POST => true,
//     CURLOPT_POSTFIELDS => json_encode($data),
//     CURLOPT_RETURNTRANSFER => true,
//     CURLOPT_HTTPHEADER => array(
//         'Content-Type: application/x-www-form-urlencoded'
//     )
// );

$data = http_build_query($data);
$headers = array(
    'Content-Type: application/x-www-form-urlencoded',
    'Content-Length: ' . strlen($data)
);

$options = array(
    'http' => array(
        'header'  => implode("\r\n", $headers),
        'method'  => 'POST',
        'content' => $data,
    )
);

// Make the POST request
$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === false) {
    echo "Poziv nije uspeo.";
} else {
    echo $result;
}

// inicijalizujte curl
// $curl = curl_init();

// // postavite opcije curl-a
// curl_setopt_array($curl, $options);

// // izvršite curl i dobijte rezultat
// $result = curl_exec($curl);

// // proverite da li postoji greška
// if ($result === false) {
//     echo 'Curl error: ' . curl_error($curl);
// }

// // // zatvorite curl
// curl_close($curl);

// // prikažite rezultat
// echo $result;
?>