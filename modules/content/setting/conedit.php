<?php
if (!defined('LANAI_ADMIN_REQUEST')) { http_response_code(403); exit; }
require_once dirname(__DIR__).'/module.php';
require_once dirname(__DIR__).'/editor.php';
$content = new Content();
$action = is_string($_POST['ac']??null) ? $_POST['ac'] : '';
if (($_SERVER['REQUEST_METHOD']??'')!=='POST' || !is_string($_POST['csrf_token']??null) || !$sys_lanai->validateCsrfToken('content',$_POST['csrf_token'])) {
    $sys_lanai->getErrorBox('Invalid request. Reload the editor and try again.'); return;
}
if (in_array($action,array('new','edit'),true)) {
    $contentAction = $action;
    $contentValues = array('conId'=>is_scalar($_POST['mid']??null)?(int)$_POST['mid']:0);
    foreach (array('conTitle','conBody1','conBody2','conAllowComments','conMenu') as $field) $contentValues[$field]=is_string($_POST[$field]??null)?$_POST[$field]:'';
    $contentNeedsReview = lanai_content_needs_review($sys_lanai);
    try {
        if ($action==='edit') {
            $record=$content->getContentById($contentValues['conId']);
            if (!$record || $record->EOF || !lanai_content_can_edit($sys_lanai,$record->fields)) {
                $sys_lanai->getErrorBox(_CONTENT_NO_PERMISSION); return;
            }
        } elseif (!$sys_lanai->userHasCapability('edit_content') && !$sys_lanai->userHasCapability('edit_own_content')) {
            $sys_lanai->getErrorBox(_CONTENT_NO_PERMISSION); return;
        }
        $values=lanai_content_values($_POST,$contentNeedsReview);
        if (!$content->ensureEditorSchema()) throw new RuntimeException('schema');
        if ($action==='new') {
            $id=$content->setNewContent($values['conTitle'],$values['conBody1'],$values['conBody2'],$values['conAllowComments'],$contentNeedsReview);
            if (!$id) throw new RuntimeException('save');
            $menuFailed=false; $pendingNow=$contentNeedsReview;
            // A menu link would point at a page the public cannot see yet, and menus are an administrator tool.
            if ($values['conMenu']==='yes' && !$contentNeedsReview && $sys_lanai->userHasCapability('manage_options')) {
                try { $menuFailed=!$content->setContentMenu($id,$values['conTitle']); }
                catch (Throwable $error) { $menuFailed=true; }
            }
        } else {
            $id=$contentValues['conId']; $menuFailed=false;
            $pendingNow=$contentNeedsReview && $record->fields['conActive']!=='y';
            if (!$content->setEditContent($id,$values['conTitle'],$values['conBody1'],$values['conBody2'],$values['conAllowComments'],$pendingNow)) throw new RuntimeException('save');
        }
        $sys_lanai->go2Page('setting.php?modname=content&mf=coneditform&mid='.(int)$id.'&saved=1'.($menuFailed?'&menu_failed=1':'').($pendingNow?'&review=1':''));
        return;
    } catch (InvalidArgumentException $error) {
        $contentError=$error->getMessage();
    } catch (Throwable $error) {
        $contentError='Your content could not be saved. Your text is still below. Check database access and retry; no menu was created for this failed save.';
    }
    include __DIR__.'/editor_form.php';
    return;
}
if (!in_array($action,array('active','mactive','mdelete'),true)) {
    $sys_lanai->getErrorBox('Unknown content action.'); return;
}
$ids=$action==='active'?array($_POST['mid']??0):($_POST['mid']??array());
if (!is_array($ids)) { $sys_lanai->getErrorBox('Select content first.'); return; }
try {
    if ($action!=='mdelete' && !$content->ensureEditorSchema()) throw new RuntimeException('schema');
    foreach ($ids as $id) {
        if (!is_scalar($id) || (int)$id<1) continue;
        $record=$content->getContentById((int)$id);
        $allowed=$record && !$record->EOF && ($action==='mdelete' ? lanai_content_can_delete($sys_lanai,$record->fields) : lanai_content_can_publish($sys_lanai,$record->fields));
        if (!$allowed) {
            $sys_lanai->getErrorBox(_CONTENT_NO_PERMISSION); return;
        }
        if ($action==='mdelete') $saved=$content->setDeleteContent((int)$id);
        else $saved=$content->setContentActive((int)$id,$action==='active'?($_POST['v']??'n'):($record->fields['conActive']==='y'?'n':'y'));
        if (!$saved) throw new RuntimeException('save');
    }
    $sys_lanai->go2Page('setting.php?modname=content');
} catch (Throwable $error) {
    $sys_lanai->getErrorBox('The content changes could not be saved. Please retry.');
}