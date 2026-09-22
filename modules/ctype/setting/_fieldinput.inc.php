<?php
// Renders one form input for a flexible field row (from getFields()/getItemValues()).
// Expects $f (assoc array with cfdId, cfdLabel, cfdType, cfdOptions, cfdRequired) and $value.
function ctype_render_field_input($f, $value)
{
    $name = 'cfd_' . $f['cfdId'];
    $required = ($f['cfdRequired'] === 'y') ? '*' : '';
    echo '<tr><td>' . htmlspecialchars($f['cfdLabel']) . '</td><td>';

    switch ($f['cfdType']) {
        case 'textarea':
        case 'richtext':
            echo '<textarea name="' . $name . '" rows="5" cols="40"' . ($f['cfdType'] === 'richtext' ? ' class="tinymce"' : '') . '>' . htmlspecialchars((string) $value) . '</textarea>';
            break;
        case 'number':
            echo '<input type="number" step="any" name="' . $name . '" value="' . htmlspecialchars((string) $value) . '">';
            break;
        case 'date':
            echo '<input type="date" name="' . $name . '" value="' . htmlspecialchars((string) $value) . '">';
            break;
        case 'checkbox':
            echo '<input type="checkbox" name="' . $name . '" value="1"' . ($value == '1' ? ' checked' : '') . '>';
            break;
        case 'select':
            $options = array_filter(array_map('trim', explode(',', (string) $f['cfdOptions'])));
            echo '<select name="' . $name . '">';
            foreach ($options as $opt) {
                echo '<option value="' . htmlspecialchars($opt) . '"' . ($opt === (string) $value ? ' selected' : '') . '>' . htmlspecialchars($opt) . '</option>';
            }
            echo '</select>';
            break;
        case 'image':
        case 'file':
            // keep the existing stored path unless a new upload replaces it
            echo '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars((string) $value) . '">';
            if ($f['cfdType'] === 'image' && (string) $value !== '') {
                echo '<div class="mb-2"><img src="' . htmlspecialchars((string) $value) . '" style="max-height:80px;"></div>';
            } elseif ((string) $value !== '') {
                echo '<div class="mb-2"><a href="' . htmlspecialchars((string) $value) . '" target="_blank">' . htmlspecialchars(basename((string) $value)) . '</a></div>';
            }
            echo '<input type="file" name="cfd_upload_' . $f['cfdId'] . '">';
            break;
        default: // text
            echo '<input type="text" name="' . $name . '" size="40" value="' . htmlspecialchars((string) $value) . '">';
            break;
    }

    echo ' ' . $required . '</td></tr>';
}
?>
