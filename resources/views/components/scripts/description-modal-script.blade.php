@once
    @push('script')
        <script>
            $('body').on('click', '.read-more', function () {
                var fullDescription = $(this).data('description');
                var descriptionModalLabel = $(this).data('description-label');
                $('#fullDescription').html(fullDescription);
                $('#descriptionModalLabel').text(descriptionModalLabel);
                $('#descriptionModal').modal('show');
            });
        </script>
    @endpush
@endonce
