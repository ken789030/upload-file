<?php
$jsonString = '';
if (isset($_FILES) && $_FILES) {
    include_once "config.php";
    $jsonString = file_get_contents($_FILES['file']['tmp_name']);
    
    $robotList = json_decode($jsonString, true);
    // print_r($jsonString);
    $robots = $robotList['RobotList'];
    $total = count($robots);
    
    $statis = [
        "BaseBody" => [],
        "Head" => [],
        "Shoulder" => [],
        "Arms" => [],
        'LowerBody' => [],
        'MainColor' => [],
        'Rank' => []
    ];
    foreach ($robots as $key => $robot) {
        foreach ($robot as $name => $part) {
            if ($name != 'Name') {
                if (!$statis[$name][$part]) {
                    $statis[$name][$part] = 1;
                } else {
                    $statis[$name][$part] += 1;
                }
            }
        }
    }
    // print_r(json_encode($statis));


    
    // print_r($total);
    $htmlString = "";
    foreach ($statis as $key => $types) {
        $htmlString .= "<tr><td>".$key."種類</td><td>數量</td><td>機率</td></tr>";
        foreach ($types as $typeKey => $type) {
            $typeKeyString = $typeKey;
            switch ($key) {
                case 'BaseBody':
                    $typeKeyString = $basebody[$typeKey];
                    break;
                case 'Head':
                    $typeKeyString = $head[$typeKey];
                    break;
                case 'Shoulder':
                    $typeKeyString = $shoulder[$typeKey];
                    break;
                case 'Arms':
                    $typeKeyString = $arms[$typeKey];
                    break;
                case 'LowerBody':
                    $typeKeyString = $lowerbody[$typeKey];
                    break;
               
            }


            $htmlString .= "<tr><td>".$typeKeyString."</td><td>".$type."</td><td>".sprintf('%.2f', round((($type/$total)*100), 2))."%</td></tr>";
        }
        
    }
    // die;

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>簡易RMW-NFT分析工具</title>
    <!-- <script src="https://unpkg.com/vue@next"></script> -->
    <script src="js/jquery.min.js"></script>
</head>
<body>
    <style>
        table {
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
    </style>
    <?php 
        if ($jsonString != '') {
            ?>
            <table>
                <tr>
                    <td>總數</td>
                    <td><?php echo $total;?></td>
                    <td></td>
                </tr>
                <?php echo $htmlString;?>
            </table>
            
            <?php
        } else {
            ?>
            <form  method="post" enctype="multipart/form-data" id="jsonUpload">
                <input type="file" name="file" id="file" accept=".json" >
            </form>
            <button id="btn" onclick="upload()">上傳檔案</button>
            
            <?php
        }
    ?>
    
</body>
<script>
    function upload() {
        var jsonFile = $("#file").get(0).files[0];
        if (jsonFile == undefined) {
            alert("請選擇檔案");
        } else {
            $("#jsonUpload").submit();
        }
    }
</script>
</html>
