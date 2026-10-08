<?php
/** Review workflow: staff without publish_content submit pages for an editor to approve. */
function lanai_content_needs_review($sys)
{
    return !$sys->userHasCapability('publish_content');
}

/** Staff may change live pages only with edit_content or publish_content; owners otherwise edit unpublished work. */
function lanai_content_can_edit($sys, $fields)
{
    if (!$sys->userCanActOnContent($fields['userId'], 'edit_content')) return false;
    return $sys->userHasCapability('edit_content') || $sys->userHasCapability('publish_content') || ($fields['conActive'] ?? '') !== 'y';
}

/** Publishing needs publish_content itself; owning a page is not enough. */
function lanai_content_can_publish($sys, $fields)
{
    return $sys->userHasCapability('publish_content') && $sys->userCanActOnContent($fields['userId'], 'edit_content');
}

function lanai_content_can_delete($sys, $fields)
{
    if (!$sys->userCanActOnContent($fields['userId'], 'delete_content')) return false;
    return $sys->userHasCapability('delete_content') || $sys->userHasCapability('publish_content') || ($fields['conActive'] ?? '') !== 'y';
}

/** Allow-list HTML from authors who cannot publish, so a reviewer previewing it never runs their script. */
function lanai_content_sanitize($html)
{
    $html = (string)$html;
    if ($html === '') return '';
    $allowedTags = array_flip(array('p','br','hr','strong','b','em','i','u','s','strike','sub','sup','small','mark','blockquote','pre','code',
        'ul','ol','li','h1','h2','h3','h4','h5','h6','a','img','span','div','figure','figcaption','table','thead','tbody','tfoot','tr','th','td','caption'));
    $dropWithChildren = array_flip(array('script','style','iframe','frame','frameset','object','embed','applet','form','input','button','textarea','select','option',
        'link','meta','base','svg','math','audio','video','source','template','noscript'));
    $allowedAttributes = array('a' => array('href','title'), 'img' => array('src','alt','title','width','height'),
        'td' => array('colspan','rowspan'), 'th' => array('colspan','rowspan','scope'));
    $safeUrl = static function ($value, $schemes) {
        $compact = preg_replace('/[\x00-\x20\x7f]+/', '', (string)$value);
        if ($compact === '') return null;
        if (preg_match('#^([a-z][a-z0-9+.-]*):#i', $compact, $match)) return in_array(strtolower($match[1]), $schemes, true) ? $value : null;
        return $value;
    };
    $old = libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML('<?xml encoding="UTF-8"><div id="lanai-root">' . $html . '</div>', LIBXML_NONET | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    libxml_use_internal_errors($old);
    $root = $doc->getElementById('lanai-root');
    if (!$root) return '';
    $clean = function ($node) use (&$clean, $allowedTags, $dropWithChildren, $allowedAttributes, $safeUrl) {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child->nodeType === XML_COMMENT_NODE || $child->nodeType === XML_PI_NODE || $child->nodeType === XML_CDATA_SECTION_NODE) {
                $node->removeChild($child);
                continue;
            }
            if ($child->nodeType !== XML_ELEMENT_NODE) continue;
            $name = strtolower($child->nodeName);
            if (isset($dropWithChildren[$name])) { $node->removeChild($child); continue; }
            $clean($child);
            if (!isset($allowedTags[$name])) {
                while ($child->firstChild) $node->insertBefore($child->firstChild, $child);
                $node->removeChild($child);
                continue;
            }
            foreach (iterator_to_array($child->attributes) as $attribute) {
                $attributeName = strtolower($attribute->name);
                $keep = in_array($attributeName, $allowedAttributes[$name] ?? array(), true) || $attributeName === 'class';
                if ($keep && $attributeName === 'href' && $safeUrl($attribute->value, array('http','https','mailto','tel')) === null) $keep = false;
                if ($keep && $attributeName === 'src' && $safeUrl($attribute->value, array('http','https')) === null) $keep = false;
                if (!$keep) $child->removeAttributeNode($attribute);
            }
            if ($name === 'a' && $child->hasAttribute('href')) $child->setAttribute('rel', 'noopener noreferrer nofollow');
        }
    };
    $clean($root);
    $out = '';
    foreach ($root->childNodes as $child) $out .= $doc->saveHTML($child);
    return trim($out);
}

/** Validate content before writing or creating a navigation link. */
function lanai_content_values($post, $sanitize = false)
{
    $values = array();
    foreach (array('conTitle','conBody1','conBody2') as $field) {
        if (!isset($post[$field]) || !is_string($post[$field])) throw new InvalidArgumentException('Enter a title and page content.');
        $values[$field] = trim($post[$field]);
    }
    if ($sanitize) foreach (array('conBody1','conBody2') as $field) $values[$field] = lanai_content_sanitize($values[$field]);
    if ($values['conTitle'] === '' || mb_strlen($values['conTitle']) > 200) throw new InvalidArgumentException('Enter a title of up to 200 characters.');
    // TEXT columns are byte-limited. Reject oversized input rather than truncating it.
    foreach (array('conBody1','conBody2') as $field) if (strlen($values[$field]) > 65535) throw new InvalidArgumentException('The page text is too large. Use the media library for images instead of pasting image data.');
    $body = $values['conBody1'] . $values['conBody2'];
    $text = preg_replace('/[\s\x{00a0}\x{200b}]+/u', '', html_entity_decode(strip_tags($body), ENT_QUOTES, 'UTF-8'));
    if ($text === '' && !preg_match('/<(?:img|video|audio|iframe|object|embed)\b|data-lanai-embed\s*=/i', $body)) throw new InvalidArgumentException('Add page text, a poll or media before saving.');
    $values['conAllowComments'] = ($post['conAllowComments'] ?? '') === 'y' ? 'y' : 'n';
    $values['conMenu'] = ($post['conMenu'] ?? '') === 'yes' ? 'yes' : 'no';
    return $values;
}