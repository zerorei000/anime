<?php
require_once 'Common.php';

$notion = new NotionAPI(NOTION_SECRET);
$action = Tools::getValue('action', 'list');
if ($action == 'edit') {
    $id = Tools::getValue('id');
    $number = Tools::getValueInt('number');
    $params = array(
        'properties' => array(
            '编辑' => array('number' => $number),
        ),
    );
    $result = $notion->patch('pages', $id, $params);
    Tools::show(0, 'success', $result);
} else {
    $weekdays = array('太阳','月亮','火星','水星','木星','金星','土星');
    $weekday = date('w');
    $params = array(
        'filter' => array(
            'and' => array(
                array(
                    'property' => '更新',
                    'select' => array(
                        'equals' => $weekdays[date('w')],
                    ),
                ),
                array(
                    'property' => '状态',
                    'status' => array(
                        'equals' => '追番',
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
    $content = [];
    foreach ($result['data']['results'] as $k => $row) {
        $content[$k]['译名'] = $notion->fieldShow($row['properties']['译名']);
        $content[$k]['进度'] = $notion->fieldShow($row['properties']['进度']);
        $content[$k]['编辑'] = $notion->fieldShow($row['properties']['最近编辑']);
        $content[$k]['操作'] = '<a href="javascript:void(0)" class="btn btn-prev" data-old="' . $content[$k]['进度'] . '" data-id="' . $row['id'] . '">减一</a>&nbsp;&nbsp;<a href="javascript:void(0)" class="btn btn-next" data-old="' . $content[$k]['进度'] . '" data-id="' . $row['id'] . '">加一</a>';
        $content[$k]['推荐度'] = $notion->fieldShow($row['properties']['推荐度']);
        $content[$k]['更新'] = $notion->fieldShow($row['properties']['更新']);
        $content[$k]['特别篇'] = $notion->fieldShow($row['properties']['特别篇']);
        $content[$k]['编辑时间'] = date('Y-m-d H:i:s', strtotime($notion->fieldShow($row['properties']['编辑时间'])));
        $content[$k]['状态'] = $notion->fieldShow($row['properties']['状态']);
    }
    $data = [];
    $data['filter']['weekday'] = $weekday;
    $data['content'] = $content;
    Tools::show(0, 'success', $data);
}

