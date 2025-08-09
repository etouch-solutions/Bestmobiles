<?php
// popup_handler.php
if (isset($_GET['msg']) && $_GET['msg'] !== '') {
    $type = isset($_GET['error']) && $_GET['error'] == 1 ? 'error' : 'success';
    $msg  = htmlspecialchars($_GET['msg'], ENT_QUOTES);

    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '$type',
                title: '$msg',
                confirmButtonText: 'OK'
            }).then(() => {
                // Remove msg from URL so popup doesn't appear after refresh
                if (window.history.replaceState) {
                    const url = new URL(window.location.href);
                    url.searchParams.delete('msg');
                    url.searchParams.delete('error');
                    url.searchParams.delete('success');
                    window.history.replaceState({}, document.title, url.toString());
                }
            });
        });
    </script>
    ";
}
?>
