<?php

$dataUri = $_POST["image"];
// remove "data:image/jpeg;base64," from image Data URI.
$data = str_replace("data:image/jpeg;base64,", "", $dataUri);

$imagesDir = getcwd() ."/images";

if(!is_dir($imagesDir)) {
  // ggf. das Images-Verzeichnis erstellen
  if(!@mkdir($imagesDir, 0744)) {
    echo json_encode(["error" => "Unexpected Error"]);
    die();
  }
}

$filename = $imagesDir ."/image". date("Y-m-d_His") .".jpeg";

// Vor dem Speichern muss überprüft werden, ob das Bild valide ist,
// damit es nicht dazu kommt, dass schädliche Dateien gespeichert werden
$im = @imagecreatefromstring(base64_decode($data));
$valid = ($im != FALSE);
if(!$valid) die("No valid image!");
imagedestroy($im);

file_put_contents($filename, base64_decode($data));

$url = 'http://timpo28.pythonanywhere.com/classify';
 
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => array('image' => new CURLFILE($filename)),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
