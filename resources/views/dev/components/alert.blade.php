@if (session('success'))
    <div class="alert alert-success" id="success-alert">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger" id="error-alert">{{ session('error') }}</div>
@endif

<script>
    setTimeout(function() {
        $("#success-alert").fadeOut(1000);
        $("#error-alert").fadeOut(1000);
    }, 3000);
</script>
