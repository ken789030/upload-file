<?php

namespace App\Contollers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

include_once dirname(__DIR__, 2)."/config.php" ;


class Statistics 
{
    protected $robots;
    protected $total = 0;
    protected $htmlString;
    protected $statis = [
        "BaseBody" => [],
        "Head" => [],
        "Shoulder" => [],
        "Arms" => [],
        'LowerBody' => [],
        'MainColor' => [],
        'Rank' => []
    ];

    public function __construct($robots)
    {
        $this->robots = $robots;
        $this->statisRobots();
    }

    public function statisRobots() 
    {
        foreach ($this->robots as $key => $robot) {
            if ($robot['Lv'] == 5) {
                $this->total++;
                foreach ($robot as $name => $part) {
                    if ($name != 'Name' && $name != 'Random' && 
                        $name != 'filename' && $name != 'Lv') {
                        if (!$this->statis[$name][$part]) {
                            $this->statis[$name][$part] = 1;
                        } else {
                            $this->statis[$name][$part] += 1;
                        }
                    }
                }
            }
        }
    }

    public function keyToCht($key, $typeKey)
    {
        $typeKeyString = $typeKey;
        switch ($key) {
            case 'BaseBody':
                $typeKeyString = BASEBODY[$typeKey];
                break;
            case 'Head':
                $typeKeyString = HEAD[$typeKey];
                break;
            case 'Shoulder':
                $typeKeyString = SHOULDER[$typeKey];
                break;
            case 'Arms':
                $typeKeyString = ARMS[$typeKey];
                break;
            case 'LowerBody':
                $typeKeyString = LOWERBODY[$typeKey];
                break;
        }

        return $typeKeyString;
    }

    public function keyToEN($key, $typeKey)
    {
        $typeKeyString = $typeKey;
        switch ($key) {
            case 'BaseBody':
                $typeKeyString = BASEBODY_EN[$typeKey];
                break;
            case 'Head':
                $typeKeyString = HEAD_EN[$typeKey];
                break;
            case 'Shoulder':
                $typeKeyString = SHOULDER_EN[$typeKey];
                break;
            case 'Arms':
                $typeKeyString = ARMS_EN[$typeKey];
                break;
            case 'LowerBody':
                $typeKeyString = LOWERBODY_EN[$typeKey];
                break;
        }

        return $typeKeyString;
    }

    public function getHtmlString()
    {
        $htmlString = "";
        foreach ($this->statis as $key => $types) {
            $htmlString .= "<tr><td>".$key."種類</td><td>數量</td><td>機率</td></tr>";
            foreach ($types as $typeKey => $type) {
                $htmlString .= "<tr><td>".$this->keyToCht($key, $typeKey)."</td><td>".$type."</td><td>".sprintf('%.2f', round((($type/$this->total)*100), 2))."%</td></tr>";
            }
        }

        return $htmlString;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function exportExcel()
    {
        $spreadsheet = new Spreadsheet();

        // Set document properties
        $spreadsheet->getProperties()->setCreator('RMW')
            ->setLastModifiedBy('RMW');
        
        // Add some data
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', '總數')
            ->setCellValue('B1', $this->total);

        $count = 1;
        foreach ($this->statis as $key => $types) {
            $count++;
            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$count, $key.'種類')
            ->setCellValue('B'.$count, '數量')
            ->setCellValue('C'.$count, '機率');
            foreach ($types as $typeKey => $type) {
                $count++;
                $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A'.$count, $this->keyToCht($key, $typeKey))
                ->setCellValue('B'.$count, $type)
                ->setCellValue('C'.$count, sprintf('%.2f', round((($type/$this->total)*100), 2)).'%');
            }
        }
       

        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('Result');

        $spreadsheet->createSheet(1);
        $spreadsheet->setActiveSheetIndex(1);
        $page2Count = 1;
        $spreadsheet->setActiveSheetIndex(1)
                ->setCellValue('A'.$page2Count, 'Name')
                ->setCellValue('B'.$page2Count, 'BaseBody')
                ->setCellValue('C'.$page2Count, 'Head')
                ->setCellValue('D'.$page2Count, 'Shoulder')
                ->setCellValue('E'.$page2Count, 'Arms')
                ->setCellValue('F'.$page2Count, 'LowerBody')
                ->setCellValue('G'.$page2Count, 'MainColor')
                ->setCellValue('H'.$page2Count, 'Rank')
                ->setCellValue('I'.$page2Count, 'Random')
                ->setCellValue('J'.$page2Count, 'filename')
                ->setCellValue('K'.$page2Count, 'Lv');
        foreach ($this->robots as $robot) {
            if ($page2Count === 1) {
                $page2Count++;
            }
            $spreadsheet->setActiveSheetIndex(1)
                ->setCellValue('A'.$page2Count, $robot['Name'])
                ->setCellValue('B'.$page2Count, $this->keyToCht('BaseBody',$robot['BaseBody'])."(".$robot['BaseBody'].")")
                ->setCellValue('C'.$page2Count, $this->keyToCht('Head',$robot['Head'])."(".$robot['Head'].")")
                ->setCellValue('D'.$page2Count, $this->keyToCht('Shoulder',$robot['Shoulder'])."(".$robot['Shoulder'].")")
                ->setCellValue('E'.$page2Count, $this->keyToCht('Arms',$robot['Arms'])."(".$robot['Arms'].")")
                ->setCellValue('F'.$page2Count, $this->keyToCht('LowerBody',$robot['LowerBody'])."(".$robot['LowerBody'].")")
                ->setCellValue('G'.$page2Count, $this->keyToCht('MainColor',$robot['MainColor']))
                ->setCellValue('H'.$page2Count, $robot['Rank'])
                ->setCellValue('I'.$page2Count, $robot['Random'])
                ->setCellValue('J'.$page2Count, $robot['filename'])
                ->setCellValue('K'.$page2Count, $robot['Lv']);
            
            $page2Count++;
        }
        
        $spreadsheet->getActiveSheet()->setTitle('原始資料');
        

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="RMW-nft.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function exportJson()
    {
        $sortArrays = [];
        $description = 'test word-Lorem ipsum dolor sit amet, has adhuc homero contentiones ut, ut paulo errem ius. Ad sed exerci nonumy, nec omnes prompta partiendo at, ei duo nostro aliquip contentiones. Eu option vidisse fabulas vix, in usu fugit elitr deseruisse. Putant assentior persequeris vel ei. Veritus maiorum lobortis sit ea. Eros ferri definiebas mea id, lorem indoctum cum ei.-test word';

        foreach ($this->robots as $robot) {
            if (!$sortArrays[(intval($robot['Name']) - 1)]) {
                $sortArrays[intval($robot['Name']) - 1] = [];
            }
            
            $sortArrays[intval($robot['Name']) - 1][$robot['Lv'] - 1] = [
                'name' => 'Free Me#'.(intval($robot['Name']) - 1),
                'filename' => $robot['filename'].'.jpg',
                'attributes' => [
                    0 => [
                        'trait_type' => 'LEVEL',
                        'value' => 'Lv'.$robot['Lv']
                    ],
                    1 => [
                        'trait_type' => 'BaseBody',
                        'value' => $this->keyToEN('BaseBody',$robot['BaseBody'])
                    ],
                    2 => [
                        'trait_type' => 'Armor Color',
                        'value' => ($robot['Lv'] === 1)? "" :$robot['MainColor']
                    ],
                    3 => [
                        'trait_type' => 'HEAD & CHEST',
                        'value' => $this->keyToEN('Head',$robot['Head'])
                    ],
                    4 => [
                        'trait_type' => 'SHOULDER',
                        'value' => $this->keyToEN('Shoulder',$robot['Shoulder'])
                    ],
                    5 => [
                        'trait_type' => 'ARM',
                        'value' => $this->keyToEN('Arms',$robot['Arms'])
                    ],
                    6 => [
                        'trait_type' => 'LOWER BODY',
                        'value' => $this->keyToEN('LowerBody',$robot['LowerBody'])
                    ]
                ],
                'description' => $description
            ];
            
        }
        ksort($sortArrays);
        foreach ($sortArrays as $sort => $array) {
            ksort($sortArrays[$sort]);
        }
        // print_r(json_encode($sortArrays[0], JSON_PRETTY_PRINT));
        // die;
        header('Content-disposition: attachment; filename=rmwNFT.json');
        header('Content-Type: application/json; charset=utf-8');
        print_r(json_encode($sortArrays, JSON_PRETTY_PRINT));
        die;

    }
}
