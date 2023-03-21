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
    $weekDays = array('太阳','月亮','火星','水星','木星','金星','土星');
    $params = array(
        'filter' => array(
            'and' => array(
                array(
                    'property' => '更新',
                    'checkbox' => array(
                        'equals' => $weekDays[date('w')],
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
    $data = [];
    foreach ($result['data']['results'] as $k => $row) {
        $data[$k]['译名'] = $notion->fieldShow($row['properties']['译名']);
        $data[$k]['进度'] = $notion->fieldShow($row['properties']['进度']);
        $data[$k]['编辑'] = $notion->fieldShow($row['properties']['最近编辑']);
        $data[$k]['操作'] = '<a href="javascript:;" class="btn btn-prev" data-old="' . $data[$k]['进度'] . '" data-id="' . $row['id'] . '">减一</a>&nbsp;&nbsp;<a href="javascript:;" class="btn btn-next" data-old="' . $data[$k]['进度'] . '" data-id="' . $row['id'] . '">加一</a>';
        $data[$k]['推荐度'] = $notion->fieldShow($row['properties']['推荐度']);
        $data[$k]['更新'] = $notion->fieldShow($row['properties']['更新']);
        $data[$k]['特别篇'] = $notion->fieldShow($row['properties']['特别篇']);
        $data[$k]['编辑时间'] = date('Y-m-d H:i:s', strtotime($notion->fieldShow($row['properties']['编辑时间'])));
        $data[$k]['状态'] = $notion->fieldShow($row['properties']['状态']);
    }
    Tools::show(0, 'success', $data);
}

