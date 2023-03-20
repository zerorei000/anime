<?php
require_once 'Common.php';

$notion = new NotionAPI(NOTION_SECRET);
$params = array(
    'filter' => array(
        'and' => array (
            array(
                'property' => '今天更新',
                'checkbox' => array(
                    'equals' => true, //搜索行名
                ),
            ),
            array(
                'property' => '状态',
                'status' => array(
                    'equals' => '追番', //搜索行名
                ),
            ),
        ),
    ),
    'sorts' => array(
        array(
            'property' => '推荐度',
            'direction' => 'descending',
        ),
    )
);
$result = $notion->post('databases', NOTION_ANIME_DB_ID, 'query', $params);
$data = [];
foreach ($result['data']['results'] as $k => $row) {
    foreach ($row['properties'] as $field => $col) {
        switch ($col['type']) {
            case 'title':
                $data[$k][$field] = $col['title'][0]['plain_text'];
                break;
            default:
                !is_array($col[$col['type']]) && $data[$k][$field] = $col[$col['type']];
                break;
        }
    }
}
Tools::show(0, 'success', $data);
//更新page的内容
//$params = array(
//    'properties' => array(
//        //指定列和值
//        '数' => array('number' => $result['data']['results'][0]['properties']['数']['number'] + 1),
//    ),
//);
//$result = $notion->patch('pages', $result['data']['results'][0]['id'], $params);
//print_r($result);

