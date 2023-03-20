<?php
require_once 'Common.php';

$notion = new NotionAPI(NOTION_SECRET);
$params = array(
    'filter' => array(
        'and' => array(
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
            'property' => '更新',
            'direction' => 'ascending',
        ),
        array(
            'property' => '推荐度',
            'direction' => 'descending',
        ),
    )
);
$result = $notion->post('databases', NOTION_ANIME_DB_ID, 'query', $params);
$data = [];
foreach ($result['data']['results'] as $k => $row) {
    $data[$k]['译名'] = $notion->fieldShow($row['properties']['译名']);
    $data[$k]['进度'] = $notion->fieldShow($row['properties']['进度']);
    $data[$k]['编辑'] = $notion->fieldShow($row['properties']['最近编辑']);
    $data[$k]['操作'] = '<a href="javascript:;" class="btn btn-prev" data-old="' . $data[$k]['进度'] . '" data-id="' . $row['id'] . '">减一</a><div class="ONEREM"></div><a href="javascript:;" class="btn btn-next" data-old="' . $data[$k]['进度'] . '" data-id="' . $row['id'] . '">加一</a>';
    $data[$k]['推荐度'] = $notion->fieldShow($row['properties']['推荐度']);
    $data[$k]['更新'] = $notion->fieldShow($row['properties']['更新']);
    $data[$k]['特别篇'] = $notion->fieldShow($row['properties']['特别篇']);
    $data[$k]['编辑时间'] = date('Y-m-d H:i:s', strtotime($notion->fieldShow($row['properties']['编辑时间'])));
    $data[$k]['状态'] = $notion->fieldShow($row['properties']['状态']);
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

