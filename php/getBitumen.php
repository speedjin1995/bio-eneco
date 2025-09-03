<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * from Bitumen WHERE id=?")) {
        $update_stmt->bind_param('s', $id);
        
        // Execute the prepared query.
        if (! $update_stmt->execute()) {
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Something went wrong"
                )); 
        }
        else{
            $result = $update_stmt->get_result();
            $message = array();
            
            while ($row = $result->fetch_assoc()) {
                $message['id'] = $row['id'];

                ## 60/70 Processing ##
                $sixtySeventyTemp = json_decode($row['60/70'], true);
                $sixtySeventyRows = [];
                if (!empty($sixtySeventyTemp)) {
                    foreach ($sixtySeventyTemp as $sixtySeventyKey => $sixtySeventyRow) {
                        if (is_numeric($sixtySeventyKey)) {
                            $sixtySeventyRows[] = $sixtySeventyRow;
                        }
                    }
                }
                $message['sixtysevn'] = $sixtySeventyRows;
                $message['totalSixtySeventy'] = $sixtySeventyTemp["totalSixtySeventy"] ?? 0;
                $message['totalTemp'] = $sixtySeventyTemp["totalTemperature"] ?? 0;
                $message['totalLevel'] = $sixtySeventyTemp["totalLevel"] ?? 0;
                ########################################################################################

                ## lfo Processing ##
                $lfoTemp = json_decode($row['lfo'], true);
                $lfoRows = [];
                if (!empty($lfoTemp)) {
                    foreach ($lfoTemp as $lfoKey => $lfoRow) {
                        if (is_numeric($lfoKey)) {
                            $lfoRows[] = $lfoRow;
                        }
                    }
                }
                $message['lfo'] = $lfoRows;
                $message['totalLfo'] = $lfoTemp["totalLfo"] ?? 0;
                ########################################################################################

                ## diesel Processing ##
                $dieselTemp = json_decode($row['diesel'], true);
                $dieselRows = [];
                if (!empty($dieselTemp)) {
                    foreach ($dieselTemp as $dieselKey => $dieselRow) {
                        if (is_numeric($dieselKey)) {
                            $dieselRows[] = $dieselRow;
                        }
                    }
                }
                $message['diesel'] = $dieselRows;
                $message['totalDiesel'] = $dieselTemp["totalDiesel"] ?? 0;
                ########################################################################################

                ## hotoil Processing ##
                $hotoilTemp = json_decode($row['hotoil'], true);
                $hotoilRows = [];
                if (!empty($hotoilTemp)) {
                    foreach ($hotoilTemp as $hotoilKey => $hotoilRow) {
                        if (is_numeric($hotoilKey)) {
                            $hotoilRows[] = $hotoilRow;
                        }
                    }
                }
                $message['hotoil'] = $hotoilRows;
                $message['totalHotoil'] = $hotoilTemp["totalHotoil"] ?? 0;
                ########################################################################################

                ## pg76 Processing ##
                $pg76Temp = json_decode($row['pg76'], true);
                $pg76Rows = [];
                if (!empty($pg76Temp)) {
                    foreach ($pg76Temp as $pg76Key => $pg76Row) {
                        if (is_numeric($pg76Key)) {
                            $pg76Rows[] = $pg76Row;
                        }
                    }
                } 
                $message['pgSeventyNine'] = $pg76Rows;
                $message['totalPgSevenNine'] = $pg76Temp["totalPgSevenNine"] ?? 0;
                ########################################################################################

                ## fibre Processing ##
                $fibreTemp = json_decode($row['fibre'], true);
                $message['fibreNameMr6'] = $fibreTemp[0]['fibreNameMr6'] ?? 'MR6';
                $message['fibreTypeMr6'] = $fibreTemp[0]['fibreTypeMr6'] ?? 0.00;
                $message['fibreBagsMr6'] = $fibreTemp[0]['fibreBagsMr6'] ?? 0;
                $message['fibreQtyMr6'] = $fibreTemp[0]['fibreQtyMr6'] ?? 0.00;
                $message['fibreNameRpf'] = $fibreTemp[1]['fibreNameRpf'] ?? 'RPF';
                $message['fibreTypeRpf'] = $fibreTemp[1]['fibreTypeRpf'] ?? 0.00;
                $message['fibreBagsRpf'] = $fibreTemp[1]['fibreBagsRpf'] ?? 0;
                $message['fibreQtyRpf'] = $fibreTemp[1]['fibreQtyRpf'] ?? 0.00;
                $message['fibreNameNova'] = $fibreTemp[2]['fibreNameNova'] ?? 'Nova Fiber';
                $message['fibreTypeNova'] = $fibreTemp[2]['fibreTypeNova'] ?? 0.00;
                $message['fibreBagsNova'] = $fibreTemp[2]['fibreBagsNova'] ?? 0;
                $message['fibreQtyNova'] = $fibreTemp[2]['fibreQtyNova'] ?? 0.00;
                $message['fibreNameForta'] = $fibreTemp[3]['fibreNameForta'] ?? 'Forta Fiber';
                $message['fibreTypeForta'] = $fibreTemp[3]['fibreTypeForta'] ?? 0.00;
                $message['fibreBagsForta'] = $fibreTemp[3]['fibreBagsForta'] ?? 0;
                $message['fibreQtyForta'] = $fibreTemp[3]['fibreQtyForta'] ?? 0.00;

                // Unset first 4 elements in the array for the default fibre data
                unset($fibreTemp[0]);
                unset($fibreTemp[1]);
                unset($fibreTemp[2]);
                unset($fibreTemp[3]);

                $fibreRows = [];
                if (!empty($fibreTemp)) {
                    foreach ($fibreTemp as $fibreKey => $fibreRow) {
                        if (is_numeric($fibreKey)) {
                            $fibreRows[] = $fibreRow;
                        }
                    }
                }

                $message['fibre'] = $fibreRows;
                ########################################################################################

                ## Data Processing ##
                $data = json_decode($row['data'], true);
                $message['fortymm'] = $data['40mm'] ?? 0;
                $message['twentyeightmm'] = $data['28mm'] ?? 0;
                $message['twentyMM'] = $data['20mm'] ?? 0;
                $message['fourteenMM'] = $data['14mm'] ?? 0;
                $message['tenMM'] = $data['10mm'] ?? 0;
                $message['QD'] = $data['QD'] ?? 0;
                $message['rs1k'] = $data['rs1k'] ?? 0;
                $message['k140'] = $data['k140'] ?? 0;
                $message['ss1k'] = $data['ss1k'] ?? 0;
                $message['others'] = $data['others'] ?? 0;
                $message['transport'] = $data['transport'] ?? 0;
                $message['burner'] = $data['burner'] ?? 0;
                $message['opcDo'] = $data['opcDo'] ?? 0;
                $message['opcIncoming'] = $data['opcIncoming'] ?? 0;
                $message['opcQty'] = $data['opcQty'] ?? 0;
                $message['limeDo'] = $data['limeDo'] ?? 0;
                $message['limeIncoming'] = $data['limeIncoming'] ?? 0;
                $message['limeQty'] = $data['limeQty'] ?? 0;

                $message['crmb'] = $row['crmb'];
                $message['plant_id'] = $row['plant_id'];
                $message['plant_code'] = $row['plant_code'];
                $message['declaration_datetime'] = $row['declaration_datetime'];
            }
            
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
                ));   
        }
    }
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
            )); 
}
?>