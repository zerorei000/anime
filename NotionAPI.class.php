<?php

class NotionAPI
{
    private $url = 'https://api.notion.com/%s/%s%s%s';
    private $version;
    private $v;
    private $secret;
    const CONNECT_TIMEOUT = 3;
    const TRANSFER_TIMEOUT = 20;

    public function __construct($secret, $version = '2022-06-28', $v = 'v1')
    {
        $this->secret = $secret;
        $this->v = $v;
        $this->version = $version;
    }

    public function get($type, $id, $params = null)
    {
        $url = $this->getUrl($type, $id);
        echo $url;
        if ($params && is_array($params)) {
            foreach ($params as $paramKey => $paramValue) {
                $url .= '/' . $paramKey . '/' . $paramValue;
            }
        }
        $curl = $this->curlInit($url);
        $content = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return array(
            'code' => $code,
            'data' => json_decode($content, true),
        );
    }

    public function post($type, $id = '', $method = '', $params = null)
    {
        $url = $this->getUrl($type, $id, $method);
        $curl = $this->curlInit($url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        $content = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return array(
            'code' => $code,
            'data' => json_decode($content, true),
        );
    }

    public function patch($type, $id, $params = null)
    {
        $url = $this->getUrl($type, $id);
        $curl = $this->curlInit($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        $content = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return array(
            'code' => $code,
            'data' => json_decode($content, true),
        );
    }

    public function fieldShow($field)
    {
        $show = '';
        if (!isset($field['type'])) {
            return $show;
        }
        switch ($field['type']) {
            case 'title':
                $show = $field['title'][0]['plain_text'];
                break;
            default:
                if (!is_array($field[$field['type']]) && $field[$field['type']]) {
                    $show = $field[$field['type']];
                }
                break;
        }
        return $show;
    }

    private function getUrl($type, $id = '', $method = '')
    {
        return sprintf($this->url, $this->v, $type, '/' . $id, '/' . $method);
    }

    private function curlInit($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIMEOUT);
        curl_setopt($curl, CURLOPT_TIMEOUT, self::TRANSFER_TIMEOUT);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->secret,
            'Notion-Version: ' . $this->version,
            'Content-Type: application/json'
        ));
        return $curl;
    }
}
