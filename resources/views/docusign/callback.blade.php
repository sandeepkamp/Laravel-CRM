{{-- resources/views/docusign/callback.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>DocuSign Completed</title>
</head>
<body>
<script>
    // Notify parent window
    if (window.opener) {
        window.opener.postMessage('docusign-complete', '*');
        window.close();
    } else if (window.parent) {
        window.parent.postMessage('docusign-complete', '*');
    }
</script>
</body>
</html>
