<?php

use App\Contollers\Statistics;

$jsonString = '';
if (isset($_FILES) && $_FILES) {
    
    include_once dirname(__FILE__).'/vendor/autoload.php';
    include_once "app/Controllers/Statistics.php";

    $type = $_POST['type'];

    $jsonString = file_get_contents($_FILES['file']['tmp_name']);
    
    $robotList = json_decode($jsonString, true);

    $robots = $robotList['RobotList'];
    $statisController = new Statistics($robots);
    $htmlString = $statisController->getHtmlString();
    $total = $statisController->getTotal();
    if ($type == 'excel') {
        $statisController->exportExcel();
        die;
    }
    

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
                <select name="type" id="type">
                    <option value="result">顯示分析結果</option>
                    <option value="excel">匯出Excel</option>
                </select>
            </form>
            <br>
            <button id="btn" onclick="upload()">提交</button>
            
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
