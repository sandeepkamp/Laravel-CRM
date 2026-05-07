$(document).ready(function () {
    $('form.prevent-multiple-submits').on('submit', function () {
        const $form = $(this);
        const $submitBtn = $form.find('button[type="submit"], input[type="submit"]');
        $submitBtn.prop('disabled', true);
    });

    // let hasError = @json(session()->has('error') || $errors->any());

    // if (hasError) {
    //     const $form = $('form.prevent-multiple-submits');
    //     const $submitBtn = $form.find('button[type="submit"], input[type="submit"]');
    //     $submitBtn.prop('disabled', false);
    // }
});
