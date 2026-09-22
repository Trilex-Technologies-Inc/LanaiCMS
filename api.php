<?php

/**
 * Lightweight REST API.
 *
 * Routes (see htaccess.sample.txt for the pretty-URL rewrites):
 *   GET  /api.php?resource=news[&id=NWSID]
 *   GET  /api.php?resource=content[&id=CONID]
 *   GET  /api.php?resource=ctype[&id=CTP_SLUG[&sub=ITEM_SLUG]]
 *   POST/PUT/DELETE /api.php?resource=ctype&id=CTP_SLUG[&sub=ITEM_SLUG]  (Bearer token required)
 *
 * Read endpoints are public (mirrors what's already visible on the public site).
 * Write endpoints require "Authorization: Bearer <token>" issued via the
 * apitoken admin module, and enforce the same roles/capabilities used by
 * the admin screens (see Systems::userCanActOnContent()).
 */

// Suppress any stray HTML the legacy bootstrap might emit (warnings, security
// notices) so the response body stays pure JSON.
ob_start();
include_once('setconfig.inc.php');
ob_end_clean();

include_once('modules/news/module.php');
include_once('modules/content/module.php');
include_once('modules/ctype/module.php');
include_once('modules/apitoken/module.php');

header('Content-Type: application/json; charset=utf-8');

function api_respond($data, $status = 200)
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if (session_status() === PHP_SESSION_ACTIVE) {
        // Avoid persisting API-authenticated uid into a browser session.
        session_abort();
    }
    exit;
}

function api_error($message, $status = 400)
{
    api_respond(array('error' => $message), $status);
}

function api_bearer_token()
{
    $header = '';
    if (function_exists('getallheaders')) {
        foreach (getallheaders() as $k => $v) {
            if (strcasecmp($k, 'Authorization') === 0) {
                $header = $v;
                break;
            }
        }
    }
    if ($header === '' && isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $header = $_SERVER['HTTP_AUTHORIZATION'];
    }
    if (!preg_match('/^Bearer\s+(.+)$/i', trim($header), $m)) {
        return '';
    }
    return trim($m[1]);
}

/**
 * Require a valid bearer token and stash the resolved userId into the PHP
 * session so existing model code (which reads $_SESSION['uid']) works as-is.
 */
function api_require_auth()
{
    $userId = ApiToken::authenticate(api_bearer_token());
    if ($userId === false) {
        api_error('Missing or invalid API token', 401);
    }
    $_SESSION['uid'] = $userId;
    return $userId;
}

function api_pagination()
{
    $limit = isset($_REQUEST['limit']) ? (int) $_REQUEST['limit'] : 20;
    $limit = max(1, min(100, $limit));
    $offset = isset($_REQUEST['offset']) ? max(0, (int) $_REQUEST['offset']) : 0;
    return array($limit, $offset);
}

function api_json_body()
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : array();
}

$resource = isset($_REQUEST['resource']) ? trim($_REQUEST['resource']) : '';
$method = $_SERVER['REQUEST_METHOD'];

switch ($resource) {

    case 'news':
        $news = new News();
        if (!empty($_REQUEST['id'])) {
            $rs = $news->getNewsById($_REQUEST['id']);
            if ($rs->recordcount() < 1 || $rs->fields['nwsActive'] !== 'y') {
                api_error('News item not found', 404);
            }
            api_respond(array(
                'id' => (int) $rs->fields['nwsId'],
                'title' => $rs->fields['nwsTitle'],
                'preface' => $rs->fields['nwsPreface'],
                'body' => $rs->fields['nwsBody'],
                'created' => $rs->fields['nwsCreate'],
            ));
        }
        list($limit, $offset) = api_pagination();
        global $db, $cfg;
        $sql = "SELECT * FROM " . $cfg['tablepre'] . "news WHERE nwsActive='y' ORDER BY nwsCreate DESC";
        $rs = $db->SelectLimit($sql, $limit, $offset);
        $items = array();
        while ($rs && !$rs->EOF) {
            $items[] = array(
                'id' => (int) $rs->fields['nwsId'],
                'title' => $rs->fields['nwsTitle'],
                'preface' => $rs->fields['nwsPreface'],
                'created' => $rs->fields['nwsCreate'],
            );
            $rs->movenext();
        }
        api_respond(array('items' => $items, 'limit' => $limit, 'offset' => $offset));
        break;

    case 'content':
        $content = new Content();
        if (!empty($_REQUEST['id'])) {
            $rs = $content->getContentById($_REQUEST['id']);
            if ($rs->recordcount() < 1 || $rs->fields['conActive'] !== 'y') {
                api_error('Content not found', 404);
            }
            api_respond(array(
                'id' => (int) $rs->fields['conId'],
                'title' => $rs->fields['conTitle'],
                'body1' => $rs->fields['conBody1'],
                'body2' => $rs->fields['conBody2'],
            ));
        }
        list($limit, $offset) = api_pagination();
        global $db, $cfg;
        $sql = "SELECT * FROM " . $cfg['tablepre'] . "content WHERE conActive='y' ORDER BY conId DESC";
        $rs = $db->SelectLimit($sql, $limit, $offset);
        $items = array();
        while ($rs && !$rs->EOF) {
            $items[] = array(
                'id' => (int) $rs->fields['conId'],
                'title' => $rs->fields['conTitle'],
            );
            $rs->movenext();
        }
        api_respond(array('items' => $items, 'limit' => $limit, 'offset' => $offset));
        break;

    case 'ctype':
        $ctype = new ContentType();
        $ctpSlug = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : '';
        $itemSlug = isset($_REQUEST['sub']) ? trim($_REQUEST['sub']) : '';

        if ($ctpSlug === '') {
            // list content types
            $rs = $ctype->getTypes();
            $types = array();
            while (!$rs->EOF) {
                if ($rs->fields['ctpActive'] === 'y') {
                    $types[] = array('slug' => $rs->fields['ctpSlug'], 'title' => $rs->fields['ctpTitle']);
                }
                $rs->movenext();
            }
            api_respond(array('types' => $types));
        }

        $typeRs = $ctype->getTypeBySlug($ctpSlug);
        if ($typeRs->recordcount() < 1) {
            api_error('Content type not found', 404);
        }
        $ctpId = $typeRs->fields['ctpId'];

        if ($method === 'POST' && $itemSlug === '') {
            api_require_auth();
            if (!$sys_lanai->userHasCapability('edit_content') && !$sys_lanai->userHasCapability('edit_own_content')) {
                api_error('Missing edit_content/edit_own_content capability', 403);
            }
            $body = api_json_body();
            if (empty($body['title'])) {
                api_error('title is required');
            }
            $values = array();
            if (!empty($body['fields']) && is_array($body['fields'])) {
                foreach ($body['fields'] as $fieldName => $value) {
                    $fieldRs = $ctype->getFieldByName($ctpId, $fieldName);
                    if ($fieldRs->recordcount() > 0) {
                        $values[$fieldRs->fields['cfdId']] = $value;
                    }
                }
            }
            $citId = $ctype->setSaveItem(null, $ctpId, $body['title'], $values);
            api_respond(array('id' => $citId), 201);
        }

        if ($itemSlug === '') {
            // list items for the type
            list($limit, $offset) = api_pagination();
            global $db, $cfg;
            $sql = "SELECT * FROM " . $cfg['tablepre'] . "citem WHERE ctpId=" . intval($ctpId) . " AND citActive='y' ORDER BY citCreated DESC";
            $rs = $db->SelectLimit($sql, $limit, $offset);
            $items = array();
            while ($rs && !$rs->EOF) {
                $items[] = array('slug' => $rs->fields['citSlug'], 'title' => $rs->fields['citTitle']);
                $rs->movenext();
            }
            api_respond(array('items' => $items, 'limit' => $limit, 'offset' => $offset));
        }

$itemRs = $ctype->getItemBySlug($ctpId, $itemSlug);
if ($itemRs->recordcount() < 1) {
    api_error('Item not found', 404);
}

        if ($method === 'PUT' || $method === 'PATCH') {
            api_require_auth();
            $existing = $ctype->getItemById($itemRs->fields['citId']);
            if ($existing->recordcount() < 1 || !$sys_lanai->userCanActOnContent($existing->fields['userId'], 'edit_content')) {
                api_error('Forbidden', 403);
            }
            $body = api_json_body();
            $title = !empty($body['title']) ? $body['title'] : $existing->fields['citTitle'];
            $values = array();
            if (!empty($body['fields']) && is_array($body['fields'])) {
                foreach ($body['fields'] as $fieldName => $value) {
                    $fieldRs = $ctype->getFieldByName($ctpId, $fieldName);
                    if ($fieldRs->recordcount() > 0) {
                        $values[$fieldRs->fields['cfdId']] = $value;
                    }
                }
            }
            $ctype->setSaveItem($existing->fields['citId'], $ctpId, $title, $values);
            api_respond(array('id' => (int) $existing->fields['citId']));
        }

        if ($method === 'DELETE') {
            api_require_auth();
            $existing = $ctype->getItemById($itemRs->fields['citId']);
            if ($existing->recordcount() < 1 || !$sys_lanai->userCanActOnContent($existing->fields['userId'], 'delete_content')) {
                api_error('Forbidden', 403);
            }
            $ctype->setDeleteItem($existing->fields['citId']);
            api_respond(array('deleted' => true));
        }

        // GET single item detail, with flexible field values
        $values = array();
        $rsv = $ctype->getItemValues($itemRs->fields['citId']);
        while (!$rsv->EOF) {
            $values[$rsv->fields['cfdName']] = ($rsv->fields['cfdType'] === 'number') ? $rsv->fields['cvalNumber'] : $rsv->fields['cvalText'];
            $rsv->movenext();
        }
        api_respond(array(
            'slug' => $itemRs->fields['citSlug'],
            'title' => $itemRs->fields['citTitle'],
            'fields' => $values,
        ));
        break;

    default:
        api_error('Unknown resource', 404);
}
?>
