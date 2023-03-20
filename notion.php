<?php
session_start();
if(empty($_SESSION['admin']['userid']) && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
    exit;
}
header('Content-Type: application/javascript; charset=UTF-8');
$callback = $_GET['callback'];
require_once 'config.php';
require_once 'NotionAPI.class.php';
/**
 * Test表的test行的数列加1
 */
//筛选行
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
//获取到行所在page的id和内容
$result = $notion->post('databases', '4b820d3e060a4373917461830d3f3736', 'query', $params);
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
//echo '<pre>';print_r(array($result, $data));
echo $callback . '(' . json_encode($data) . ')';
//更新page的内容
//$params = array(
//    'properties' => array(
//        //指定列和值
//        '数' => array('number' => $result['data']['results'][0]['properties']['数']['number'] + 1),
//    ),
//);
//$result = $notion->patch('pages', $result['data']['results'][0]['id'], $params);
//print_r($result);

