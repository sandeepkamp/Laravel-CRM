<!DOCTYPE html>
<html>
<head>
    <title>DocuSign Complete</title>
</head>
<body>
<script>
    // Notify the parent window
    if (window.opener) {
        window.opener.postMessage('docusign-complete', '*');
        window.close();
    } else if (window.parent) {
        window.parent.postMessage('docusign-complete', '*');
    }
</script>
</body>
</html>
