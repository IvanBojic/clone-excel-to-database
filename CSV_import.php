<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    ini_set('memory_limit', '2G');
    // učitavanje biblioteke PHPSpreadsheet
    require 'C:\\Apache24\\htdocs\\ProhromBiScript\\vendor\\autoload.php';


    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

    // putanja do foldera sa XLSX datotekama
    $folder_path = "D:\\infosys\\Croonus\\";

    // konekcija sa bazom
    $servername = "192.168.0.253";
    $username = "sa";
    $password = "prohrom***032";
    $dbname = "prohrom_data";

    // povezivanje sa bazom podataka
    $conn = new PDO("sqlsrv:Server=$servername;Database=$dbname", $username, $password);

    // podesavanje PDO opcija
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!$conn) {
        die("Neuspela konekcija sa bazom podataka.");
    } else {
        echo "Uspesno povezivanje sa bazom podataka.";
    }

    $files = scandir($folder_path);
    $files = array_diff($files, ['.', '..']);

    foreach ($files as $file) {
        $filename = basename($file, '.xlsx');

        $tableExists = $conn->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_name = '".$filename."'");
        if($tableExists->fetchColumn() == 1) {
            $result = $conn->query("SELECT MAX(id) FROM ".$filename);
            $maxId = $result->fetch()[0];
        } else {
            continue;
        }

        $file_path = $folder_path . $file;

        // Provera da li je fajl kreiran u poslednjih 24 sata
        $twentyFourHoursAgo = strtotime('-24 hours');

        $fileCreationTime = filectime($file_path);



        if ($fileCreationTime > $twentyFourHoursAgo) {
            continue;
        }

        $spreadsheet = IOFactory::load($file_path);
        $worksheet = $spreadsheet->getActiveSheet();

        $highestColumnIndex = Coordinate::columnIndexFromString($worksheet->getHighestColumn());
        $rowCount = $worksheet->getHighestDataRow();

        $headerValues = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $headerValue = $worksheet->getCellByColumnAndRow($col, 1)->getValue();
            array_push($headerValues, $headerValue);
        }

        // Brisanje podataka iz tabele u bazi pre importa
        $conn->query("TRUNCATE TABLE ".$filename);

        // Import podataka iz CSV fajla u tabelu u bazi
        for ($row = 2; $row <= $rowCount; ++$row) {
            $rowValues = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $rowValue = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
            array_push($rowValues, $rowValue);
            }

            // Unos reda podataka u tabelu	    
            $sql = "INSERT INTO ".$filename;
            $sql .= " VALUES (";
            $sql .= implode(", ", array_fill(0, count($headerValues), "?"));
            $sql .= ");";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $maxId, PDO::PARAM_INT);
            for ($i = 0; $i < count($rowValues); $i++) {
                $stmt->bindValue(($i + 1), $rowValues[$i]);
            }

            $stmt->execute();
        }

        echo "Fajl ".$file." je uspešno importovan u tabelu ".$filename.".<br>";
        }
        
        $conn = null;
        ?>