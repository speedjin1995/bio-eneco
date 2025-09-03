<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$username = $_SESSION["username"];
}
// Check if the user is already logged in, if yes then redirect him to index page
$id = $_SESSION['id'];
$phoneNo = $_SESSION['plant'];
$faxNo = date("Y-m-d H:i:s");

// Processing form data when form is submitted
if (empty($_POST["bitumenId"])) {
    $bitumenId = null;
} else {
    $bitumenId = trim($_POST["bitumenId"]);
}

if (empty($_POST["plant"])) {
    $plant = null;
} else {
    $plant = trim($_POST["plant"]);
}

if (empty($_POST["plantCode"])) {
    $plantCode = null;
} else {
    $plantCode = trim($_POST["plantCode"]);
} 

if (empty($_POST["datetime"])) {
    $declarationDatetime = null;
} else {
    $declarationDatetime = DateTime::createFromFormat('d-m-Y H:i', $_POST["datetime"])->format('Y-m-d H:i:s');
}   

# Processing for 60/70 data
if (!empty($_POST["no"]) && count($_POST["no"]) > 0) {
    $sixtySeventyData = [];
    $no = $_POST["no"];
    $sixtyseventy = $_POST["sixtyseventy"];
    $temp = $_POST["temp"];
    $level = $_POST["level"];

    foreach ($no as $key => $value) {
        $sixtySeventyData[] = array(
            "no" => $no[$key],
            "sixtyseventy" => $sixtyseventy[$key],
            "temperature" => $temp[$key],
            "level" => $level[$key],
        );
    }

    $sixtySeventyData['totalSixtySeventy'] = $_POST["totalSixtySeventy"];
    $sixtySeventyData['totalTemperature'] = $_POST["totalTemp"];
    $sixtySeventyData['totalLevel'] = $_POST["totalLevel"];
    $sixtySeventyData = json_encode($sixtySeventyData, JSON_PRETTY_PRINT);
} else {
    $sixtySeventyData = NULL;
}

# Processing for lfo data
if (!empty($_POST["lfoNo"]) && count($_POST["lfoNo"]) > 0) {
    $lfoData = [];
    $lfoNo = $_POST["lfoNo"];
    $lfoWeight = $_POST["lfoWeight"];

    foreach ($lfoNo as $key => $no) {
        $lfoData[] = array(
            "no" => $no,
            "lfoWeight" => $lfoWeight[$key]
        );
    }

    $lfoData['totalLfo'] = $_POST["totalLfo"];
    $lfoData = json_encode($lfoData, JSON_PRETTY_PRINT);
} else {
    $lfoData = NULL;
}

# Processing for diesel data
if (!empty($_POST["dieselNo"]) && count($_POST["dieselNo"]) > 0) {
    $dieselData = [];
    $dieselNo = $_POST["dieselNo"];
    $dieselWeight = $_POST["dieselWeight"];

    foreach ($dieselNo as $key => $no) {
        $dieselData[] = array(
            "no" => $no,
            "dieselWeight" => $dieselWeight[$key]
        );
    }

    $dieselData['totalDiesel'] = $_POST["totalDiesel"];
    $dieselData = json_encode($dieselData, JSON_PRETTY_PRINT);
} else {
    $dieselData = NULL;
}

# Processing for hotoil data
if (!empty($_POST["hotoilNo"]) && count($_POST["hotoilNo"]) > 0) {
    $hotoilData = [];
    $hotoilNo = $_POST["hotoilNo"];
    $hotoilWeight = $_POST["hotoilWeight"];

    foreach ($hotoilNo as $key => $no) {
        $hotoilData[] = array(
            "no" => $no,
            "hotoilWeight" => $hotoilWeight[$key]
        );
    }

    $hotoilData['totalHotoil'] = $_POST["totalHotoil"];
    $hotoilData = json_encode($hotoilData, JSON_PRETTY_PRINT);
} else {
    $hotoilData = NULL;
}

# Processing for pg79No data
if (!empty($_POST["pg79No"]) && count($_POST["pg79No"]) > 0) {
    $pg79Data = [];
    $pg79No = $_POST["pg79No"];
    $pg79 = $_POST["pgSevenNine"];

    foreach ($pg79No as $key => $no) {
        $pg79Data[] = array(
            "no" => $no,
            "pgSevenNine" => $pg79[$key]
        );
    }

    $pg79Data['totalPgSevenNine'] = $_POST["totalPgSevenNine"];
    $pg79Data = json_encode($pg79Data, JSON_PRETTY_PRINT);
} else {
    $pg79Data = NULL;
}

if (empty($_POST["40mm"])) {
    $fortymm = null;
} else {
    $fortymm = trim($_POST["40mm"]);
} 

if (empty($_POST["28mm"])) {
    $twentyeightmm = null;
} else {
    $twentyeightmm = trim($_POST["28mm"]);
} 

if (empty($_POST["20mm"])) {
    $twenty_mm = null;
} else {
    $twenty_mm = trim($_POST["20mm"]);
} 

if (empty($_POST["14mm"])) {
    $fourteen_mm = null;
} else {
    $fourteen_mm = trim($_POST["14mm"]);
} 

if (empty($_POST["10mm"])) {
    $ten_mm = null;
} else {
    $ten_mm = trim($_POST["10mm"]);
} 

if (empty($_POST["QD"])) {
    $qd = null;
} else {
    $qd = trim($_POST["QD"]);
} 

if (empty($_POST["fibreNameMr6"])) {
    $fibreNameMr6 = null;
} else {
    $fibreNameMr6 = trim($_POST["fibreNameMr6"]);
}

if (empty($_POST["fibreTypeMr6"])) {
    $fibreTypeMr6 = 0.00;
} else {
    $fibreTypeMr6 = trim($_POST["fibreTypeMr6"]);
}

if (empty($_POST["fibreBagsMr6"])) {
    $fibreBagsMr6 = 0;
} else {
    $fibreBagsMr6 = trim($_POST["fibreBagsMr6"]);
}

if (empty($_POST["fibreQtyMr6"])) {
    $fibreQtyMr6 = 0.00;
} else {
    $fibreQtyMr6 = trim($_POST["fibreQtyMr6"]);
}

if (empty($_POST["fibreNameRpf"])) {
    $fibreNameRpf = null;
} else {
    $fibreNameRpf = trim($_POST["fibreNameRpf"]);
}

if (empty($_POST["fibreTypeRpf"])) {
    $fibreTypeRpf = 0.00;
} else {
    $fibreTypeRpf = trim($_POST["fibreTypeRpf"]);
}

if (empty($_POST["fibreBagsRpf"])) {
    $fibreBagsRpf = 0;
} else {
    $fibreBagsRpf = trim($_POST["fibreBagsRpf"]);
}

if (empty($_POST["fibreQtyRpf"])) {
    $fibreQtyRpf = 0.00;
} else {
    $fibreQtyRpf = trim($_POST["fibreQtyRpf"]);
}

if (empty($_POST["fibreNameNova"])) {
    $fibreNameNova = null;
} else {
    $fibreNameNova = trim($_POST["fibreNameNova"]);
}

if (empty($_POST["fibreTypeNova"])) {
    $fibreTypeNova = 0.00;
} else {
    $fibreTypeNova = trim($_POST["fibreTypeNova"]);
}

if (empty($_POST["fibreBagsNova"])) {
    $fibreBagsNova = 0;
} else {
    $fibreBagsNova = trim($_POST["fibreBagsNova"]);
}

if (empty($_POST["fibreQtyNova"])) {
    $fibreQtyNova = 0.00;
} else {
    $fibreQtyNova = trim($_POST["fibreQtyNova"]);
}

if (empty($_POST["fibreNameForta"])) {
    $fibreNameForta = null;
} else {
    $fibreNameForta = trim($_POST["fibreNameForta"]);
}

if (empty($_POST["fibreTypeForta"])) {
    $fibreTypeForta = 0.00;
} else {
    $fibreTypeForta = trim($_POST["fibreTypeForta"]);
}

if (empty($_POST["fibreBagsForta"])) {
    $fibreBagsForta = 0;
} else {
    $fibreBagsForta = trim($_POST["fibreBagsForta"]);
}

if (empty($_POST["fibreQtyForta"])) {
    $fibreQtyForta = 0.00;
} else {
    $fibreQtyForta = trim($_POST["fibreQtyForta"]);
}

# Processing for fibreNo data
if (!empty($_POST["fibreNo"]) && count($_POST["fibreNo"]) > 0) {
    $fibreData = [];
    $fibreNo = $_POST["fibreNo"];
    $fibreName = $_POST["fibreName"];
    $fibreType = $_POST["fibreType"];
    $fibreNoOfBags = $_POST["fibreNoOfBags"];
    $fibreQty = $_POST["fibreQty"];

    $fibreData[] = array(
        "fibreNameMr6" => $fibreNameMr6,
        "fibreTypeMr6" => $fibreTypeMr6,
        "fibreBagsMr6" => $fibreBagsMr6,
        "fibreQtyMr6" => $fibreQtyMr6,
    );
    $fibreData[] = array(
        "fibreNameRpf" => $fibreNameRpf,
        "fibreTypeRpf" => $fibreTypeRpf,
        "fibreBagsRpf" => $fibreBagsRpf,
        "fibreQtyRpf" => $fibreQtyRpf,
    );
    $fibreData[] = array(
        "fibreNameNova" => $fibreNameNova,
        "fibreTypeNova" => $fibreTypeNova,
        "fibreBagsNova" => $fibreBagsNova,
        "fibreQtyNova" => $fibreQtyNova,
    );
    $fibreData[] = array(
        "fibreNameForta" => $fibreNameForta,
        "fibreTypeForta" => $fibreTypeForta,
        "fibreBagsForta" => $fibreBagsForta,
        "fibreQtyForta" => $fibreQtyForta,
    );

    foreach ($fibreNo as $key => $no) {
        $fibreData[] = array(
            "no" => $no,
            "fibreName" => $fibreName[$key],
            "fibreType" => $fibreType[$key],
            "fibreBags" => $fibreNoOfBags[$key],
            "fibreQty" => $fibreQty[$key],
        );
    }

    $fibreData = json_encode($fibreData, JSON_PRETTY_PRINT);
} else {
    $fibreData = [];

    $fibreData[] = array(
        "fibreNameMr6" => $fibreNameMr6,
        "fibreTypeMr6" => $fibreTypeMr6,
        "fibreBagsMr6" => $fibreBagsMr6,
        "fibreQtyMr6" => $fibreQtyMr6,
    );
    $fibreData[] = array(
        "fibreNameRpf" => $fibreNameRpf,
        "fibreTypeRpf" => $fibreTypeRpf,
        "fibreBagsRpf" => $fibreBagsRpf,
        "fibreQtyRpf" => $fibreQtyRpf,
    );
    $fibreData[] = array(
        "fibreNameNova" => $fibreNameNova,
        "fibreTypeNova" => $fibreTypeNova,
        "fibreBagsNova" => $fibreBagsNova,
        "fibreQtyNova" => $fibreQtyNova,
    );
    $fibreData[] = array(
        "fibreNameForta" => $fibreNameForta,
        "fibreTypeForta" => $fibreTypeForta,
        "fibreBagsForta" => $fibreBagsForta,
        "fibreQtyForta" => $fibreQtyForta,
    );

    $fibreData = json_encode($fibreData, JSON_PRETTY_PRINT);
}

if (empty($_POST["rs1k"])) {
    $rs1k = null;
} else {
    $rs1k = trim($_POST["rs1k"]);
} 

if (empty($_POST["k140"])) {
    $k140 = null;
} else {
    $k140 = trim($_POST["k140"]);
} 

if (empty($_POST["ss1k"])) {
    $ss1k = null;
} else {
    $ss1k = trim($_POST["ss1k"]);
} 

if (empty($_POST["others"])) {
    $others = null;
} else {
    $others = trim($_POST["others"]);
} 

if (empty($_POST["transport"])) {
    $transport = null;
} else {
    $transport = trim($_POST["transport"]);
} 

if (empty($_POST["burner"])) {
    $burner = null;
} else {
    $burner = trim($_POST["burner"]);
} 

if (empty($_POST["opcDo"])) {
    $opcDo = null;
} else {
    $opcDo = trim($_POST["opcDo"]);
} 

if (empty($_POST["opcIncoming"])) {
    $opcIncoming = null;
} else {
    $opcIncoming = trim($_POST["opcIncoming"]);
} 

if (empty($_POST["opcQty"])) {
    $opcQty = null;
} else {
    $opcQty = trim($_POST["opcQty"]);
} 

if (empty($_POST["limeDo"])) {
    $limeDo = null;
} else {
    $limeDo = trim($_POST["limeDo"]);
} 

if (empty($_POST["limeIncoming"])) {
    $limeIncoming = null;
} else {
    $limeIncoming = trim($_POST["limeIncoming"]);
} 

if (empty($_POST["limeQty"])) {
    $limeQty = null;
} else {
    $limeQty = trim($_POST["limeQty"]);
} 

# Processing for data
$data = array(
    "40mm" => $fortymm,
    "28mm" => $twentyeightmm,
    "20mm" => $twenty_mm,
    "14mm" => $fourteen_mm,
    "10mm" => $ten_mm,
    "QD" => $qd,
    "rs1k" => $rs1k,
    "k140" => $k140,
    "ss1k" => $ss1k,
    "others" => $others,
    "transport" => $transport,
    "burner" => $burner,
    "opcDo" => $opcDo,
    "opcIncoming" => $opcIncoming,
    "opcQty" => $opcQty,
    "limeDo" => $limeDo,
    "limeIncoming" => $limeIncoming,
    "limeQty" => $limeQty,
);

$data = json_encode($data, JSON_PRETTY_PRINT);

if(!empty($bitumenId)){
    if ($update_stmt = $db->prepare("UPDATE Bitumen SET `60/70`=?, `pg76`=?, `lfo`=?, `diesel`=?, `hotoil`=?, `fibre`=?, `data`=?, `declaration_datetime`=?, `plant_id`=?, `plant_code`=?, modified_by=? WHERE id=?")) {
        $update_stmt->bind_param('ssssssssssss', $sixtySeventyData, $pg79Data, $lfoData, $dieselData, $hotoilData, $fibreData, $data, $declarationDatetime, $plant, $plantCode, $username, $bitumenId);

        // Execute the prepared query.
        if (! $update_stmt->execute()) {
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> $update_stmt->error
                )
            );
        }
        else{
            $update_stmt->close();
            $db->close();

            echo json_encode(
                array(
                    "status"=> "success", 
                    "message"=> "Updated Successfully!!" 
                )
            );
        }
    }
}
else
{ 
    if ($insert_stmt = $db->prepare("INSERT INTO Bitumen (`60/70`, `pg76`, `lfo`, `diesel`, `hotoil`, `fibre`, `data`, `declaration_datetime`, `plant_id`, `plant_code`, `created_by`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
        $insert_stmt->bind_param('sssssssssss', $sixtySeventyData, $pg79Data, $lfoData, $dieselData, $hotoilData, $fibreData, $data, $declarationDatetime, $plant, $plantCode, $username);

        // Execute the prepared query.
        if (! $insert_stmt->execute()) {
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> $insert_stmt->error
                )
            );
        }
        else{
            echo json_encode(
                array(
                    "status"=> "success", 
                    "message"=> "Added Successfully!!" 
                )
            );

            $insert_stmt->close();
            $db->close();
            
        }
    }
}
?>