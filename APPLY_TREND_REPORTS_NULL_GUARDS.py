from pathlib import Path

p = Path('/opt/lampp/htdocs/csv1_newcompetiscan/trend_reports.php')
s = p.read_text()

s = s.replace(
    '$row_country = $DRW->fetch_array($rs_country);\nif(isset($_REQUEST[\'audience_id\'])) {',
    '$row_country = $DRW->fetch_array($rs_country);\nif (!$row_country || !isset($row_country[0])) {\n\t$row_country = array(\'BOTH\');\n}\nif(isset($_REQUEST[\'audience_id\'])) {'
)

old_saved = "while($row1 = $DRW->fetch_row($savedQuery1)) {\n        if(strstr($row1['0'],',')){\n          $expl_sector=explode(',',$row1['0']);"
new_saved = "while($row1 = $DRW->fetch_row($savedQuery1)) {\n        if (!$row1 || !isset($row1[0])) {\n            continue;\n        }\n        if(strstr($row1[0],',')){\n          $expl_sector=explode(',',$row1[0]);"
s = s.replace(old_saved, new_saved)

old_recursive = "                    $row_country = $DRW->fetch_array($rs_country);\n                    if($row_country[0]=='US'){"
new_recursive = "                    $row_country = $DRW->fetch_array($rs_country);\n                    if (!$row_country || !isset($row_country[0])) {\n                        $row_country = array('BOTH');\n                    }\n                    if($row_country[0]=='US'){"
s = s.replace(old_recursive, new_recursive)

p.write_text(s)
