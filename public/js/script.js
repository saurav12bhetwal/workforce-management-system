// public/js/script.js

$(document).ready(function() {
    // ===== SIDEBAR TOGGLE =====
    $('#sidebarToggle').on('click', function(e) {
        e.preventDefault();
        $('.sidebar').toggleClass('active');
        $('.main-content').toggleClass('shifted');
    });

    // ===== TOASTR NOTIFICATIONS =====
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    // ===== AUTO DISMISS ALERTS =====
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);

    // ===== CONFIRM DELETE =====
    $(document).on('click', '.delete-confirm', function(e) {
        if (!confirm('Are you sure you want to delete this item?')) {
            e.preventDefault();
            return false;
        }
        return true;
    });

    // ===== PASSWORD SHOW/HIDE =====
    $(document).on('click', '.toggle-password', function() {
        const input = $(this).closest('.input-group').find('input');
        const icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // ===== TOOLTIP INIT =====
    $('[data-bs-toggle="tooltip"]').tooltip();
});