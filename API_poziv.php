<?php
// postavite URL adresu API servera
$url = 'http://localhost:31000';

// podaci koje želite da pošaljete serveru
$data = array(
  "app_option": "get_prohrom_pbi",
  "data": "",
  "req_id": "DOTEST_4S60QRPEI",
  "instance_id": "",
  "password": "demo",
  "session_id": "",
  "username": "info",
  "client_id": "DEMO",
  "checksum": "9beeaf5b585bb5b5b022559e1213a467",
);

// postavite opcije za curl
$options = array(
    CURLOPT_URL => $url,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
    )
);

// inicijalizujte curl
$curl = curl_init();

// postavite opcije curl-a
curl_setopt_array($curl, $options);

// izvršite curl i dobijte rezultat
$result = curl_exec($curl);

// proverite da li postoji greška
if ($result === false) {
    echo 'Curl error: ' . curl_error($curl);
}

// zatvorite curl
curl_close($curl);

// prikažite rezultat
echo $result;
?>